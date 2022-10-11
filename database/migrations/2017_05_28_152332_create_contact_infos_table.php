<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_infos', function (Blueprint $table) {
            $table->increments('id');
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
            $table->string('email')->nullable();
            $table->tinyInteger('primary')->default(0);
            $table->unsignedInteger('entity_id');
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
        Schema::dropIfExists('contact_infos');
    }
}
