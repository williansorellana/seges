<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digital_signatures', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relación polimórfica
            |--------------------------------------------------------------------------
            */

            $table->morphs('signable');

            /*
            |--------------------------------------------------------------------------
            | Usuario firmante
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Tipo firma
            |--------------------------------------------------------------------------
            */

            $table->string('signature_type');

            /*
            |--------------------------------------------------------------------------
            | Hash SHA256
            |--------------------------------------------------------------------------
            */

            $table->string('hash', 64);

            /*
            |--------------------------------------------------------------------------
            | Snapshot firmado
            |--------------------------------------------------------------------------
            */

            $table->json('snapshot');

            /*
            |--------------------------------------------------------------------------
            | Datos auditoría
            |--------------------------------------------------------------------------
            */

            $table->ipAddress('ip_address')->nullable();

            $table->text('user_agent')->nullable();

            $table->timestamp('signed_at');

            $table->timestamps();
        });
    }
};