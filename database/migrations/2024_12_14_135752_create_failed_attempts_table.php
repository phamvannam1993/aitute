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
        Schema::create('failed_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address')->nullable(); // Theo dõi qua IP
            $table->integer('attempts')->default(0); // Số lần nhập sai
            $table->timestamp('locked_until')->nullable(); // Thời gian khóa
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failed_attempts');
    }
};
