<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_maintenance_states', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->integer('last_oil_change_km')->nullable();
            $table->integer('next_oil_change_km')->nullable();
            $table->enum('tire_status_front', ['good', 'fair', 'poor'])->default('good');
            $table->enum('tire_status_rear', ['good', 'fair', 'poor'])->default('good');
            $table->date('last_service_date')->nullable();
            $table->timestamps();
            $table->index('vehicle_id', 'vehicle_maintenance_states_vehicle_id_foreign');
            $table->foreign('vehicle_id', 'vehicle_maintenance_states_vehicle_id_foreign')->references('id')->on('vehicles')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_maintenance_states');
    }
};
