<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('renditions', function (Blueprint $table) {
            $table->boolean('payment_completed')->default(false)->after('refund_resolved_at');
            $table->timestamp('payment_completed_at')->nullable()->after('payment_completed');
            $table->foreignId('payment_completed_by')->nullable()->after('payment_completed_at')->constrained('users')->nullOnDelete();
            $table->text('payment_observation')->nullable()->after('payment_completed_by');
        });
    }

    public function down(): void
    {
        Schema::table('renditions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_completed_by');
            $table->dropColumn([
                'payment_completed',
                'payment_completed_at',
                'payment_observation',
            ]);
        });
    }
};