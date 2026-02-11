<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Added this import

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vehicle_requests', function (Blueprint $table) {
            $table->json('delivery_photos')->nullable()->after('end_date');
        });

        // Modify ENUM to include 'in_trip'
        // Using DB::statement for MySQL because Schema::table()->change() doesn't support ENUM properly on all versions
        DB::statement("ALTER TABLE vehicle_requests MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'in_trip', 'completed') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_requests', function (Blueprint $table) {
            $table->dropColumn('delivery_photos');
        });

        // Revert ENUM
        DB::statement("ALTER TABLE vehicle_requests MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending'");
    }
};
