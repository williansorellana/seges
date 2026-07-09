<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('route_plannings', function (Blueprint $table) {
            $table->json('amipass_ruts')->nullable()->after('notification_emails');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('route_plannings', function (Blueprint $table) {
            $table->dropColumn('amipass_ruts');
        });
    }
};
