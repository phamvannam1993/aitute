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
        Schema::create('agents', function (Blueprint $table) {
            $table->id(); // ID đại lý
            $table->string('name'); // Tên đại lý
            $table->string('email')->nullable(); // Email đại lý
            $table->string('phone')->nullable(); // Số điện thoại đại lý
            $table->string('address')->nullable(); // Địa chỉ đại lý
            $table->timestamps();
        });

        // Thêm cột agent_id vào bảng aff_keys để liên kết đại lý với key
        Schema::table('aff_keys', function (Blueprint $table) {
            $table->unsignedBigInteger('agent_id')->nullable()->after('config_aff_id'); // Khóa ngoại
            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aff_keys', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->dropColumn('agent_id');
        });

        Schema::dropIfExists('agents');
    }
};
