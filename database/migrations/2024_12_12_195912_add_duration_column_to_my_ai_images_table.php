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
        Schema::table('my_ai_images', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('id')->default('1'); // Thêm user_id
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamp('start_time')->nullable()->after('status');
            $table->timestamp('end_time')->nullable()->after('start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('my_ai_images', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time', 'user_id']);
            $table->dropForeign(['user_id']);
        });
    }
};
