<?php

namespace App\Services;

use App\Models\Rendition;
use App\Models\User;
use App\Models\WorkflowHistory;
use Illuminate\Support\Facades\DB;

class RenditionAttemptService
{
    public function replace(Rendition $previous, User $owner): Rendition
    {
        abort_unless($previous->user_id === $owner->id, 403);

        return DB::transaction(function () use ($previous, $owner) {
            User::whereKey($owner->id)->lockForUpdate()->firstOrFail();
            $previous->refresh();
            abort_unless($previous->status === 'rejected', 422, 'Solo se reemplazan intentos rechazados.');
            if ($existing = $previous->replacement()->first()) {
                return $existing;
            }
            $next = Rendition::create([
                'route_planning_id' => $previous->route_planning_id, 'user_id' => $owner->id,
                'attempt_number' => $previous->attempt_number + 1, 'replaces_rendition_id' => $previous->id,
                'funds_received' => $previous->funds_received, 'deadline_at' => $previous->deadline_at,
                'status' => 'draft',
            ]);
            WorkflowHistory::create(['workflowable_type' => Rendition::class, 'workflowable_id' => $next->id,
                'user_id' => $owner->id, 'action' => 'replacement_created', 'from_status' => null,
                'to_status' => 'draft', 'observation' => 'Nuevo intento en reemplazo de RND-'.$previous->id.'. Conserva los fondos originales; no genera un nuevo anticipo.', 'ip_address' => request()->ip()]);

            return $next;
        });
    }
}
