<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IncreasePhonesLengthTableClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('mobile');
            $table->dropColumn('fax');
        });
        Schema::table('clients', function (Blueprint $table) {
            
            $table->string('fax',30)->nullable()->after('country');
            $table->string('mobile',30)->nullable()->after('country');
            $table->string('phone',30)->nullable()->after('country');
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
            $table->dropColumn('phone');
            $table->dropColumn('mobile');
            $table->dropColumn('fax');
        });
        Schema::table('clients', function (Blueprint $table) {
            
            $table->string('fax',20)->nullable()->after('country');
            $table->string('mobile',20)->nullable()->after('country');
            $table->string('phone',20)->nullable()->after('country');
        });
    }
}
