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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->bigInteger('coupon_discount_vnd')->nullable();
            $table->bigInteger('total_price')->default(0);
            $table->bigInteger('total_point')->default(0);
            $table->string('status');
            $table->timestamp('order_at')->nullable();
            $table->timestamp('cancel_at')->nullable();
            $table->timestamp('finish_at')->nullable();
            $table->string('request_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
