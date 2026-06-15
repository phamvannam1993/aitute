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
        Schema::create('short_videos', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('video_id')->default(0);
            $table->string('task_id')->nullable();
            $table->text('thumbnail')->nullable();
            $table->integer('scene_number');
            $table->text('description')->nullable();
            $table->text('audio_description')->nullable();
            $table->boolean('is_upload_audio')->nullable();
            $table->text('s3_url')->nullable();
            $table->text('transcription_url')->nullable();
            $table->string('model')->nullable();
            $table->string('voice_over')->nullable();
            $table->text('image_url')->nullable();
            $table->text('audio_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('short_videos');
    }
};
