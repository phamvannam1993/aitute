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
        Schema::create('occupations', function (Blueprint $table) {
            $table->id(); // ID tự động tăng
            $table->string('code'); // Mã ngành nghề
            $table->unsignedBigInteger('user_id');
            $table->string('name'); // Tên ngành nghề
            $table->text('description')->nullable(); // Mô tả có thể null
            $table->string('image')->nullable(); // Đường dẫn ảnh có thể null
            $table->timestamp('deleted_at')->nullable(); // Xóa mềm
            $table->timestamps(); // Thêm cột created_at và updated_at
            $table->index('user_id');
            $table->index('code');
            $table->index('name');
            $table->index('deleted_at');
            // Tạo composite index cho 'code', 'ten_nganh_nghe', và 'deleted_at'
            $table->index(['user_id', 'code', 'name', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('occupations');
    }
};
