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
        Schema::create('facebook_apps', function (Blueprint $table) {
            $table->id();
            $table->string('app_id');
            $table->string('app_secret');
            $table->string('user_access_token')->nullable();
            $table->timestamp('user_access_token_expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facebook_apps');
    }
};
