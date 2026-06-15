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
        Schema::table('facebook_appables', function (Blueprint $table) {
            $table->renameColumn('facebook_app_id', 'facebook_id');
            $table->renameColumn('facebook_appable_id', 'facebookable_id');
            $table->renameColumn('facebook_appable_type', 'facebookable_type');
        });

        Schema::rename('facebook_appables', 'facebookables');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('facebookables', 'facebook_appables');

        Schema::table('facebook_appables', function (Blueprint $table) {
            $table->renameColumn('facebook_id', 'facebook_app_id');
            $table->renameColumn('facebookable_id', 'facebook_appable_id');
            $table->renameColumn('facebookable_type', 'facebook_appable_type');
        });
    }
};
