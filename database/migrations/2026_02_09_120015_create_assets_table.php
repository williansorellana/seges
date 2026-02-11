<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_interno', 255);
            $table->string('codigo_barra', 255);
            $table->string('nombre', 255);
            $table->unsignedBigInteger('categoria_id');
            $table->string('marca', 255)->nullable();
            $table->string('modelo', 255)->nullable();
            $table->string('numero_serie', 255)->nullable();
            $table->enum('estado', ['available', 'assigned', 'maintenance', 'written_off'])->default('available');
            $table->string('ubicacion', 255)->nullable();
            $table->date('fecha_adquisicion')->nullable();
            $table->integer('valor_referencial')->nullable();
            $table->string('foto_path', 255)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
            $table->unique('codigo_interno', 'assets_codigo_interno_unique');
            $table->unique('codigo_barra', 'assets_codigo_barra_unique');
            $table->index('categoria_id', 'assets_categoria_id_foreign');
            $table->foreign('categoria_id', 'assets_categoria_id_foreign')->references('id')->on('asset_categories')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
