<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_returns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_request_id');
            $table->integer('return_mileage');
            $table->enum('fuel_level', ['1/4', '1/2', '3/4', 'full']);
            $table->enum('tire_status_front', ['good', 'fair', 'poor']);
            $table->enum('tire_status_rear', ['good', 'fair', 'poor']);
            $table->enum('cleanliness', ['clean', 'dirty', 'very_dirty']);
            $table->boolean('body_damage_reported')->default('0');
            $table->text('comments')->nullable();
            $table->json('photos_paths')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
            $table->index('vehicle_request_id', 'vehicle_returns_vehicle_request_id_foreign');
            $table->foreign('vehicle_request_id', 'vehicle_returns_vehicle_request_id_foreign')->references('id')->on('vehicle_requests')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_returns');
    }
};
