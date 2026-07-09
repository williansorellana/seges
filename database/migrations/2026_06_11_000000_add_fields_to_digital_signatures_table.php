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
        Schema::table('digital_signatures', function (Blueprint $table) {
            if (!Schema::hasColumn('digital_signatures', 'role')) {
                $table->string('role')->nullable();
            }
            if (!Schema::hasColumn('digital_signatures', 'verification_token')) {
                $table->string('verification_token')->nullable();
            }
            if (!Schema::hasColumn('digital_signatures', 'signed_snapshot')) {
                $table->json('signed_snapshot')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('digital_signatures', function (Blueprint $table) {
            $table->dropColumn(['role', 'verification_token', 'signed_snapshot']);
        });
    }
};
