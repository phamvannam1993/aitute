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
        Schema::create('operations', function (Blueprint $table) {
            $table->id(); // Tạo trường ID
            $table->string('code'); // Mã ngành nghề
            $table->unsignedBigInteger('occupation_id'); // Tạo trường liên kết với ngành nghề (occupation)
            $table->unsignedBigInteger('user_id');
            $table->string('name'); // Tên nghiệp vụ
            $table->text('description')->nullable(); // Mô tả (có thể để null)
            $table->string('image')->nullable(); // Đường dẫn đến ảnh (có thể để null)
            $table->timestamps(); // Tạo các trường created_at và updated_at
            $table->timestamp('deleted_at')->nullable(); // Xóa mềm

            // Thiết lập khoá ngoại cho occupation_id, liên kết với bảng occupations
            $table->foreign('occupation_id')->references('id')->on('occupations')->onDelete('cascade');
            $table->index('user_id');
            $table->index('code');
            $table->index('name');
            $table->index('deleted_at');

            // Tạo composite index cho 'code', 'Tên nghiệp vụ', và 'deleted_at'
            $table->index(['user_id', 'code', 'name', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operations');
    }
};
