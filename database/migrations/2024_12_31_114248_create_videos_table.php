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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->text('image_url')->nullable();
            $table->text('description')->nullable();
            $table->integer('duration');
            $table->string('ratio')->nullable();
            $table->text('audio_url')->nullable();
            $table->text('thumbnail')->nullable();
            $table->boolean('is_transcription')->nullable();
            $table->text('transcription_url')->nullable();
            $table->text('error_msg')->nullable(); 
            $table->text('s3_url')->nullable();
            $table->boolean('is_upload_audio')->nullable();
            $table->text('s3_url_video_audio')->nullable();
            $table->text('s3_url_video_image')->nullable();
            $table->text('s3_url_video_result')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
