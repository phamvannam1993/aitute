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
        Schema::create('my_ai_image_template_categories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('my_ai_image_collection_id');
            $table->string('title');
            $table->string('slug')->nullable();
            $table->integer('month')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_ai_image_template_categories');
    }
};
