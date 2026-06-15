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
            if (!Schema::hasColumn('ai_generated_medias', 'task_id')) {
                $table->string('task_id')->nullable();
            }
            if (!Schema::hasColumn('ai_generated_medias', 'duration')) {
                $table->string('duration')->nullable();
            }
            if (!Schema::hasColumn('ai_generated_medias', 'model')) {
                $table->string('model')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_generated_medias', function (Blueprint $table) {
            $table->dropColumn('task_id');
            $table->dropColumn('duration');
            $table->dropColumn('model');
        });
    }
};
