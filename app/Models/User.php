<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    //Aquí implementamos la notificación personalizada para verificación de email.
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmailCustom);
    }
    /**
     * Los atributos que se pueden asignar de forma masiva.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'profile_photo_path',
        'rut',
        'address',
        'phone',
        'cargo',
        'departamento',
        'license_expires_at',
        'license_photo_path',
        'role',
        'must_change_password',
        'is_active',
        'authorized_modules',
        'jefatura_id',
    ];

    /**
     * Los atributos que deben ocultarse para la serialización.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Obtiene los atributos que deben ser convertidos (casting).
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'license_expires_at' => 'date',
            'must_change_password' => 'boolean',
            'is_active' => 'boolean',
            'authorized_modules' => 'array',
        ];
    }

    /**
     * Verificar si el usuario tiene acceso a un módulo específico.
     *
     * @param string $module
     * @return bool
     */
    public function hasModuleAccess(string $module): bool
    {
        // Admin siempre tiene acceso a todo.
        if ($this->role === 'admin') {
            return true;
        }

        $modules = $this->authorized_modules ?? [];

        // Si no tiene módulos autorizados, no debe acceder al módulo.
        if (empty($modules)) {
            return false;
        }

        if (in_array('all', $modules, true)) {
            return true;
        }

        return in_array($module, $modules, true);
    }

    public function vehicleRequests()
    {
        return $this->hasMany(VehicleRequest::class);
    }

    public function getShortNameAttribute(): string
    {
        // Retorna "PrimerNombre PrimerApellido"
        $firstName = explode(' ', $this->name)[0];
        $firstLastName = explode(' ', $this->last_name ?? '')[0];

        return trim("$firstName $firstLastName");
    }

    public function jefatura()
    {
        return $this->belongsTo(User::class, 'jefatura_id');
    }

    public function subordinados()
    {
        return $this->hasMany(User::class, 'jefatura_id');
    }
}
