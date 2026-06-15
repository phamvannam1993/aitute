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
        Schema::create('assistant_favorites', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            // user_id, ai_assistant_id
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('ai_assistant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assistant_favorites');
    }
};
