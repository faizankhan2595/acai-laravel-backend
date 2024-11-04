<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('created_by')->default(0)->after('password')->comment('0 > Admin');
            $table->tinyInteger('account_status')->default(0)->after('password');
            $table->tinyInteger('mobile_verified')->default(0)->after('password');
            $table->string('avatar')->nullable()->after('password');
            $table->string('dob')->nullable()->after('password');
            $table->tinyInteger('gender')->default(0)->after('password');
            $table->string('otp')->nullable()->default(NULL)->after('password');
            $table->string('mobile_number')->unique()->nullable()->default(NULL)->after('password');
            $table->softDeletes();
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
            $table->dropColumn('dob');
            $table->dropColumn('otp');
            $table->dropColumn('avatar');
            $table->dropColumn('mobile_number');
            $table->dropColumn('mobile_verified');
            $table->dropColumn('account_status');
            $table->dropColumn('gender');
            $table->dropColumn('deleted_at');
            $table->dropColumn('created_by');
        });
    }
}
