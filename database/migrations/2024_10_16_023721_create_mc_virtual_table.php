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
        Schema::create('mc_virtuals', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->text('avatar_url');
            $table->string('voice_id');
            $table->string('mc_virtual_id');
            $table->text('content');
            $table->string('status');
            $table->text('result_url')->nullable();
            $table->string('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mc_virtuals');
    }
};
