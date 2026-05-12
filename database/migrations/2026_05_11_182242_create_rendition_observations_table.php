<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('rendition_observations', function (Blueprint $table) {
            $table->id();
            
            // Relación polimórfica para que sirva tanto a RoutePlanning como a Rendition
            $table->morphs('observable'); 
            
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // quién hace la observación
            
            $table->text('observation');
            $table->enum('action', ['comment', 'approved', 'rejected', 'returned'])->default('comment');
            
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('rendition_observations');
    }
};
