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
            $table->boolean('is_upload_audio')->default(false);
            $table->text('s3_url_video_audio')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mc_virtuals', function (Blueprint $table) {
            //
        });
    }
};
