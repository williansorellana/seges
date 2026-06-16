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
        Schema::table('workflow_histories', function (Blueprint $table) {
            if (!Schema::hasColumn('workflow_histories', 'observation')) {
                $table->text('observation')->nullable();
            }
            if (!Schema::hasColumn('workflow_histories', 'ip_address')) {
                $table->string('ip_address')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workflow_histories', function (Blueprint $table) {
            $table->dropColumn(['observation', 'ip_address']);
        });
    }
};
