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
        Schema::create('ai_project_experts', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->text('title');
            $table->text('description')->nullable();
            $table->text('image_product')->nullable();
            $table->text('files')->nullable();
            $table->text('url')->nullable();
            $table->text('answers')->nullable();
            $table->text('expert_info')->nullable();
            $table->text('buss_info')->nullable();
            $table->text('analysis_sections')->nullable();
            $table->text('communication_strategy')->nullable();
            $table->text('results')->nullable();
            $table->text('model_product')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_project_experts');
    }
};
