<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->bigInteger('transaction_id')->nullable();
            $table->foreignId('reward_voucher_id')->constrained('reward_vouchers');
            $table->string('amount')->nullable();
            $table->string('coupon_code')->nullable();
            $table->string('qr_path')->nullable();
            $table->foreignId('scanned_by')->nullable()->constrained('users');
            $table->tinyInteger('is_redeemed')->default(0);
            $table->string('redeemed_on')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
