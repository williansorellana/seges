<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_write_offs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('motivo');
            $table->date('fecha');
            $table->timestamps();
            $table->index('asset_id', 'asset_write_offs_asset_id_foreign');
            $table->index('user_id', 'asset_write_offs_user_id_foreign');
            $table->foreign('asset_id', 'asset_write_offs_asset_id_foreign')->references('id')->on('assets')->onDelete('cascade');
            $table->foreign('user_id', 'asset_write_offs_user_id_foreign')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_write_offs');
    }
};
