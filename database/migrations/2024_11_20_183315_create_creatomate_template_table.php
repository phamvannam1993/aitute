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
        Schema::create('creatomate_templates', function (Blueprint $table) {
            $table->id();
            $table->string('template_id');
            $table->string('title');
            $table->text('template_thumbnail')->nullable();
            $table->text('structure');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creatomate_templates');
    }
};
