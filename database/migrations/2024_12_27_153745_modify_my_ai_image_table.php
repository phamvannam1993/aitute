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
        Schema::table('my_ai_images', function (Blueprint $table) {
            $table->float('prompt_strength')->default(0.7);
            $table->boolean('use_keyword')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('my_ai_images', function (Blueprint $table) {
            $table->dropColumn(['prompt_strength', 'use_keyword']);
        });
    }
};