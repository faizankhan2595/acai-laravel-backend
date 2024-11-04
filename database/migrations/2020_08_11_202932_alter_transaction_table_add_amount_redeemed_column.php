<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTransactionTableAddAmountRedeemedColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('point_transactions', function (Blueprint $table) {
            $table->integer('points_available')->after('transaction_value')->nullable();
            $table->integer('points_redeemed')->default(0)->after('transaction_value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('point_transactions', function (Blueprint $table) {
            $table->dropColumn('points_available');
            $table->dropColumn('points_redeemed');
        });
    }
}
