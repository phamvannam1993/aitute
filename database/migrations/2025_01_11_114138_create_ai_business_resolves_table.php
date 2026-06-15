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
        Schema::create('ai_business_resolves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('project_id')->constrained('ai_business_projects')->onDelete('cascade');
            $table->string('type'); // image, video, audio...
            $table->text('s3_url')->nullable();
            $table->text('path')->nullable();
            $table->longText('prompt')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Single indexes
            $table->index('user_id');
            $table->index('project_id');

            // Composite index cho user_id và project_id
            $table->index(['user_id', 'project_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_business_resolves');
    }
};
