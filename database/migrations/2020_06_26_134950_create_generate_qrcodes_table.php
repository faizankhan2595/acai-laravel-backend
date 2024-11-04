<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGenerateQrcodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('generate_qr_codes', function (Blueprint $table) {
            $table->id();
            $table->string('reference_id')->nullable();
            $table->foreignId('generated_by')->constrained('users');
            $table->foreignId('scanned_by')->nullable()->constrained('users');
            $table->bigInteger('transaction_id')->nullable();
            $table->string('amount')->nullable();
            $table->string('code')->nullable();
            $table->string('qr_path')->nullable();
            $table->tinyInteger('is_scanned')->default(0);
            $table->string('scanned_on')->nullable();
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
        Schema::dropIfExists('generate_qr_codes');
    }
}
