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
        Schema::create('ai_assistant_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('ai_assistant_id');
            $table->string('type');
            $table->text('prompt');
            $table->text('media_link')->nullable();
            $table->text('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_assistant_histories');
    }
};
