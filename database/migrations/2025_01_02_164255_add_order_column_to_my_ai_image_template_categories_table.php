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
        Schema::table('my_ai_image_template_categories', function (Blueprint $table) {
            $table->integer('order')->nullable()->after('slug');
        });
    }

    public function down()
    {
        Schema::table('my_ai_image_template_categories', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};