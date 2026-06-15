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
        Schema::create('user_sale', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->unsignedBigInteger('conversation_id')->nullable();
            $table->unsignedBigInteger('message_id')->nullable();
            $table->string('fb_message_psid')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->integer('age')->nullable();
            $table->json('meta')->nullable();
            $table->datetime('order_date')->nullable();
            $table->dateTime('appointment')->nullable();
            $table->longText('treatment')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('conversation_id')->references('id')->on('ai_tu_te_conversations')->onDelete('set null');
            $table->foreign('message_id')->references('id')->on('ai_tu_te_messages')->onDelete('set null');
            $table->boolean('isDelete')->default(false);
            $table->enum('conversation_status', ['cool', 'hot'])->nullable();
        });

        Schema::create('user_sale_contact_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sale_contact_statuses');
        try {
            Schema::table('user_sale', function (Blueprint $table) {
                $table->dropForeign('user_sale_conversation_id_foreign');
                $table->dropForeign('user_sale_message_id_foreign');
                $table->dropForeign('user_sale_user_id_foreign');
            });
        } catch (\Exception $e) {
        }
        Schema::dropIfExists('user_sale');
    }
};
