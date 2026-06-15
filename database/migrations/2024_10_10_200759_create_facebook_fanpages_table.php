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
        Schema::create('facebook_fanpages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facebook_app_id')->constrained()->onDelete('cascade');
            $table->string('page_id');
            $table->string('page_access_token')->nullable();
            $table->timestamp('page_access_token_expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facebook_fanpages');
    }
};
