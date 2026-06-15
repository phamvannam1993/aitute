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
        Schema::table('facebook_apps', function (Blueprint $table) {
            $table->renameColumn('app_id', 'facebook_user_id');
            $table->renameColumn('app_secret', 'facebook_user_name');
        });

        Schema::rename('facebook_apps', 'facebooks');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('facebooks', 'facebook_apps');

        Schema::table('facebook_apps', function (Blueprint $table) {
            $table->renameColumn('facebook_user_id', 'app_id');
            $table->renameColumn('facebook_user_name', 'app_secret');
        });
    }
};
