<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('renditions', function (Blueprint $table) {
            $table->unique('route_planning_id', 'renditions_route_planning_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('renditions', function (Blueprint $table) {
            $table->dropUnique('renditions_route_planning_id_unique');
        });
    }
};