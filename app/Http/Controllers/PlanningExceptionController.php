<?php

namespace App\Http\Controllers;

use App\Models\PlanningException;
use App\Models\User;
use App\Notifications\WorkflowNotification;
use App\Services\PlanningEligibilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PlanningExceptionController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate(['type' => 'required|in:daily_extra,temporary_unlock', 'reason' => 'required|string|min:10|max:1000']);
        $exception = DB::transaction(function () use ($data) {
            $user = User::whereKey(auth()->id())->lockForUpdate()->firstOrFail();
            $boss = $user->jefatura;
            if (! $boss || $boss->role !== 'jefatura' || ! $boss->is_active || $boss->id === $user->id) {
                throw ValidationException::withMessages(['reason' => 'Necesitas una jefatura activa asignada para solicitar una excepción.']);
            }
            $policy = app(PlanningEligibilityService::class);
            if ($data['type'] === 'daily_extra' && ($policy->dailyCount($user) !== 2 || $policy->overdue($user)->isNotEmpty())) {
                throw ValidationException::withMessages(['reason' => 'La tercera planificación se solicita después de crear dos hoy y sin rendiciones vencidas.']);
            }
            if ($data['type'] === 'temporary_unlock' && $policy->overdue($user)->isEmpty()) {
                throw ValidationException::withMessages(['reason' => 'No tienes rendiciones vencidas que requieran un desbloqueo.']);
            }
            $exists = PlanningException::where('user_id', $user->id)->where('type', $data['type'])
                ->whereDate('valid_on', now()->toDateString())->exists();
            if ($exists) {
                throw ValidationException::withMessages(['reason' => 'Ya existe una solicitud de este tipo para hoy. Revisa su estado.']);
            }

            return PlanningException::create(['user_id' => $user->id, 'type' => $data['type'],
                'valid_on' => now()->toDateString(), 'request_reason' => $data['reason']]);
        });
        $exception->user->jefatura->notify(new WorkflowNotification('Excepción de planificación solicitada',
            $exception->user->name.' solicita una excepción para hoy. Motivo: '.$exception->request_reason,
            route('route-plannings.index')));

        return back()->with('success', 'Solicitud enviada a tu jefatura. La autorización solo tendrá vigencia hoy y para una planificación.');
    }

    public function decide(Request $request, PlanningException $exception)
    {
        $data = $request->validate(['decision' => 'required|in:approved,rejected', 'reason' => 'required|string|min:10|max:1000']);
        DB::transaction(function () use ($exception, $data) {
            $worker = User::whereKey($exception->user_id)->lockForUpdate()->firstOrFail();
            abort_unless(auth()->user()->role === 'jefatura' && $worker->jefatura_id === auth()->id()
                && $worker->id !== auth()->id(), 403);
            $exception->refresh();
            abort_unless($exception->status === 'pending' && $exception->valid_on->isToday(), 422, 'La solicitud ya fue resuelta o venció.');
            $exception->update(['status' => $data['decision'], 'decision_reason' => $data['reason'],
                'decided_by' => auth()->id(), 'decided_at' => now()]);
        });
        $exception->user->notify(new WorkflowNotification('Excepción de planificación resuelta',
            ($data['decision'] === 'approved' ? 'Autorizada' : 'Rechazada').' para hoy. Motivo: '.$data['reason'],
            route('route-plannings.index')));

        return back()->with('success', 'Decisión registrada. Se notificó al trabajador.');
    }
}
