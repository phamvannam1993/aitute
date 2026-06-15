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
        Schema::create('activation_logs', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable(); // Email người kích hoạt
            $table->string('key'); // Key đã kích hoạt
            $table->string('device_info')->nullable(); // Thông tin thiết bị
            $table->string('device_summary')->nullable(); // Thông tin thiết bị
            $table->string('ip')->nullable(); // Thông tin thiết bị
            $table->timestamp('activated_at'); // Thời gian kích hoạt
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activation_logs');
    }
};
