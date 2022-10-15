<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FilefieldsMissingBondJobpartiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_parties', function (Blueprint $table) {
            $table->unsignedInteger('bond_pdf_filename_size')->default(0)->after('bond_pdf');
            $table->string('bond_pdf_filename_mime')->nullable()->after('bond_pdf');
            $table->string('bond_pdf_filename')->nullable()->after('bond_pdf');
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
            $table->dropColumn('bond_pdf_filename');
            $table->dropColumn('bond_pdf_filename_mime');
            $table->dropColumn('bond_pdf_filename_size');
        });
    }
}
