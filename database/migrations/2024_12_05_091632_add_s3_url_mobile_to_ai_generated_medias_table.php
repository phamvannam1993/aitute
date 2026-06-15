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
        Schema::table('ai_generated_medias', function (Blueprint $table) {
            $table->string('s3_url_mobile')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_generated_medias', function (Blueprint $table) {
            $table->dropColumn('s3_url_mobile');
        });
    }
};
