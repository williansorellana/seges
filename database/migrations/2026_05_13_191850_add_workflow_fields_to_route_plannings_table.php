<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('route_plannings', function (Blueprint $table) {

            $table->string('workflow_status')
                ->default('draft');

            $table->timestamp('submitted_at')->nullable();

            $table->timestamp('approved_at')->nullable();

            $table->timestamp('rejected_at')->nullable();

            $table->string('current_department')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('route_plannings', function (Blueprint $table) {
            $table->dropColumn([
                'workflow_status',
                'submitted_at',
                'approved_at',
                'rejected_at',
                'current_department'
            ]);
        });
    }
};