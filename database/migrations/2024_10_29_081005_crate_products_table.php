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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('month');
            $table->integer('credit');
            $table->decimal('price', 10, 0);
            $table->decimal('price_actual', 10, 2);
            $table->string('currency', 3)->default('vnd');
            $table->integer('discount');
            $table->text('description');
            $table->tinyInteger('recommend');
            $table->boolean('is_delete')->nullable()->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
