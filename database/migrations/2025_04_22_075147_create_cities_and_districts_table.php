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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->char('code', 3);
            $table->string('code_name', 128)->nullable();
            $table->string('latitude', 128);
            $table->string('longitude', 128);
            $table->timestamps();
        });
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->integer('city_id');
            $table->string('name', 50);
            $table->char('code', 3);
            $table->string('code_name', 128)->nullable();
            $table->string('latitude', 128)->nullable();
            $table->string('longitude', 128)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('districts');
        Schema::dropIfExists('cities');
    }
};
