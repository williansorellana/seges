<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->string('type', 255);
            $table->date('expires_at')->nullable();
            $table->string('file_path', 255)->nullable();
            $table->string('status', 255)->default('active');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
            $table->index('vehicle_id', 'vehicle_documents_vehicle_id_foreign');
            $table->foreign('vehicle_id', 'vehicle_documents_vehicle_id_foreign')->references('id')->on('vehicles')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_documents');
    }
};
