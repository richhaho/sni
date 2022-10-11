<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePartiesTypesJobParties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('job_parties', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        Schema::table('job_parties', function (Blueprint $table) {
            $table->string('type')->nullable()->after('job_id');
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
            $table->dropColumn('type');
        });
        Schema::table('job_parties', function (Blueprint $table) {
            $table->string('type')->nullable()->after('job_id');
        });
    }
}
