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
        Schema::create('usage_limits', function (Blueprint $table) {
            $table->id();
            $table->string('assistant_type'); // Loại trợ lý ảo (text, image, audio, mc ảo)
            $table->integer('default_limit')->default(0); // Số lần sử dụng mặc định
            $table->timestamps();
            $table->index('assistant_type');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usage_limits', function (Blueprint $table) {
            $table->dropIndex(['assistant_type']);
        });
        Schema::dropIfExists('usage_limits');
    }
};
