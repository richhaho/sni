<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChamgeTitleSizeClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('title');
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->string('title',191)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('title');
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->string('title',20)->nullable();
        });
    }
}
