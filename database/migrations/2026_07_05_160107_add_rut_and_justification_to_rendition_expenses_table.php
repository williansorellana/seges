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
        Schema::table('rendition_expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('rendition_expenses', 'provider_rut')) {
                $table->string('provider_rut')->nullable()->after('provider');
            }
            if (!Schema::hasColumn('rendition_expenses', 'justification')) {
                $table->text('justification')->nullable()->after('expense_category');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rendition_expenses', function (Blueprint $table) {
            $table->dropColumn(['provider_rut', 'justification']);
        });
    }
};
