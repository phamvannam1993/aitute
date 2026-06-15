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
        Schema::create('social_posts', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->text('content');
            $table->json('medias')->nullable();
            $table->text('video')->nullable();
            $table->json('platforms');
            $table->timestamp('scheduled_at')->comment("The time that the post is scheduled to be published");
            $table->timestamp('published_at')->nullable()->comment("The real time that the post was published");
            $table->tinyInteger('attempts')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_posts');
    }
};
