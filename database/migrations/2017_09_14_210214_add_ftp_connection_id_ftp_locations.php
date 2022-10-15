<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFtpConnectionIdFtpLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ftp_locations', function (Blueprint $table) {
            $table->unsignedInteger('connection_id')->default(0)->after('path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ftp_locations', function (Blueprint $table) {
            $table->dropColumn('connection_id');
        });
    }
}
