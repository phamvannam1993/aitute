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
        Schema::create('ai_text_conversations_admin', function (Blueprint $table) {
            $table->id(); // Tự tăng ID
            $table->json('messages'); // Cột lưu trữ tin nhắn dưới dạng JSON
            $table->timestamps(); // Cột created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_text_conversations_admin');
    }
};
