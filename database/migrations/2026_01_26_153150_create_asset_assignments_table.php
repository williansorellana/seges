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
        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activo_id')->constrained('assets')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('fecha_entrega');
            $table->dateTime('fecha_devolucion')->nullable();
            $table->string('estado_entrega')->comment('good, fair, poor, damaged');
            $table->string('estado_devolucion')->nullable()->comment('good, fair, poor, damaged');
            $table->text('observaciones')->nullable();
            $table->boolean('alerted_overdue')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_assignments');
    }
};
