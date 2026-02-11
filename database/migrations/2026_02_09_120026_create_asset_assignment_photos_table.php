<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_assignment_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignment_id');
            $table->string('photo_path', 255);
            $table->timestamps();
            $table->index('assignment_id', 'asset_assignment_photos_assignment_id_foreign');
            $table->foreign('assignment_id', 'asset_assignment_photos_assignment_id_foreign')->references('id')->on('asset_assignments')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_assignment_photos');
    }
};
