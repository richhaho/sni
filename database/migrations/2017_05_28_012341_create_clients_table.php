<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_name')->nullable();
            $table->string('title',20)->nullable();
            $table->string('first_name',80);
            $table->string('last_name',80);
            $table->string('address_1',200)->nullable();
            $table->string('address_2',200)->nullable();
            $table->string('city',200)->nullable();
            $table->string('state',50)->nullable();
            $table->string('zip',50)->nullable();
            $table->string('country',50)->nullable();
            $table->string('phone',20)->nullable();
            $table->string('mobile',20)->nullable();
            $table->string('fax',20)->nullable();
            $table->string('email');
            $table->enum('gender', ['female', 'male'])->default('female');
            $table->enum('billing_type', ['attime', 'invoiced'])->default('attime');
            $table->integer('parent_client_id')->unsigned()->default(0);
            $table->integer('client_user_id')->unsigned()->default(0);
            $table->enum('print_method', ['sni', 'client'])->default('sni');
            $table->enum('send_certified', ['green', 'nongreen'])->default('green');
            $table->longText('default_materials')->nullable();
            $table->double('interest_rate', 5,2)->default(0);
           
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
        Schema::dropIfExists('clients');
    }
}
