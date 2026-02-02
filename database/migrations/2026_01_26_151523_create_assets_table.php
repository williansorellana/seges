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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_interno')->unique()->comment('Ejemplo: ACT-0001');
            $table->string('codigo_barra')->unique()->comment('Generado automáticamente');
            $table->string('nombre');
            $table->foreignId('categoria_id')->constrained('asset_categories')->onDelete('restrict');
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->string('numero_serie')->nullable()->comment('Número de serie del fabricante');
            $table->enum('estado', ['available', 'assigned', 'maintenance', 'written_off'])->default('available');
            $table->string('ubicacion')->nullable();
            $table->date('fecha_adquisicion')->nullable();
            $table->integer('valor_referencial')->nullable()->comment('Valor en pesos');
            $table->string('foto_path')->nullable();
            $table->text('observaciones')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
