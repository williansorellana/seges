<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('last_name', 255)->nullable();
            $table->string('email', 255);
            $table->enum('role', ['admin', 'supervisor', 'worker', 'viewer'])->default('worker');
            $table->string('job_title', 255)->nullable();
            $table->string('department', 255)->nullable();
            $table->string('license_number', 255)->nullable();
            $table->string('license_type', 255)->nullable();
            $table->date('license_expires_at')->nullable();
            $table->string('license_photo_path', 2048)->nullable();
            $table->string('rut', 255)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('phone', 255)->nullable();
            $table->string('cargo', 255)->nullable();
            $table->string('departamento', 255)->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255);
            $table->boolean('must_change_password')->default('0');
            $table->boolean('is_active')->default('1');
            $table->string('remember_token', 100)->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
            $table->unique('email', 'users_email_unique');
            $table->unique('rut', 'users_rut_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
