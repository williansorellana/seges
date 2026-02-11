<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('meeting_room_id');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('purpose', 255);
            $table->integer('attendees')->nullable();
            $table->text('resources')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->timestamps();
            $table->index('user_id', 'room_reservations_user_id_foreign');
            $table->index('meeting_room_id', 'room_reservations_meeting_room_id_foreign');
            $table->foreign('meeting_room_id', 'room_reservations_meeting_room_id_foreign')->references('id')->on('meeting_rooms')->onDelete('cascade');
            $table->foreign('user_id', 'room_reservations_user_id_foreign')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_reservations');
    }
};
