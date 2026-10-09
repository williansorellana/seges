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
    private User $worker;

    private User $boss;

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
            if (preg_match('/route_planning|rendition|workflow|digital_signature|rut_cost_center|travel_submission_rules/', basename($file))) {
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

    private function user(string $role, array $extra = []): User
    {
        return User::create(array_merge(['name' => 'Prueba', 'last_name' => 'Reglas',
            'email' => uniqid().'@example.test', 'password' => 'test-password', 'role' => $role,
            'authorized_modules' => ['renditions'], 'is_active' => true, 'must_change_password' => false], $extra));
    }

    private function payload(): array
    {
        return ['trip_type' => 'terreno', 'motive' => 'Visita', 'destination' => 'Santiago',
            'start_date' => '2026-10-10', 'end_date' => '2026-10-13'];
    }

    private function rendition(array $extra = [], string $end = '2026-10-01'): Rendition
    {
        $planning = RoutePlanning::create(['user_id' => $this->worker->id, 'trip_type' => 'terreno',
            'motive' => 'Visita', 'destination' => 'Santiago', 'start_date' => '2026-10-01',
            'end_date' => $end, 'status' => 'approved', 'requested_on' => '2026-10-01']);

        return Rendition::create(array_merge(['route_planning_id' => $planning->id,
            'user_id' => $this->worker->id, 'status' => 'draft', 'deadline_at' => '2026-10-08'], $extra));
    }

    private function receipt(Rendition $rendition, array $extra = []): void
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

    public function test_rejection_grants_two_business_days_and_preserves_original_submission(): void
    {
        $rendition = $this->rendition();
        $this->receipt($rendition);
        $this->actingAs($this->worker)->post(route('renditions.submit', $rendition))->assertSessionHasNoErrors();
        $first = $rendition->fresh()->first_submitted_at->toDateTimeString();
        $this->actingAs($this->boss)->post(route('renditions.reject-jefatura-rendition', $rendition),
            ['observation' => 'Corregir información del proveedor'])->assertSessionHasNoErrors();
        $this->assertSame('2026-10-14', $rendition->fresh()->correction_deadline_at->toDateString());
        Carbon::setTestNow(Carbon::parse('2026-10-14 23:00', 'America/Santiago'));
        $this->assertCount(0, app(PlanningEligibilityService::class)->overdue($this->worker));
        Carbon::setTestNow(Carbon::parse('2026-10-15 08:00', 'America/Santiago'));
        $this->assertCount(1, app(PlanningEligibilityService::class)->overdue($this->worker));
        $this->actingAs($this->worker)->post(route('renditions.submit', $rendition))->assertSessionHasNoErrors();
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
        $rendition = $this->rendition(['status' => 'rejected']);
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
        $this->assertSame('2026-10-14', $rendition->correction_deadline_at->toDateString());
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
}
