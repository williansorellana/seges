<?php

namespace App\Policies;

use App\Models\RoutePlanning;
use App\Models\User;

class RoutePlanningPolicy
{
    public function approveFinance(User $user, RoutePlanning $planning): bool
    {
        return
            $planning->workflow_status === 'pending_finance'
            &&
            $user->departamento === 'Finanzas'
            &&
            in_array($user->role, [
                'admin',
                'finance_approver'
            ]);
    }

    public function approveJefatura(User $user, RoutePlanning $planning): bool
    {
        return
            $planning->workflow_status === 'pending_jefatura'
            &&
            $planning->user->jefatura_id === $user->id;
    }
}