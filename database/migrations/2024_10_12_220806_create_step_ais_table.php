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
        Schema::create('step_ais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_assistant_id')->constrained('ai_assistants')->onDelete('cascade');
            $table->string('step_name');
            $table->text('step_description')->nullable();
            $table->integer('position')->default(1);
            $table->timestamps();

            $table->index('ai_assistant_id');
            $table->index(['ai_assistant_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('step_ais');
    }
};
