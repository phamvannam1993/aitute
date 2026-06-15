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
        Schema::table('lipsync', function (Blueprint $table) {
            $table->text('audio')->change();
            $table->text('video')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lipsync', function (Blueprint $table) {
            $table->string('audio', 255)->change();
            $table->string('video', 255)->change();
        });
    }
};
