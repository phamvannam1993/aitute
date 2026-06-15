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
        Schema::create('pebblely_videos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('theme');
            $table->string('input_url');
            $table->string('s3_url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pebblely_videos');
    }
};
