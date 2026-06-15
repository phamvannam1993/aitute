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
        Schema::create('facebook_contents', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0);
            $table->text('title');
            $table->text('content');
            $table->text('images')->nullable();
            $table->text('video_url')->nullable();
            $table->integer('facebook_fanpage_id')->default(0);
            $table->timestamp('post_date')->nullable();
            $table->boolean('is_video')->default(false);
            $table->integer('status')->default(0);
            $table->boolean('is_post')->default(0);
            $table->integer('project_id')->default(0);
            $table->text('goal')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facebook_contents');
    }
};
