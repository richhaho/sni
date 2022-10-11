<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobPaymentHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_payment_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('job_id')->nullable();
            $table->timestamp('payed_on')->nullable();
            $table->text('description')->nullable();
            $table->decimal('amount')->defautl(0);
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
        Schema::dropIfExists('job_payment_histories');
    }
}
