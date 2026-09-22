<?php

namespace App\Policies;

use App\Models\RoutePlanning;
use App\Models\User;

class RoutePlanningPolicy
{
    public function approveFinance(User $user, RoutePlanning $planning): bool
    {
        return
            $planning->status === 'pending_finances'
            &&
            $user->role === 'finances';
    }

    public function approveJefatura(User $user, RoutePlanning $planning): bool
    {
        return
            $planning->status === 'pending_jefatura'
            &&
            $user->role === 'jefatura'
            &&
            $planning->user->jefatura_id === $user->id;
    }
}
