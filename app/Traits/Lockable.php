<?php

namespace App\Traits;

use App\Models\AuditLock;

trait Lockable
{
    /**
     * Relación con los bloqueos de auditoría.
     */
    public function locks()
    {
        return $this->morphMany(AuditLock::class, 'lockable');
    }

    /**
     * Obtiene el bloqueo activo actual.
     */
    public function currentLock()
    {
        return $this->locks()->active()->first();
    }

    /**
     * Determina si el recurso está bloqueado por otro usuario.
     */
    public function isLocked()
    {
        $lock = $this->currentLock();
        if (!$lock) {
            return false;
        }
        return $lock->user_id !== auth()->id();
    }

    /**
     * Obtiene el usuario que tiene el bloqueo actual.
     */
    public function lockOwner()
    {
        $lock = $this->currentLock();
        return $lock ? $lock->user : null;
    }

    /**
     * Adquiere o refresca un bloqueo para el usuario especificado.
     */
    public function acquireLock($userId = null, $durationMinutes = 5)
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) {
            return false;
        }

        // Limpiar bloqueos expirados del recurso
        $this->locks()->where('expires_at', '<=', now())->delete();

        $existing = $this->currentLock();
        if ($existing && $existing->user_id !== $userId) {
            // Existe un bloqueo activo de otro usuario, no se puede adquirir
            return false;
        }

        // Crear o actualizar el bloqueo
        $this->locks()->updateOrCreate(
            ['lockable_type' => get_class($this), 'lockable_id' => $this->id],
            [
                'user_id' => $userId,
                'locked_at' => now(),
                'expires_at' => now()->addMinutes($durationMinutes),
            ]
        );

        return true;
    }

    /**
     * Libera el bloqueo actual si pertenece al usuario.
     */
    public function releaseLock($userId = null)
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) {
            return;
        }

        $this->locks()->where('user_id', $userId)->delete();
    }
}
