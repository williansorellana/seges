<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rendition_expenses', function (Blueprint $table) {
            $table->string('expense_category')
                ->default('otros')
                ->after('document_type');
        });
    }

    public function down(): void
    {
        Schema::table('rendition_expenses', function (Blueprint $table) {
            $table->dropColumn('expense_category');
        });
    }
};