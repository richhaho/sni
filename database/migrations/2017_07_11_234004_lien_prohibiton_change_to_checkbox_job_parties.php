<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LienProhibitonChangeToCheckboxJobParties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_parties', function (Blueprint $table) {
            $table->dropColumn('landowner_lien_prohibition');
        });
        Schema::table('job_parties', function (Blueprint $table) {
            $table->tinyInteger('landowner_lien_prohibition')->default(0)->after('landowner_deed_number');
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
            $table->dropColumn('landowner_lien_prohibition');
        });
        Schema::table('job_parties', function (Blueprint $table) {
            $table->text('landowner_lien_prohibition')->nullable()->after('landowner_deed_number');
        });
    }
}
