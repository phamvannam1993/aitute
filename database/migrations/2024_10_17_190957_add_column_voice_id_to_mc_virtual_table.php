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
        Schema::table('mc_virtuals', function (Blueprint $table) {
            $table->bigInteger('ai_generated_media_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mc_virtuals', function (Blueprint $table) {
            $table->dropColumn(['ai_generated_media_id']);
        });
    }
};
