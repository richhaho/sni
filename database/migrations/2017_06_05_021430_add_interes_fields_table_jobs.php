<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInteresFieldsTableJobs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->double('interest_rate', 5,2)->default(0)->after('interest_charged');
            $table->double('interest_asof_date',15,2)->default(0)->after('interest_charged');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('interest_rate', 5,2);
            $table->dropColumn('interest_asof_date',15,2);
        });
    }
}
