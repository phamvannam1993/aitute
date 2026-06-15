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
        Schema::table('facebooks', function (Blueprint $table) {
            $table->string('facebook_user_id')->nullable()->change();
            $table->string('facebook_user_name')->nullable()->change();
            $table->text('user_access_token')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
