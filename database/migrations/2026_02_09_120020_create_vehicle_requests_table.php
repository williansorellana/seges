<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->enum('destination_type', ['local', 'outside'])->default('local');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->dateTime('original_end_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->string('rejection_reason', 255)->nullable();
            $table->unsignedBigInteger('completed_by_user_id')->nullable();
            $table->text('early_termination_reason')->nullable();
            $table->integer('return_mileage')->nullable();
            $table->unsignedBigInteger('conductor_id')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->index('user_id', 'vehicle_requests_user_id_foreign');
            $table->index('vehicle_id', 'vehicle_requests_vehicle_id_foreign');
            $table->index('conductor_id', 'vehicle_requests_conductor_id_foreign');
            $table->index('completed_by_user_id', 'vehicle_requests_completed_by_user_id_foreign');

            $table->foreign('completed_by_user_id', 'vehicle_requests_completed_by_user_id_foreign')
                  ->references('id')->on('users')
                  ->onDelete('set null');

            $table->foreign('conductor_id', 'vehicle_requests_conductor_id_foreign')
                  ->references('id')->on('conductores')
                  ->onDelete('set null');

            $table->foreign('user_id', 'vehicle_requests_user_id_foreign')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->foreign('vehicle_id', 'vehicle_requests_vehicle_id_foreign')
                  ->references('id')->on('vehicles')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_requests');
    }
};
