<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega marcas de envío para evitar correos repetidos de inicio/término de viaje.
     */
    public function up(): void
    {
        Schema::table('vehicle_requests', function (Blueprint $table) {
            $table->timestamp('start_reminder_sent_at')->nullable()->after('status');
            $table->timestamp('end_reminder_sent_at')->nullable()->after('start_reminder_sent_at');
        });
    }

    /**
     * Revierte las marcas de envío de recordatorios.
     */
    public function down(): void
    {
        Schema::table('vehicle_requests', function (Blueprint $table) {
            $table->dropColumn([
                'start_reminder_sent_at',
                'end_reminder_sent_at',
            ]);
        });
    }
};