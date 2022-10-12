<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobPartiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_parties', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type', ['customer', 'general_contractor', 'bond', 'landowner', 'leaseholder', 'copy_recipient'])->default('customer');
            $table->unsignedInteger('entity_id');
            $table->binary('bond_pdf');
            $table->datetime('bond_date')->nullable();
            $table->double('bond_amount', 15, 2)->nullable();
            $table->string('bond_bookpage_number', 80)->nullable();
            $table->string('landowner_deed_number', 80)->nullable();
            $table->longText('landowner_lien_prohibition')->nullable();
            $table->longText('leaseholder_lease_agreement')->nullable();
            $table->string('leaseholder_lease_number', 80)->nullable();
            $table->string('leaseholder_bookpage_number', 80)->nullable();
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
        Schema::dropIfExists('job_parties');
    }
}
