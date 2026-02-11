<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('frequent_external_persons', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('frequent_external_persons', function (Blueprint $table) {
            if (Schema::hasColumn('frequent_external_persons', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
