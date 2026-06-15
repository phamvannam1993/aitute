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
        Schema::table('facebook_configs', function (Blueprint $table) {
            $table->string('page_picture', 1024)->nullable()->after('page_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facebook_configs', function (Blueprint $table) {
            $table->dropColumn('page_picture');
        });
    }
};
