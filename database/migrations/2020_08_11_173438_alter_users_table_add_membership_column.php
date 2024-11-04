<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersTableAddMembershipColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('gold_expiring_date', 0)->after('is_project_acai')->nullable();
            $table->dateTime('gold_activation_date', 0)->after('is_project_acai')->nullable();
            $table->tinyInteger('membership_type')->after('is_project_acai')->default(1)->comment('1 > purple, 2 Gold');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // $table->dropColumn('membership_type');
            $table->dropColumn('gold_activation_date');
            $table->dropColumn('gold_expiring_date');
        });
    }
}
