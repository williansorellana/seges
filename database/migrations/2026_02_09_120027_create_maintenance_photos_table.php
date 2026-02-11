<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('maintenance_id');
            $table->string('photo_path', 255);
            $table->timestamps();
            $table->index('maintenance_id', 'maintenance_photos_maintenance_id_foreign');
            $table->foreign('maintenance_id', 'maintenance_photos_maintenance_id_foreign')->references('id')->on('asset_maintenances')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_photos');
    }
};
