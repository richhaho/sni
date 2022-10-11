<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDatesJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
           $table->dropColumn('started_at');
        });
        Schema::table('jobs', function (Blueprint $table) {
           $table->timestamp('last_day')->nullable()->after('country');
           $table->timestamp('started_at')->nullable()->after('country');
           
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
            $table->dropColumn('started_at');
            $table->dropColumn('last_day');
        });
        Schema::table('jobs', function (Blueprint $table) {
           $table->date('started_at')->nullable()->after('country');
        });
        
    }
}
