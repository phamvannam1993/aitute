<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryIdToOccupationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('occupations', function (Blueprint $table) {
            // Thêm cột liên kết 'category_id' với bảng 'categories'
            $table->unsignedBigInteger('category_id')->nullable()->after('id')->default(1);
            // Tạo index cho 'category_id'
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('occupations', function (Blueprint $table) {
            $table->dropColumn('category_id');
        });
    }
}
