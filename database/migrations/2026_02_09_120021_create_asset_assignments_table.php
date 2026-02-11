<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activo_id');
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->unsignedBigInteger('worker_id')->nullable();
            $table->dateTime('fecha_entrega');
            $table->dateTime('fecha_estimada_devolucion')->nullable();
            $table->dateTime('fecha_devolucion')->nullable();
            $table->string('estado_entrega', 255);
            $table->string('estado_devolucion', 255)->nullable();
            $table->text('comentarios_devolucion')->nullable();
            $table->boolean('alerted_overdue')->default('0');
            $table->text('observaciones')->nullable();
            $table->string('trabajador_nombre', 255)->nullable();
            $table->string('trabajador_rut', 255)->nullable();
            $table->string('trabajador_departamento', 255)->nullable();
            $table->string('trabajador_cargo', 255)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->index('activo_id', 'asset_assignments_activo_id_foreign');
            $table->index('usuario_id', 'asset_assignments_usuario_id_foreign');
            $table->index('worker_id', 'asset_assignments_worker_id_foreign');
            $table->index('created_by', 'asset_assignments_created_by_foreign');
            $table->foreign('activo_id', 'asset_assignments_activo_id_foreign')->references('id')->on('assets')->onDelete('cascade');
            $table->foreign('created_by', 'asset_assignments_created_by_foreign')->references('id')->on('users')->onDelete('set null');
            $table->foreign('usuario_id', 'asset_assignments_usuario_id_foreign')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('worker_id', 'asset_assignments_worker_id_foreign')->references('id')->on('workers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_assignments');
    }
};
