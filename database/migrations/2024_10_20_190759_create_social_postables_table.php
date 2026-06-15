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
        Schema::create('social_postables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_post_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('attempts')->default(0);
            $table->string('platform_post_id')->nullable()->comment("platform: facebook, tiktok,...");
            $table->timestamp('scheduled_at')->nullable()->comment("The time that the post is scheduled to be published");
            $table->timestamp('published_at')->nullable()->comment("The real time that the post was published");
            $table->unsignedBigInteger('social_postable_id');
            $table->string('social_postable_type');
            $table->index(['social_postable_type', 'social_postable_id'], 'social_postable_index');
            $table->timestamps();
        });

        if (Schema::hasColumn('social_posts', 'platforms')) {
            Schema::table('social_posts', function (Blueprint $table) {
                $table->dropColumn(['platforms', 'scheduled_at', 'published_at', 'attempts']);
            });
        }
    }

    /**
     * Reverse the migrations.F
     */
    public function down(): void
    {
        Schema::dropIfExists('social_postables');
    }
};
