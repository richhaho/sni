<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixFieldLengthTableJobs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('folio_number');
        });
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('folio_number',50)->nullable()->after('legal_description');
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
            $table->dropColumn('folio_number');
        });
        Schema::table('jobs', function (Blueprint $table) {
            $table->longText('folio_number')->nullable()->after('legal_description');
        });
    }
}
