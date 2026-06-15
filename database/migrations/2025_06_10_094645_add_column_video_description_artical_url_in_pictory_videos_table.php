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
        Schema::table('pictory_videos', function (Blueprint $table) {
//            $table->text('video_description')->nullable();
//            $table->text('article_url')->nullable();
//            $table->tinyInteger('is_upload_audio')->default(0);
//            $table->text('final_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pictory_videos', function (Blueprint $table) {
            //
        });
    }
};
