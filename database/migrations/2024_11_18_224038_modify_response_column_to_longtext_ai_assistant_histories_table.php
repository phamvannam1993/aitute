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
        Schema::table('ai_assistant_histories', function (Blueprint $table) {
            $table->longText('response')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('ai_assistant_histories', function (Blueprint $table) {
            $table->text('response')->nullable()->change();
        });
    }
};
