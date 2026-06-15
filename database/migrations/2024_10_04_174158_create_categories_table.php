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
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); // Tạo trường ID
            $table->string('name'); // Tên lĩnh vực
            $table->text('description')->nullable(); // Mô tả có thể để null
            $table->string('image')->nullable(); // Hình ảnh đại diện
            $table->timestamps(); // Tạo created_at và updated_at
            $table->timestamp('deleted_at')->nullable(); // Xóa mềm
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
