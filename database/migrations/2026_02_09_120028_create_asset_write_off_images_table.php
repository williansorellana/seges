<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_write_off_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_write_off_id');
            $table->string('image_path', 255);
            $table->timestamps();
            $table->index('asset_write_off_id', 'asset_write_off_images_asset_write_off_id_foreign');
            $table->foreign('asset_write_off_id', 'asset_write_off_images_asset_write_off_id_foreign')->references('id')->on('asset_write_offs')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_write_off_images');
    }
};
