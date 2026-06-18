<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_categories', function (Blueprint $table) {
            $table->string('tipo')->default('hardware')->after('descripcion');
        });
    }

    public function down(): void    
    {
    Schema::table('asset_categories', function (Blueprint $table) {
        $table->dropColumn('tipo');
    });
    }   
};
