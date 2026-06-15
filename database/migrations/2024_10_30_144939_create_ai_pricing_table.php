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
        Schema::create('ai_pricings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('model_code')->unique();
            $table->string('type');
            $table->double('cost_normal')->nullable();
            $table->double('cost_input')->nullable();
            $table->double('cost_output')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_pricings');
    }
};
