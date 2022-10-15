<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkOrderPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_order_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('work_order_id');
            $table->unsignedInteger('job_id');
            $table->string('reference');
            $table->string('description');
            $table->double('amount', 15, 4);
            $table->dateTime('payed_at')->nullable();
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
        Schema::dropIfExists('work_order_payments');
    }
}
