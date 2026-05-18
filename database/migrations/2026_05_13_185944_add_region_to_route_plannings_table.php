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
        Schema::table('route_plannings', function (Blueprint $table) {
            $table->string('region')->nullable()->after('destination');
        });
    }

    public function down(): void
    {
        Schema::table('route_plannings', function (Blueprint $table) {
            $table->dropColumn('region');
        });
    }
};
