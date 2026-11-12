<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('route_planning_companions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_planning_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('rut', 20);
            $table->boolean('receives_amipass')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('route_planning_companions'); }
};
