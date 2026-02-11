<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vehicle_request_companions', function (Blueprint $table) {
            $table->string('external_position')->nullable()->after('external_rut');
            $table->string('external_department')->nullable()->after('external_position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_request_companions', function (Blueprint $table) {
            $table->dropColumn(['external_position', 'external_department']);
        });
    }
};
