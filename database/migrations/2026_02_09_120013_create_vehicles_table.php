<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate', 255);
            $table->string('brand', 255);
            $table->string('model', 255);
            $table->string('serial_number', 255)->nullable();
            $table->integer('year');
            $table->string('fuel_type', 255)->nullable();
            $table->integer('mileage');
            $table->enum('status', ['available', 'maintenance', 'out_of_service', 'occupied'])->default('available');
            $table->string('image_path', 255)->nullable();
            $table->string('insurance_policy_path', 255)->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
            $table->unique('plate', 'vehicles_plate_unique');
            $table->unique('serial_number', 'vehicles_serial_number_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
