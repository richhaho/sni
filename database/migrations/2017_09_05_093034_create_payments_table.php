<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoices_id')->nullable();
            $table->string('type',50)->nullable();
            $table->decimal('amount')->default(0);
            $table->unsignedInteger('client_id')->nullable();
            $table->string('reference',50)->nullable();
            $table->string('gateway',50)->nullable();
            $table->string('transaction_status',50)->nullable();
            $table->text('log_result')->nullable();
            $table->unsignedInteger('user_id')->nullable();
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
        Schema::dropIfExists('payments');
    }
}
