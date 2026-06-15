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
        Schema::table('message_histories', function (Blueprint $table) {
            $table->string('user_page_id')->nullable();
            $table->longText('text_content')->nullable();
            $table->integer('price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('message_histories', function (Blueprint $table) {
            $table->dropColumn('user_page_id');
            $table->dropColumn('text_content');
            $table->dropColumn('price');
        });
    }
};