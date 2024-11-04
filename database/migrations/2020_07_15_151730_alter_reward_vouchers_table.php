<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRewardVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reward_vouchers', function (Blueprint $table) {
            $table->string('discount_subtitle')->after('price')->nullable();
            $table->string('discount_title')->after('price')->nullable();
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
            $table->dropColumn('discount_title');
            $table->dropColumn('discount_subtitle');
        });
    }
}
