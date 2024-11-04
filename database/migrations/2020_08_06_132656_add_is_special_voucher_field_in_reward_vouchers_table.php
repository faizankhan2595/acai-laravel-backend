<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSpecialVoucherFieldInRewardVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reward_vouchers', function (Blueprint $table) {
            $table->integer('is_special_voucher')->after('voucher_type')->default(0)->comment('0 > Not Special, 1 > Special Voucher');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reward_vouchers', function (Blueprint $table) {
            $table->dropColumn('is_special_voucher');
        });
    }
}
