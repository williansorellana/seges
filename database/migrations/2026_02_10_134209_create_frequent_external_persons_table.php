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
        Schema::create('frequent_external_persons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rut')->nullable();
            $table->timestamps();

            // Índice para búsqueda rápida
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frequent_external_persons');
    }
};
