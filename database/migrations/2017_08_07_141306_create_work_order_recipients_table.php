<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkOrderRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_order_recipients', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('party_id');
            $table->unsignedInteger('work_order_id');
            $table->string('firm_name')->nullable();
            $table->string('attention_name')->nullable();
            $table->string('address')->nullable();
            $table->string('mailing_type')->nullable();
            $table->string('barcode')->nullable();
            $table->tinyInteger('return_receipt')->default(0);
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
        Schema::dropIfExists('work_order_recipients');
    }
}
