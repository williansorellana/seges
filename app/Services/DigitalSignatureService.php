<?php

namespace App\Services;

use App\Models\DigitalSignature;
use Illuminate\Support\Str;

class DigitalSignatureService
{
    public function sign(
        $model,
        $user,
        array $snapshot,
        string $type = 'approval'
    ) {
        /*
        |--------------------------------------------------------------------------
        | Payload
        |--------------------------------------------------------------------------
        */

        $payload = json_encode([
            'model_type' => get_class($model),

            'model_id' => $model->id,

            'user_id' => $user->id,

            'snapshot' => $snapshot,

            'signed_at' => now()->toISOString(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | SHA256
        |--------------------------------------------------------------------------
        */

        $hash = hash('sha256', $payload);

        /*
        |--------------------------------------------------------------------------
        | Guardar firma
        |--------------------------------------------------------------------------
        */

        return DigitalSignature::create([

            'signable_type' => get_class($model),

            'signable_id' => $model->id,

            'user_id' => $user->id,

            'role' => $user->role,

            'signature_type' => $type,

            'hash' => $hash,

            'verification_token' => Str::uuid(),

            'snapshot' => $snapshot,

            'signed_snapshot' => json_encode($snapshot),

            'ip_address' => request()->ip(),

            'user_agent' => request()->userAgent(),

            'signed_at' => now(),
        ]);
    }
}