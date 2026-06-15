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
        Schema::create('pdf_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('source_id');
            $table->json('summarize')->nullable();
            $table->text('pdf_url')->nullable();
            $table->string('short_description')->nullable();
            $table->json('keywords')->nullable();
            $table->json('greeting')->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pdf_sources');
    }
};
