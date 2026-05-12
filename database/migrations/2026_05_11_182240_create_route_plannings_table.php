<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('route_plannings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Detalles del viaje
            $table->enum('trip_type', ['terreno', 'reunion']);
            $table->string('motive');
            $table->string('destination');
            $table->date('start_date');
            $table->date('end_date');
            
            // Solicitud financiera y Amipass
            $table->boolean('requires_funds')->default(false);
            $table->decimal('requested_funds', 12, 2)->nullable();
            
            $table->boolean('requires_amipass')->default(false);
            $table->integer('amipass_days')->nullable();
            
            // Flujo de aprobación y Estado
            $table->enum('status', [
                'draft', 
                'pending_jefatura', 
                'pending_controlling', 
                'pending_finances',
                'approved', 
                'rejected'
            ])->default('draft');
            
            // Firma digital (para integridad de datos aprobados)
            $table->string('digital_signature')->nullable()->comment('Hash de los datos tras aprobación');
            $table->timestamp('signed_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('route_plannings');
    }
};
