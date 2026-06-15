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
        Schema::create('ai_assistants', function (Blueprint $table) {
            $table->id();
            // columns admin_id, name, type, description, thumbnall, prompt, created_at, updated_at, deleted_at
            $table->foreignId('admin_id')->constrained('admins');
            $table->string('name');
            $table->string('type');
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->text('prompt')->nullable();
            $table->softDeletes();
            $table->timestamps();
       });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_assistants');
    }
};
