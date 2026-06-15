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
        Schema::table('ai_assistant_histories', function (Blueprint $table) {
            Schema::table('ai_assistant_histories', function (Blueprint $table) {
                $table->foreignId('ai_text_conversation_id')
                    ->nullable()
                    ->after('ai_assistant_id') // Đặt vị trí của cột mới
                    ->constrained('ai_text_conversations')
                    ->onDelete('set null');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_assistant_histories', function (Blueprint $table) {
            $table->dropForeign(['ai_text_conversation_id']);
            $table->dropColumn('ai_text_conversation_id');
        });
    }
};
