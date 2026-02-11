<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->string('rut', 255);
            $table->string('departamento', 255)->nullable();
            $table->string('cargo', 255)->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
            $table->unique('rut', 'workers_rut_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workers');
    }
};
