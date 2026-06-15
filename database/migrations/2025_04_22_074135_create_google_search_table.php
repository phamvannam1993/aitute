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
        Schema::create('google_search', function (Blueprint $table) {
            $table->id();
            $table->integer('position')->nullable();
            $table->string('title')->nullable();
            $table->string('address')->nullable();
            $table->double('latitude')->default(0);
            $table->double('longitude')->default(0);
            $table->integer('rating')->nullable();
            $table->integer('ratingCount')->nullable();
            $table->string('type')->nullable();
            $table->text('types')->nullable();
            $table->string('phoneNumber')->nullable();
            $table->text('openingHours')->nullable();
            $table->text('thumbnailUrl')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->text('place_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('district_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_search');
    }
};
