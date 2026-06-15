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
        Schema::table('pictory_videos', function (Blueprint $table) {
            // Change 'renderParams' column type to LONGTEXT (for more data storage)
            $table->longText('renderParams')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pictory_videos', function (Blueprint $table) {
            // Roll back the change, revert to TEXT type
            $table->text('renderParams')->nullable()->change();
        });
    }

};
