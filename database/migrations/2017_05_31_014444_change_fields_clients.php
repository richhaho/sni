<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFieldsClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('gender');
            $table->dropColumn('billing_type');
            $table->dropColumn('send_certified');
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->enum('gender', ['none', 'female', 'male'])->default('none');
            $table->enum('billing_type', ['none', 'attime', 'invoiced'])->default('none');
            $table->enum('send_certified', ['none', 'green', 'nongreen'])->default('none');
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
            $table->dropColumn('gender');
            $table->dropColumn('billing_type');
            $table->dropColumn('send_certified');
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->enum('gender', ['female', 'male'])->default('female');
            $table->enum('billing_type', ['attime', 'invoiced'])->default('attime');
            $table->enum('send_certified', ['green', 'nongreen'])->default('green');
        });
    }
}
