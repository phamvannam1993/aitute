<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAiGeneratedBannerPostersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ai_generated_banner_posters', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('user_id'); // User reference
            $table->text('description')->nullable(); // Description of the banner/poster
            $table->integer('width'); // Width of the banner/poster
            $table->integer('height'); // Height of the banner/poster
            $table->string('s3_url'); // URL for the stored image
            $table->enum('type', ['banner', 'poster']); // Type of the item
            $table->timestamps(); // created_at and updated_at

            // Foreign key relationship
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
            // Add composite index for user_id and type
            $table->index(['user_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ai_generated_banner_posters');
    }
}
