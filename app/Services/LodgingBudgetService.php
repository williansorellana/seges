<?php

namespace App\Services;

use App\Models\RoutePlanning;
use Illuminate\Validation\ValidationException;

class LodgingBudgetService
{
    public function assertAuthorized(RoutePlanning $planning): void
    {
        if ($planning->lodging_excess_amount > 0 && ! in_array($planning->lodging_excess_status, ['approved', 'legacy'], true)) {
            throw ValidationException::withMessages(['lodging' => 'La jefatura debe autorizar expresamente el adicional de alojamiento (IVA incluido) antes de aprobar la planificación.']);
        }
    }
}
