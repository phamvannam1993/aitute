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
        Schema::create('key_transfer_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_agent_id');
            $table->unsignedBigInteger('to_agent_id');
            $table->unsignedBigInteger('config_aff_id');
            $table->integer('number_of_keys');
            $table->timestamp('transferred_at');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('from_agent_id')
                ->references('id')
                ->on('agents')
                ->onDelete('cascade');

            $table->foreign('to_agent_id')
                ->references('id')
                ->on('agents')
                ->onDelete('cascade');

            $table->foreign('config_aff_id')
                ->references('id')
                ->on('config_aff')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('key_transfer_history');
    }
};
