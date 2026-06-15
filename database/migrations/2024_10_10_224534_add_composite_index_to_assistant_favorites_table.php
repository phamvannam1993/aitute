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
        Schema::table('assistant_favorites', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('ai_assistant_id');
            // Tạo composite index cho user_id và ai_assistant_id
            $table->index(['user_id', 'ai_assistant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assistant_favorites', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['ai_assistant_id']);
            // Xóa composite index khi rollback
            $table->dropIndex(['user_id', 'ai_assistant_id']);
        });
    }
};
