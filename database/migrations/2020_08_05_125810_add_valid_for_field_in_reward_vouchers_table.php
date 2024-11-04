<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddValidForFieldInRewardVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reward_vouchers', function (Blueprint $table) {
            $table->integer('valid_for')->after('voucher_type')->default(0)->comment('0 > All Users, 1 > Purple Users, 2 > Gold Users');
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
            $tbale->dropColumn('valid_for');
        });
    }
}
