<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activo_id');
            $table->enum('tipo', ['preventiva', 'correctiva']);
            $table->text('descripcion');
            $table->text('detalles_solucion')->nullable();
            $table->date('fecha');
            $table->date('fecha_termino')->nullable();
            $table->integer('costo')->nullable();
            $table->string('evidencia_path', 255)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->index('activo_id', 'asset_maintenances_activo_id_foreign');
            $table->index('created_by', 'asset_maintenances_created_by_foreign');
            $table->foreign('activo_id', 'asset_maintenances_activo_id_foreign')->references('id')->on('assets')->onDelete('cascade');
            $table->foreign('created_by', 'asset_maintenances_created_by_foreign')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_maintenances');
    }
};
