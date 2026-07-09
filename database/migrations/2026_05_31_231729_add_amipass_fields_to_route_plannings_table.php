<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('route_plannings', function (Blueprint $table) {
            $table->time('amipass_start_time')->nullable()->after('amipass_days');
            $table->time('amipass_end_time')->nullable()->after('amipass_start_time');
            $table->string('usual_zone')->nullable()->after('amipass_end_time');
            $table->string('extraordinary_zone')->nullable()->after('usual_zone');
            $table->decimal('amipass_amount', 12, 2)->default(0)->after('extraordinary_zone');
            $table->integer('amipass_business_days')->default(0)->after('amipass_amount');
        });
    }

    public function down(): void
    {
        Schema::table('route_plannings', function (Blueprint $table) {
            $table->dropColumn([
                'amipass_start_time',
                'amipass_end_time',
                'usual_zone',
                'extraordinary_zone',
                'amipass_amount',
                'amipass_business_days',
            ]);
        });
    }
};