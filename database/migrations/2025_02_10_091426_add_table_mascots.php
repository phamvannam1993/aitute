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
        Schema::create('mascots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chatbot_config_id')->constrained('chatbot_config')->onDelete('cascade'); // Khóa ngoại
            $table->string('text')->nullable(); 
            $table->string('img')->nullable(); 
            $table->boolean('is_show')->default(true);
            $table->enum('type', ['start', 'feedback', 'answer', 'stop']); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mascots');
    }
};