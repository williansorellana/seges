<?php

namespace App\Services;

use App\Models\PlanningException;
use App\Models\Rendition;
use App\Models\RoutePlanning;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class PlanningEligibilityService
{
    public function overdue(User $user)
    {
        return Rendition::with('routePlanning')->where('user_id', $user->id)
            ->whereIn('status', ['draft', 'rejected'])->get()->filter(function ($rendition) {
                $deadline = $this->effectiveDeadline($rendition);

                return $deadline && now('America/Santiago')->startOfDay()->greaterThan($deadline);
            });
    }

    public function effectiveDeadline(Rendition $rendition)
    {
        return $rendition->status === 'rejected' && $rendition->correction_deadline_at
            ? $rendition->correction_deadline_at
            : ($rendition->deadline_at ?? ($rendition->routePlanning
                ? app(RenditionDeadlineService::class)->deadlineFor($rendition->routePlanning->end_date) : null));
    }

    public function dailyCount(User $user): int
    {
        return RoutePlanning::where('user_id', $user->id)
            ->whereDate('requested_on', now('America/Santiago')->toDateString())->count();
    }

    public function grant(User $user, string $type): ?PlanningException
    {
        return PlanningException::where('user_id', $user->id)->where('type', $type)
            ->whereDate('valid_on', now('America/Santiago')->toDateString())
            ->where('status', 'approved')->whereNull('used_at')->first();
    }

    // Call inside a transaction after locking the worker row. Approval and
    // consumption use the same lock, preventing concurrent fourth requests.
    public function assertCanCreate(User $user): array
    {
        $overdue = $this->overdue($user);
        $unlock = $this->grant($user, 'temporary_unlock');
        if ($overdue->isNotEmpty() && ! $unlock) {
            throw ValidationException::withMessages(['planning' => 'Tienes rendiciones fuera de plazo. Complétalas y envíalas desde Mis rendiciones antes de crear una nueva planificación.']);
        }
        $count = $this->dailyCount($user);
        $extra = $this->grant($user, 'daily_extra');
        if ($count >= 3 || ($count >= 2 && ! $extra)) {
            throw ValidationException::withMessages(['planning' => 'Puedes crear dos planificaciones al día. La tercera requiere autorización de tu jefatura; no se permite una cuarta.']);
        }

        return array_filter([$overdue->isNotEmpty() ? $unlock : null, $count >= 2 ? $extra : null]);
    }
}
