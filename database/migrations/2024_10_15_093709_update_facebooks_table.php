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
        Schema::table('facebooks', function ($table) {
            $table->text('user_access_token')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
