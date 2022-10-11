<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeClientFirstLastName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE clients modify first_name VARCHAR(80) null');
        DB::statement('ALTER TABLE clients modify last_name VARCHAR(80) null');
        DB::statement('ALTER TABLE clients modify email VARCHAR(191) null');
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('print_method');
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->enum('print_method', ['none','sni', 'client'])->default('none');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       DB::statement('ALTER TABLE clients modify first_name VARCHAR(80)');
       DB::statement('ALTER TABLE clients modify last_name VARCHAR(80)');
       DB::statement('ALTER TABLE clients modify email VARCHAR(191)');
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('print_method');
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->enum('print_method', ['sni', 'client'])->default('sni');
        });
    }
}
