<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ai_assistants', function (Blueprint $table) {
            $table->boolean('is_public')->default(true)->comment('Admin cho phép public AI hay không');
        });
    }

    public function down()
    {
        Schema::table('ai_assistants', function (Blueprint $table) {
            $table->dropColumn('is_public');
        });
    }
};
