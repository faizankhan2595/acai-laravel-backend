<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRewardVoucherUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reward_voucher_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reward_voucher_id')->constrained('reward_vouchers')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->datetime('valid_from',0)->nullable();
            $table->datetime('valid_till',0)->nullable();
            $table->datetime('redeemed_on',0)->nullable();
            $table->tinyInteger('is_redeemed')->default(0);
            $table->tinyInteger('redemption_count')->default(0);
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
        Schema::dropIfExists('reward_voucher_user');
    }
}
