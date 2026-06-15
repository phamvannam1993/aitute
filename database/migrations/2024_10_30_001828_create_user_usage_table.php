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
        Schema::create('user_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('assistant_type'); // Loại trợ lý ảo (text, image, audio, mc)
            $table->integer('usage_count')->default(0); // Số lần đã sử dụng
            $table->timestamps();
            $table->index(['user_id', 'assistant_type']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_usage', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'assistant_type']);
        });
        Schema::dropIfExists('user_usage');
    }
};
