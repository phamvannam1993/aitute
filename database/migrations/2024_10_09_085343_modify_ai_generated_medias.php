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
        Schema::table('ai_generated_medias', function (Blueprint $table) {
            $table->string('style')->nullable()->change();
            $table->string('artist')->nullable()->change();
            $table->integer('width')->nullable()->change();
            $table->integer('height')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_generated_medias', function (Blueprint $table) {
            $table->string('style')->nullable(false)->change();
            $table->string('artist')->nullable(false)->change();
            $table->integer('width')->nullable(false)->change();
            $table->integer('height')->nullable(false)->change();
        });
    }
};