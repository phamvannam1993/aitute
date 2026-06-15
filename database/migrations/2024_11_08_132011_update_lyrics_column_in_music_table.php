<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('music', function (Blueprint $table) {
            $table->longText('lyrics')->change(); // Đổi từ STRING sang LONGTEXT
        });
    }

    public function down()
    {
        Schema::table('music', function (Blueprint $table) {
            $table->string('lyrics', 255)->change(); // Trở lại kích thước ban đầu nếu cần
        });
    }
};
