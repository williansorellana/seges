<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel_loads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('vehicle_request_id')->nullable();
            $table->timestamp('date');
            $table->integer('mileage');
            $table->decimal('liters', 8, 2);
            $table->integer('price_per_liter');
            $table->integer('total_cost');
            $table->string('invoice_number', 255)->nullable();
            $table->string('receipt_photo_path', 255)->nullable();
            $table->decimal('efficiency_km_l', 8, 2)->nullable();
            $table->timestamps();
            $table->index('vehicle_id', 'fuel_loads_vehicle_id_foreign');
            $table->index('user_id', 'fuel_loads_user_id_foreign');
            $table->index('vehicle_request_id', 'fuel_loads_vehicle_request_id_foreign');
            $table->foreign('user_id', 'fuel_loads_user_id_foreign')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('vehicle_id', 'fuel_loads_vehicle_id_foreign')->references('id')->on('vehicles')->onDelete('cascade');
            $table->foreign('vehicle_request_id', 'fuel_loads_vehicle_request_id_foreign')->references('id')->on('vehicle_requests')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_loads');
    }
};
