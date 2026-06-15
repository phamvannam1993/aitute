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
        Schema::table('mc_virtuals', function (Blueprint $table) {
            $table->integer('duration')->nullable()->comment('Độ dài video tính bằng giây');
            $table->json('info_json')->nullable()->comment('Thông tin chi tiết của task,clip từ D-ID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mc_virtuals', function (Blueprint $table) {
            $table->dropColumn('duration');
            $table->dropColumn('info_json');
        });
    }
};
