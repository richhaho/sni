<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyBondPdfFieldOnPartiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_parties', function (Blueprint $table) {
            $table->dropColumn('bond_pdf');
        });
        Schema::table('job_parties', function (Blueprint $table) {
            $table->binary('dond_pdf')->nullable()->after('contact_id');
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
            $table->dropColumn('bond_pdf');
        });
        Schema::table('job_parties', function (Blueprint $table) {
            $table->binary('dond_pdf')->after('contact_id');
        });
    }
}
