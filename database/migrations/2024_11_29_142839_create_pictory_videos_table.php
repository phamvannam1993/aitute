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
        Schema::create('pictory_videos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->text(column: 'job_id')->nullable();
            $table->text('title');
            $table->text('renderParams')->nullable();
            $table->text('preview_url')->nullable();
            $table->text('s3_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pictory_videos');
    }
};
