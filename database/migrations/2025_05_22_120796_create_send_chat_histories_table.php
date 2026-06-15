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
        Schema::create('send_chat_histories', function (Blueprint $table) {
            $table->id();
            $table->string('dify_conversation_id');
            $table->integer('project_id');
            $table->boolean('is_dify_backup')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('send_chat_histories');
    }
};
