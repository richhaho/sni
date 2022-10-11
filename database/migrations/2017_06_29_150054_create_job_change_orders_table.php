<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobChangeOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_change_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('job_id')->nullable();
            $table->string('number',50)->nullable();
            $table->timestamp('added_on')->nullable();
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
        Schema::dropIfExists('job_change_orders');
    }
}
