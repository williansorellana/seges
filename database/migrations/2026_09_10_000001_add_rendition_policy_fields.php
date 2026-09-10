<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('route_plannings', function (Blueprint $table) {
            $table->boolean('includes_breakfast')->default(false)->after('requires_amipass');
            $table->unsignedInteger('excluded_lunches')->default(0)->after('includes_breakfast');
            $table->unsignedInteger('excluded_dinners')->default(0)->after('excluded_lunches');
            $table->enum('amipass_rate_type', ['venta', 'otros'])->nullable()->after('excluded_dinners');
            $table->decimal('amipass_per_person_amount', 12, 2)->default(0)->after('amipass_amount');
            $table->string('project')->nullable()->after('region');
            $table->string('section')->nullable()->after('project');
            $table->string('cost_center')->nullable()->after('section');
        });

        Schema::table('renditions', function (Blueprint $table) {
            $table->date('deadline_at')->nullable()->after('route_planning_id');
            $table->unsignedInteger('rejection_count')->default(0)->after('status');
        });

        Schema::table('rendition_expenses', function (Blueprint $table) {
            $table->decimal('authorized_amount', 12, 2)->nullable()->after('amount');
            $table->decimal('lodging_excess_amount', 12, 2)->default(0)->after('authorized_amount');
            $table->enum('lodging_excess_status', ['not_required', 'pending', 'approved', 'rejected'])->default('not_required')->after('lodging_excess_amount');
            $table->foreignId('lodging_excess_authorized_by')->nullable()->constrained('users')->nullOnDelete()->after('lodging_excess_status');
            $table->timestamp('lodging_excess_authorized_at')->nullable()->after('lodging_excess_authorized_by');
        });
    }

    public function down(): void
    {
        Schema::table('rendition_expenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lodging_excess_authorized_by');
            $table->dropColumn(['authorized_amount', 'lodging_excess_amount', 'lodging_excess_status', 'lodging_excess_authorized_at']);
        });
        Schema::table('renditions', function (Blueprint $table) { $table->dropColumn(['deadline_at', 'rejection_count']); });
        Schema::table('route_plannings', function (Blueprint $table) {
            $table->dropColumn(['includes_breakfast', 'excluded_lunches', 'excluded_dinners', 'amipass_rate_type', 'amipass_per_person_amount', 'project', 'section', 'cost_center']);
        });
    }
};
