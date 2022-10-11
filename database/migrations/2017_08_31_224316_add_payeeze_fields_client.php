<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPayeezeFieldsClient extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('payeezy_type',50)->nullable();
            $table->string('payeezy_value')->nullable();
            $table->string('payeezy_cardholder_name')->nullable();
            $table->string('payeezy_exp_date',10)->nullable();
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
            $table->dropColumn('payeezy_type');
            $table->dropColumn('payeezy_value');
            $table->dropColumn('payeezy_cardholder_name');
            $table->dropColumn('payeezy_exp_date');
        });
    }
}
