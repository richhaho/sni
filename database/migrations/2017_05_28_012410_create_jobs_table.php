<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type', ['public', 'private'])->default('public');
            $table->string('number',20)->nullable();
            $table->string('status',100)->nullable();
            $table->string('name',200)->nullable();
            $table->string('address_1',200)->nullable();
            $table->string('address_2',200)->nullable();
            $table->string('address_corner',200)->nullable();
            $table->string('city',200)->nullable();
            $table->string('county',200)->nullable();
            $table->string('state',50)->nullable();
            $table->string('zip',50)->nullable();
            $table->string('country',50)->nullable();
            $table->date('started_at')->nullable();
            $table->double('contract_amount',15,2)->default(0);
            $table->longText('default_materials')->nullable();
            $table->longText('legal_description')->nullable();
            $table->longText('folio_number')->nullable();
            $table->double('unpaid_balance',15,2)->default(0);
            $table->double('interest_charged',15,2)->default(0);
            
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
        Schema::dropIfExists('jobs');
    }
}
