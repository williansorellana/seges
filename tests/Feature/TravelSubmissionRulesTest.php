<?php

namespace Tests\Feature;

use App\Models\PlanningException;
use App\Models\Rendition;
use App\Models\RoutePlanning;
use App\Models\User;
use App\Services\PlanningEligibilityService;
use App\Services\RenditionDeadlineService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TravelSubmissionRulesTest extends TestCase
{
    protected User $worker;

    protected User $boss;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.key' => 'base64:'.base64_encode(str_repeat('t', 32))]);
        // Only an ephemeral SQLite connection: never migrate or truncate a live DB.
        $this->assertSame('sqlite', DB::connection()->getDriverName());
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        Carbon::setTestNow(Carbon::parse('2026-10-09 12:00:00', 'America/Santiago'));
        Notification::fake();
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            foreach (['name', 'last_name', 'email', 'password', 'role'] as $column) {
                $table->string($column);
            }
            $table->string('rut')->nullable();
            $table->string('departamento')->nullable();
            $table->unsignedBigInteger('jefatura_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('must_change_password')->default(false);
            $table->json('authorized_modules')->nullable();
            $table->string('profile_photo_path')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
        foreach (glob(database_path('migrations/*.php')) as $file) {
            if (preg_match('/route_planning|rendition|workflow|digital_signature|rut_cost_center|travel_submission_rules|audit_locks/', basename($file))) {
                (require $file)->up();
            }
        }
        $this->boss = $this->user('jefatura');
        $this->worker = $this->user('worker', ['jefatura_id' => $this->boss->id]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    protected function user(string $role, array $extra = []): User
    {
        return User::create(array_merge(['name' => 'Prueba', 'last_name' => 'Reglas',
            'email' => uniqid().'@example.test', 'password' => 'test-password', 'role' => $role,
            'authorized_modules' => ['renditions'], 'is_active' => true, 'must_change_password' => false], $extra));
    }

    protected function payload(): array
    {
        return ['trip_type' => 'terreno', 'motive' => 'Visita', 'destination' => 'Santiago',
            'start_date' => '2026-10-10', 'end_date' => '2026-10-13'];
    }

    protected function rendition(array $extra = [], string $end = '2026-10-01'): Rendition
    {
        $planning = RoutePlanning::create(['user_id' => $this->worker->id, 'trip_type' => 'terreno',
            'motive' => 'Visita', 'destination' => 'Santiago', 'start_date' => '2026-10-01',
            'end_date' => $end, 'status' => 'approved', 'requested_on' => '2026-10-01']);

        return Rendition::create(array_merge(['route_planning_id' => $planning->id,
            'user_id' => $this->worker->id, 'status' => 'draft', 'deadline_at' => '2026-10-08'], $extra));
    }

    protected function receipt(Rendition $rendition, array $extra = []): void
    {
        $rendition->expenses()->create(array_merge(['date' => '2026-10-01', 'provider' => 'Proveedor',
            'document_type' => 'factura', 'expense_category' => 'bencina', 'amount' => 5000,
            'attachment_path' => 'receipts/test.pdf'], $extra));
    }

    public function test_calendar_excludes_holidays_weekends_and_handles_year_boundary(): void
    {
        $service = app(RenditionDeadlineService::class);
        $this->assertSame('2026-10-19', $service->deadlineFor('2026-10-09')->toDateString());
        $this->assertSame('2027-01-07', $service->deadlineFor('2026-12-30')->toDateString());
        $this->assertFalse($service->isExpired('2026-10-09', Carbon::parse('2026-10-19')));
        $this->assertTrue($service->isExpired('2026-10-09', Carbon::parse('2026-10-20')));
    }

    public function test_two_daily_requests_then_authorized_third_and_no_fourth(): void
    {
        $this->actingAs($this->worker);
        $this->post(route('route-plannings.store'), $this->payload())->assertRedirect(route('route-plannings.index'))->assertSessionHasNoErrors();
        $this->assertSame(1, RoutePlanning::count());
        $this->post(route('route-plannings.store'), $this->payload())->assertRedirect(route('route-plannings.index'))->assertSessionHasNoErrors();
        $this->post(route('route-plannings.store'), $this->payload())->assertSessionHasErrors('planning');
        $this->assertSame(2, RoutePlanning::count());
        $this->post(route('route-plannings.exceptions.store'), ['type' => 'daily_extra', 'reason' => 'Necesitamos atender otro cliente'])->assertSessionHasNoErrors();
        $exception = PlanningException::firstOrFail();
        $otherBoss = $this->user('jefatura');
        $this->actingAs($otherBoss)->post(route('route-plannings.exceptions.decide', $exception),
            ['decision' => 'approved', 'reason' => 'Urgencia comercial confirmada'])->assertForbidden();
        $this->actingAs($this->boss)->post(route('route-plannings.exceptions.decide', $exception),
            ['decision' => 'approved', 'reason' => 'Urgencia comercial confirmada'])->assertSessionHasNoErrors();
        $this->actingAs($this->worker)->post(route('route-plannings.store'), $this->payload())->assertSessionHasNoErrors();
        $this->assertNotNull($exception->fresh()->used_at);
        $this->assertDatabaseHas('workflow_histories', ['action' => 'planning_exception_used']);
        $this->post(route('route-plannings.store'), $this->payload())->assertSessionHasErrors('planning');
        $this->assertSame(3, RoutePlanning::count());
    }

    public function test_late_single_receipt_submission_unblocks_without_waiting_for_approval(): void
    {
        $rendition = $this->rendition();
        $this->receipt($rendition);
        $this->actingAs($this->worker)->post(route('route-plannings.store'), $this->payload())->assertSessionHasErrors('planning');
        $this->post(route('renditions.submit', $rendition))->assertSessionHasNoErrors();
        $rendition->refresh();
        $this->assertSame('pending_jefatura', $rendition->status);
        $this->assertNotNull($rendition->first_submitted_at);
        $this->assertSame('2026-10-08', $rendition->deadline_at->toDateString());
        $this->post(route('route-plannings.store'), $this->payload())->assertSessionHasNoErrors();
    }

    public function test_travel_draft_accepts_photo_but_cannot_be_sent_before_end(): void
    {
        Storage::fake('local');
        $rendition = $this->rendition(['deadline_at' => '2026-10-19'], '2026-10-13');
        $this->actingAs($this->worker)->post(route('renditions.expenses.store', $rendition), [
            'date' => '2026-10-09', 'provider' => 'Proveedor', 'document_type' => 'boleta',
            'expense_category' => 'peaje', 'amount' => 2000, 'justification' => 'Peaje del viaje',
            'attachment' => UploadedFile::fake()->image('boleta.jpg'),
        ])->assertSessionHasNoErrors();
        $expense = $rendition->expenses()->firstOrFail();
        Storage::disk('local')->assertExists($expense->attachment_path);
        $this->post(route('renditions.submit', $rendition))->assertSessionHasErrors('expenses');
        $this->assertSame('draft', $rendition->fresh()->status);
        $this->get(route('renditions.show', $rendition))->assertOk()->assertSee('Borrador de gastos del viaje')->assertDontSee('Terminar y Enviar');
    }

    public function test_end_date_allows_official_submission(): void
    {
        $rendition = $this->rendition(['deadline_at' => '2026-10-19'], '2026-10-09');
        $this->receipt($rendition);
        $this->actingAs($this->worker)->post(route('renditions.submit', $rendition))->assertSessionHasNoErrors();
        $this->assertSame('pending_jefatura', $rendition->fresh()->status);
    }

    public function test_submission_requires_valid_active_assigned_boss(): void
    {
        $rendition = $this->rendition();
        $this->receipt($rendition);
        $this->worker->update(['jefatura_id' => null]);
        $this->actingAs($this->worker)->post(route('renditions.submit', $rendition))->assertSessionHasErrors('expenses');
        $this->worker->update(['jefatura_id' => $this->boss->id]);
        $this->boss->update(['is_active' => false]);
        $this->post(route('renditions.submit', $rendition))->assertSessionHasErrors('expenses');
        $this->assertSame('draft', $rendition->fresh()->status);
    }

    public function test_rejection_closes_attempt_and_requires_a_new_rendition(): void
    {
        $rendition = $this->rendition();
        $this->receipt($rendition);
        $this->actingAs($this->worker)->post(route('renditions.submit', $rendition))->assertSessionHasNoErrors();
        $first = $rendition->fresh()->first_submitted_at->toDateTimeString();
        $this->actingAs($this->boss)->post(route('renditions.reject-jefatura-rendition', $rendition),
            ['observation' => 'Corregir información del proveedor'])->assertSessionHasNoErrors();
        $this->assertNull($rendition->fresh()->correction_deadline_at);
        $this->assertNotNull($rendition->fresh()->closed_at);
        $this->assertCount(1, app(PlanningEligibilityService::class)->overdue($this->worker));
        $this->actingAs($this->worker)->post(route('renditions.submit', $rendition))->assertForbidden();
        $this->post(route('renditions.new-attempt', $rendition))->assertRedirect();
        $next = $rendition->replacement()->firstOrFail();
        $this->assertSame(2, $next->attempt_number);
        $this->assertSame($rendition->fresh()->funds_received, $next->funds_received);
        $this->assertSame(0, $next->expenses()->count());
        $this->receipt($next);
        $this->post(route('renditions.submit', $next))->assertSessionHasNoErrors();
        $this->assertSame($first, $rendition->fresh()->first_submitted_at->toDateTimeString());
        $this->assertCount(0, app(PlanningEligibilityService::class)->overdue($this->worker));
    }

    public function test_all_overdue_renditions_must_be_submitted(): void
    {
        $one = $this->rendition();
        $two = $this->rendition();
        $this->receipt($one);
        $this->receipt($two);
        $this->actingAs($this->worker)->post(route('renditions.submit', $one))->assertSessionHasNoErrors();
        $this->post(route('route-plannings.store'), $this->payload())->assertSessionHasErrors('planning');
        $this->post(route('renditions.submit', $two))->assertSessionHasNoErrors();
        $this->post(route('route-plannings.store'), $this->payload())->assertSessionHasNoErrors();
    }

    public function test_temporary_unlock_is_single_use_and_does_not_erase_overdue_debt(): void
    {
        $this->rendition();
        $this->actingAs($this->worker)->post(route('route-plannings.exceptions.store'),
            ['type' => 'temporary_unlock', 'reason' => 'Urgencia operativa con cliente'])->assertSessionHasNoErrors();
        $exception = PlanningException::firstOrFail();
        $this->actingAs($this->boss)->post(route('route-plannings.exceptions.decide', $exception),
            ['decision' => 'approved', 'reason' => 'Autorizar una salida urgente hoy'])->assertSessionHasNoErrors();
        $this->actingAs($this->worker)->post(route('route-plannings.store'), $this->payload())->assertSessionHasNoErrors();
        $this->post(route('route-plannings.store'), $this->payload())->assertSessionHasErrors('planning');
        $this->assertCount(1, app(PlanningEligibilityService::class)->overdue($this->worker));
    }

    public function test_yesterdays_exception_cannot_be_used_or_approved(): void
    {
        $this->rendition();
        $exception = PlanningException::create(['user_id' => $this->worker->id, 'type' => 'temporary_unlock',
            'valid_on' => '2026-10-08', 'request_reason' => 'Urgencia comercial', 'status' => 'pending']);
        $this->actingAs($this->boss)->post(route('route-plannings.exceptions.decide', $exception),
            ['decision' => 'approved', 'reason' => 'Autorización excepcional'])->assertStatus(422);
        $exception->update(['status' => 'approved']);
        $this->actingAs($this->worker)->post(route('route-plannings.store'), $this->payload())->assertSessionHasErrors('planning');
    }

    public function test_empty_or_observed_receipt_cannot_be_submitted(): void
    {
        $rendition = $this->rendition();
        $this->actingAs($this->worker)->post(route('renditions.submit', $rendition))->assertSessionHasErrors('expenses');
        $this->receipt($rendition, ['is_valid' => false, 'rejection_reason' => 'Documento ilegible']);
        $this->post(route('renditions.submit', $rendition))->assertSessionHasErrors('expenses');
        $this->assertSame(0, $rendition->digitalSignatures()->count());
    }

    public function test_notices_and_calendar_permissions(): void
    {
        $rendition = $this->rendition();
        $this->actingAs($this->worker)->get(route('route-plannings.index'))->assertOk()->assertSee('Regularizar rendición');
        $this->get(route('renditions.show', $rendition))->assertOk()->assertSee('Fuera de plazo');
        $this->get(route('renditions.calendar'))->assertForbidden();
        $admin = $this->user('admin');
        $this->actingAs($admin)->get(route('renditions.calendar'))->assertOk()->assertSee('2027');
        $this->post(route('renditions.calendar.store'), ['date' => '2026-10-13', 'name' => 'Día no laborable de la empresa'])->assertSessionHasNoErrors();
        $this->assertSame('2026-10-20', app(RenditionDeadlineService::class)->deadlineFor('2026-10-09')->toDateString());
        $this->assertSame('2026-10-08', $rendition->fresh()->deadline_at->toDateString());
        $this->post(route('renditions.calendar.confirm'), ['year' => 2028])->assertSessionHasErrors('reviewed');
    }

    public function test_rollout_recovers_history_and_preserves_published_deadline(): void
    {
        $rendition = $this->rendition();
        DB::table('workflow_histories')->insert([
            'workflowable_type' => Rendition::class, 'workflowable_id' => $rendition->id,
            'user_id' => $this->worker->id, 'action' => 'submitted_by_worker',
            'from_status' => 'draft', 'to_status' => 'pending_jefatura',
            'created_at' => '2026-10-05 12:00:00', 'updated_at' => '2026-10-05 12:00:00',
        ]);
        $migration = require database_path('migrations/2026_10_09_000001_add_travel_submission_rules.php');
        $migration->down();
        $migration->up();
        $rendition->refresh();
        $this->assertSame('2026-10-05 12:00:00', $rendition->first_submitted_at->toDateTimeString());
        $this->assertSame('2026-10-08', $rendition->deadline_at->toDateString());
        $this->assertNull($rendition->correction_deadline_at);
    }

    public function test_financial_roles_can_enter_dashboard(): void
    {
        $this->mock(\App\Http\Controllers\DashboardController::class)
            ->shouldReceive('index')->twice()->andReturn(response('Inicio'));
        foreach (['controlling', 'finances'] as $role) {
            $this->actingAs($this->user($role))->get(route('dashboard'))->assertOk();
        }
    }

    public function test_unrelated_boss_cannot_approve_or_reject_a_planning(): void
    {
        $this->actingAs($this->worker)->post(route('route-plannings.store'), $this->payload())->assertSessionHasNoErrors();
        $planning = RoutePlanning::firstOrFail();
        $this->actingAs($this->user('jefatura'))->post(route('route-plannings.approve-jefatura', $planning))->assertForbidden();
        $this->post(route('route-plannings.reject-jefatura', $planning), ['observation' => 'Rechazo no autorizado'])->assertForbidden();
    }

    public function test_catalog_import_and_assignment_preserve_historical_center(): void
    {
        $this->assertSame(21, \App\Models\CostCenter::count());
        $this->assertSame(1, \App\Models\CostCenter::where('code', 'C113000001')->count());
        $this->assertSame(0, \App\Models\RutCostCenter::count());
        $this->worker->update(['rut' => '12.345.678-5']);
        $finance = $this->user('finances');
        $centers = \App\Models\CostCenter::take(2)->get();
        $this->actingAs($this->worker)->get(route('cost-centers.index'))->assertForbidden();
        $this->actingAs($finance)->get(route('cost-centers.index'))->assertOk()->assertSee('PERSONAL Y RR.HH.');
        $this->post(route('cost-centers.assign'), ['user_id' => $this->worker->id, 'cost_center_id' => $centers[0]->id, 'is_active' => 1])->assertSessionHasNoErrors();
        $this->actingAs($this->worker)->post(route('route-plannings.store'), $this->payload())->assertSessionHasNoErrors();
        $first = RoutePlanning::firstOrFail();
        $this->assertSame($centers[0]->code, $first->cost_center);
        $this->actingAs($finance)->post(route('cost-centers.assign'), ['user_id' => $this->worker->id, 'cost_center_id' => $centers[1]->id, 'is_active' => 1])->assertSessionHasNoErrors();
        $this->actingAs($this->worker)->post(route('route-plannings.store'), $this->payload())->assertSessionHasNoErrors();
        $this->assertSame($centers[0]->code, $first->fresh()->cost_center);
        $this->assertSame($centers[1]->code, RoutePlanning::latest('id')->first()->cost_center);
    }

    public function test_assignment_rejects_missing_rut_inactive_center_and_shared_rut(): void
    {
        $finance = $this->user('finances');
        $center = \App\Models\CostCenter::first();
        $this->actingAs($finance)->post(route('cost-centers.assign'), ['user_id' => $this->worker->id, 'cost_center_id' => $center->id, 'is_active' => 1])->assertSessionHasErrors('user_id');
        $this->worker->update(['rut' => '123456785']);
        $center->update(['is_active' => false]);
        $this->post(route('cost-centers.assign'), ['user_id' => $this->worker->id, 'cost_center_id' => $center->id, 'is_active' => 1])->assertSessionHasErrors('cost_center_id');
        $center->update(['is_active' => true]);
        $this->post(route('cost-centers.assign'), ['user_id' => $this->worker->id, 'cost_center_id' => $center->id, 'is_active' => 1])->assertSessionHasNoErrors();
        $other = $this->user('worker', ['rut' => '12.345.678-5']);
        $this->post(route('cost-centers.assign'), ['user_id' => $other->id, 'cost_center_id' => $center->id, 'is_active' => 1])->assertSessionHasErrors('user_id');
    }

    public function test_rejected_receipts_are_immutable_and_replacement_is_idempotent(): void
    {
        $rendition = $this->rendition(['funds_received' => 100000]);
        $this->receipt($rendition);
        $this->actingAs($this->worker)->post(route('renditions.submit', $rendition))->assertSessionHasNoErrors();
        $this->actingAs($this->boss)->post(route('renditions.reject-jefatura-rendition', $rendition), ['observation' => 'Comprobante no corresponde al viaje'])->assertSessionHasNoErrors();
        $this->assertNotNull($rendition->fresh()->rejection_snapshot);
        $expense = $rendition->expenses()->first();
        $this->actingAs($this->worker)->delete(route('renditions.expenses.destroy', [$rendition, $expense]))->assertForbidden();
        $this->put(route('renditions.expenses.update', [$rendition, $expense]), ['amount' => 1])->assertForbidden();
        $this->actingAs($this->boss)->post(route('renditions.new-attempt', $rendition))->assertForbidden();
        $this->actingAs($this->worker)->post(route('renditions.new-attempt', $rendition))->assertRedirect();
        $next = $rendition->replacement()->firstOrFail();
        $this->receipt($next);
        $this->post(route('renditions.new-attempt', $rendition))->assertRedirect(route('renditions.show', $next));
        $this->assertSame(2, Rendition::count());
        $this->assertSame(1, $next->expenses()->count());
        $this->assertSame($next->id, RoutePlanning::find($rendition->route_planning_id)->rendition->id);
        $this->get(route('renditions.show', $rendition))->assertOk()->assertSee('Intento rechazado y cerrado')->assertDontSee('Terminar y Enviar');
        $this->get(route('renditions.history', ['tab' => 'rendiciones']))->assertOk()->assertSee('Comprobante no corresponde al viaje');
        try {
            $rendition->refresh()->update(['total_declared' => 1]);
            $this->fail('Se permitió modificar un intento cerrado');
        } catch (\Illuminate\Validation\ValidationException $expected) {
            $this->assertSame('rejected', $rendition->fresh()->status);
        }
    }

    public function test_all_review_roles_close_rejections_without_correction_window(): void
    {
        foreach (['controlling' => 'pending_controlling', 'finances' => 'pending_finances'] as $role => $status) {
            $rendition = $this->rendition(['status' => $status]);
            $this->receipt($rendition);
            $reviewer = $this->user($role);
            $this->actingAs($reviewer)->post(route('renditions.reject-'.$role.'-rendition', $rendition), ['observation' => 'Rechazo definitivo de prueba'])->assertSessionHasNoErrors();
            $this->assertNotNull($rendition->fresh()->closed_at);
            $this->assertSame('Rechazo definitivo de prueba', $rendition->fresh()->rejection_reason);
            $this->assertNull($rendition->fresh()->correction_deadline_at);
        }
    }

    public function test_planning_lodging_excess_requires_explicit_assigned_boss_authorization(): void
    {
        $this->user('controlling');
        $payload = array_merge($this->payload(), ['start_date' => '2026-10-10', 'end_date' => '2026-10-11',
            'requires_funds' => 1, 'requested_funds' => 60000, 'funds_alojamiento' => 60000, 'lodging_nights' => 1]);
        $this->actingAs($this->worker)->post(route('route-plannings.store'), $payload)->assertSessionHasNoErrors();
        $plan = RoutePlanning::firstOrFail();
        $this->assertEquals(18350, $plan->lodging_excess_amount);
        $this->actingAs($this->boss)->get(route('renditions.approvals'))->assertOk()->assertSee('Autorizar adicional con IVA');
        $this->actingAs($this->boss)->post(route('route-plannings.approve-jefatura', $plan))->assertSessionHasErrors('lodging');
        $this->actingAs($this->user('jefatura'))->post(route('route-plannings.authorize-lodging', $plan))->assertForbidden();
        $this->actingAs($this->boss)->post(route('route-plannings.authorize-lodging', $plan))->assertSessionHasNoErrors();
        $this->post(route('route-plannings.approve-jefatura', $plan))->assertSessionHasNoErrors();
        $this->assertSame($this->boss->id, $plan->fresh()->lodging_authorized_by);
        $this->assertSame('pending_controlling', $plan->fresh()->status);
    }

    public function test_lodging_41650_including_tax_and_daily_accumulated_excess(): void
    {
        Storage::fake('local');
        $rendition = $this->rendition();
        foreach ([30000, 20000] as $amount) {
            $this->actingAs($this->worker)->post(route('renditions.expenses.store', $rendition), [
                'date' => '2026-10-01', 'provider' => 'Hotel', 'document_type' => 'boleta',
                'expense_category' => 'alojamiento', 'amount' => $amount, 'justification' => 'Hotel de la ruta',
                'attachment' => UploadedFile::fake()->image('hotel.jpg'),
            ])->assertSessionHasNoErrors();
        }
        $excess = $rendition->expenses()->latest('id')->first();
        $this->assertEquals(8350, $excess->lodging_excess_amount);
        $this->post(route('renditions.submit', $rendition))->assertSessionHasErrors('expenses');
        $this->actingAs($this->boss)->post(route('renditions.expenses.approve-lodging-excess', [$rendition, $excess]))->assertSessionHasNoErrors();
        $this->actingAs($this->worker)->post(route('renditions.submit', $rendition))->assertSessionHasNoErrors();
    }

    public function test_report_exports_are_native_filtered_and_do_not_duplicate_money(): void
    {
        $first = $this->rendition(['funds_received' => 100000, 'total_declared' => 5000]);
        $first->routePlanning->update(['project' => 'Proyecto A', 'cost_center' => 'C113000001']);
        $this->receipt($first);
        $this->actingAs($this->worker)->post(route('renditions.submit', $first))->assertSessionHasNoErrors();
        $this->actingAs($this->boss)->post(route('renditions.reject-jefatura-rendition', $first), ['observation' => 'Falta respaldo definitivo'])->assertSessionHasNoErrors();
        $next = app(\App\Services\RenditionAttemptService::class)->replace($first, $this->worker);
        $this->receipt($next, ['amount' => 7000]);
        $next->update(['total_declared' => 7000]);
        $this->rendition(['funds_received' => 50000])->routePlanning->update(['project' => 'Proyecto B']);
        $request = \Illuminate\Http\Request::create('/', 'GET', ['project' => 'Proyecto A']);
        $data = app(\App\Services\RenditionReportService::class)->data($request);
        $this->assertCount(2, $data['rows']);
        $this->assertEquals(100000, $data['totalFunds']);
        $this->assertEquals(7000, $data['totalExpenses']);
        $this->assertSame('Falta respaldo definitivo', $data['rows'][0]['rejection_reason']);
        $finance = $this->user('finances');
        $xlsx = $this->actingAs($finance)->get(route('renditions.reports.export', ['project' => 'Proyecto A']))->assertOk();
        $this->assertStringStartsWith('PK', $xlsx->getContent());
        $this->assertStringContainsString('.xlsx', $xlsx->headers->get('Content-Disposition'));
        $zipPath = sys_get_temp_dir().'/seges-test-'.uniqid().'.zip';
        file_put_contents($zipPath, $xlsx->getContent());
        try {
            $archive = new \PharData($zipPath);
            $xml = simplexml_load_string($archive['xl/worksheets/sheet1.xml']->getContent());
            $xml->registerXPathNamespace('s', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $this->assertCount(4, $xml->xpath('//s:sheetData/s:row'));
            $this->assertStringContainsString('Falta respaldo definitivo', $archive['xl/worksheets/sheet1.xml']->getContent());
            $this->assertCount(0, $xml->xpath('//s:f'));
            unset($archive);
        } finally {
            unlink($zipPath);
        }
        $pdf = $this->get(route('renditions.reports.pdf', ['project' => 'Proyecto A']))->assertOk();
        $this->assertStringStartsWith('%PDF', $pdf->getContent());
        $this->get(route('renditions.reports', ['project' => 'Proyecto A']))->assertOk()->assertSee('Exportar Excel (.xlsx)')->assertSee('Exportar PDF');
        $qa = base_path('../../tmp/alignment-qa');
        if (! is_dir($qa)) {
            mkdir($qa, 0777, true);
        }
        file_put_contents($qa.'/reporte-prueba.xlsx', $xlsx->getContent());
        file_put_contents($qa.'/reporte-prueba.pdf', $pdf->getContent());
        $this->actingAs($this->worker)->get(route('renditions.reports.export'))->assertForbidden();
        $this->get(route('renditions.reports.pdf'))->assertForbidden();
    }

    public function test_alignment_migration_preserves_legacy_rejection_reason_and_receipts(): void
    {
        $rendition = $this->rendition(['status' => 'pending_jefatura']);
        $this->receipt($rendition);
        $this->actingAs($this->boss)->post(route('renditions.reject-jefatura-rendition', $rendition), ['observation' => 'Causa histórica del rechazo'])->assertSessionHasNoErrors();
        $migration = require database_path('migrations/2026_10_26_000001_align_rendition_attempts_and_cost_centers.php');
        $migration->down();
        $migration->up();
        $rendition->refresh();
        $this->assertSame('Causa histórica del rechazo', $rendition->rejection_reason);
        $this->assertNotNull($rendition->closed_at);
        $this->assertCount(1, $rendition->rejection_snapshot['expenses']);
        $this->assertSame('2026-10-08', $rendition->deadline_at->toDateString());
        $this->assertSame(1, $rendition->expenses()->count());
    }

    public function test_rollback_refuses_to_destroy_multiple_attempts(): void
    {
        $rendition = $this->rendition(['status' => 'rejected']);
        app(\App\Services\RenditionAttemptService::class)->replace($rendition, $this->worker);
        $migration = require database_path('migrations/2026_10_26_000001_align_rendition_attempts_and_cost_centers.php');
        try {
            $migration->down();
            $this->fail('Se permitió revertir y perder intentos');
        } catch (\RuntimeException $expected) {
            $this->assertSame(2, Rendition::count());
        }
    }

    public function test_report_empty_results_date_only_filter_and_invalid_date_range(): void
    {
        $finance = $this->user('finances');
        $this->actingAs($finance)->get(route('renditions.reports.export', ['date_to' => '2026-10-09']))->assertOk();
        $this->get(route('renditions.reports.pdf', ['project' => 'No existe']))->assertOk();
        $this->get(route('renditions.reports.export', ['date_from' => '2026-10-20', 'date_to' => '2026-10-01']))->assertSessionHasErrors('date_to');
    }

    public function test_pending_planning_funds_are_not_counted_as_a_new_advance(): void
    {
        $this->actingAs($this->worker)->post(route('route-plannings.store'), array_merge($this->payload(), [
            'requires_funds' => 1, 'requested_funds' => 50000, 'funds_bencina' => 50000,
        ]))->assertSessionHasNoErrors();
        $data = app(\App\Services\RenditionReportService::class)->data(\Illuminate\Http\Request::create('/', 'GET'));
        $this->assertEquals(50000, $data['rows'][0]['reference_funds']);
        $this->assertEquals(0, $data['totalFunds']);
    }
}
