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
            $table->json('destinations')->nullable()->after('destination');
            $table->decimal('funds_peaje', 12, 2)->nullable()->after('requested_funds');
            $table->decimal('funds_bencina', 12, 2)->nullable()->after('funds_peaje');
            $table->decimal('funds_alojamiento', 12, 2)->nullable()->after('funds_bencina');
            $table->decimal('funds_alimentacion', 12, 2)->nullable()->after('funds_alojamiento');
            $table->decimal('funds_otros', 12, 2)->nullable()->after('funds_alimentacion');
            $table->text('funds_description')->nullable()->after('funds_otros');
            $table->text('notification_emails')->nullable()->after('digital_signature');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('route_plannings', function (Blueprint $table) {
            $table->dropColumn([
                'destinations',
                'funds_peaje',
                'funds_bencina',
                'funds_alojamiento',
                'funds_alimentacion',
                'funds_otros',
                'funds_description',
                'notification_emails',
            ]);
        });
    }
};
