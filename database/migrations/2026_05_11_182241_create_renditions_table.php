<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('renditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_planning_id')->constrained('route_plannings')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Totales
            $table->decimal('total_declared', 12, 2)->default(0);
            $table->decimal('total_approved', 12, 2)->default(0);
            $table->decimal('funds_received', 12, 2)->default(0); // los entregados en la planificación
            $table->decimal('difference', 12, 2)->default(0); // funds_received - total_approved
            
            // Flujo y Estados
            $table->enum('status', [
                'draft', 
                'pending_jefatura', 
                'pending_controlling', 
                'pending_finances', 
                'approved', 
                'rejected',
                'closed'
            ])->default('draft');
            
            // Gestión de Devoluciones/Reembolsos
            $table->boolean('refund_to_company')->default(false);
            $table->boolean('refund_to_worker')->default(false);
            $table->timestamp('refund_resolved_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('renditions');
    }
};
