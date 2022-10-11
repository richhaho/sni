<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsJobPartiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_parties', function (Blueprint $table) {
            $table->string('copy_type')->after('leaseholder_bookpage_number')->nullable();
            $table->string('leaseholder_type')->after('landowner_lien_prohibition')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_parties', function (Blueprint $table) {
             $table->dropColumn('copy_type');
            $table->dropColumn('leaseholder_type');
        });
    }
}
