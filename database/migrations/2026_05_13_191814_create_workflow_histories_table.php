<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_histories', function (Blueprint $table) {
            $table->id();

            $table->morphs('workflowable');

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('from_status')->nullable();

            $table->string('to_status');

            $table->string('action');

            $table->text('comment')->nullable();

            $table->string('ip_address')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_histories');
    }
};