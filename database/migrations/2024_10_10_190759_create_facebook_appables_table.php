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
        Schema::create('facebook_appables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facebook_app_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('facebook_appable_id');
            $table->string('facebook_appable_type');
            $table->index(['facebook_appable_type', 'facebook_appable_id'], 'facebook_appable_index');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facebook_appables');
    }
};
