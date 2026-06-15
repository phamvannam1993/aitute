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
        Schema::create('ai_assistant_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_assistant_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable(); // Name của tiêu chí là g
            $table->string('type'); // Loại tiêu chí: 'input' hoặc 'select'
            $table->string('label')->nullable(); // Nhãn cho tiêu chí
            $table->boolean('multiple')->default(false); // Thiết lập cho phép chọn nhiều hay không (chỉ dành cho select box)
            $table->text('value')->nullable(); // Lưu trữ giá trị của tiêu chí: chuỗi (input) hoặc JSON (select)
            $table->string('selectedValue')->nullable(); // Default của selected
            $table->text('placeholder')->nullable();
            $table->timestamps();
            $table->index('ai_assistant_id');
            $table->index(['ai_assistant_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_assistant_criteria');
    }
};
