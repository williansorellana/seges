<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audit_locks', function (Blueprint $table) {
            $table->id();
            $table->morphs('lockable');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('locked_at')->useCurrent();
            $table->timestamp('expires_at');
            $table->timestamps();

            // Evitar que haya más de un bloqueo activo por recurso
            $table->unique(['lockable_type', 'lockable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_locks');
    }
};
