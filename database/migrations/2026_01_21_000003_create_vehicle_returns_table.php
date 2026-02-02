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
        Schema::create('vehicle_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_request_id')->constrained()->onDelete('cascade');
            $table->integer('return_mileage');
            $table->enum('fuel_level', ['1/4', '1/2', '3/4', 'full']);
            $table->enum('tire_status_front', ['good', 'fair', 'poor']);
            $table->enum('tire_status_rear', ['good', 'fair', 'poor']);
            $table->enum('cleanliness', ['clean', 'dirty', 'very_dirty']);
            $table->boolean('body_damage_reported')->default(false);
            $table->text('comments')->nullable();
            $table->json('photos_paths')->nullable(); // Store array of file paths
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_returns');
    }
};
