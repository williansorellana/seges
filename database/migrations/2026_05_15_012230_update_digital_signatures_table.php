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

            if (!Schema::hasColumn('digital_signatures', 'signature_type')) {
                $table->string('signature_type')->nullable();
            }

            if (!Schema::hasColumn('digital_signatures', 'snapshot')) {
                $table->json('snapshot')->nullable();
            }

            if (!Schema::hasColumn('digital_signatures', 'ip_address')) {
                $table->string('ip_address')->nullable();
            }

            if (!Schema::hasColumn('digital_signatures', 'user_agent')) {
                $table->text('user_agent')->nullable();
            }

            if (!Schema::hasColumn('digital_signatures', 'signed_at')) {
                $table->timestamp('signed_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
