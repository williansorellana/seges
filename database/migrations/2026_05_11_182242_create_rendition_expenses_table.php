<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('rendition_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rendition_id')->constrained('renditions')->cascadeOnDelete();
            
            $table->date('date');
            $table->string('provider');
            $table->enum('document_type', ['boleta', 'factura', 'vale', 'otro']);
            $table->string('document_number')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('attachment_path'); // PDF or Image
            
            // Validaciones durante la auditoría
            $table->boolean('is_valid')->default(true);
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('rendition_expenses');
    }
};
