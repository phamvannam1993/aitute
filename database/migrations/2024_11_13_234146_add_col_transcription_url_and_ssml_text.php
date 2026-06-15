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
        // add column transcription_url and ssml_text to table ai_generated_medias
        Schema::table('ai_generated_medias', function (Blueprint $table) {
            $table->string('transcription_url')->nullable();
            $table->text('ssml_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_generated_medias', function (Blueprint $table) {
            $table->dropColumn('transcription_url');
            $table->dropColumn('ssml_text');
        });
    }
};
