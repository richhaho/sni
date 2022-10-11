<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeBondPdfFieldTypeJobpartiesTable extends Migration
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
            $table->string('bond_pdf')->nullable()->after('contact_id');
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
            $table->binary('bond_pdf')->nullable()->after('contact_id');
        });
    }
}
