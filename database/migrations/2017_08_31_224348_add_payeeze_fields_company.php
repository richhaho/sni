<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPayeezeFieldsCompany extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->string('apikey')->nullable();
            $table->string('apisecret')->nullable();
            $table->string('merchant_token')->nullable();
            $table->string('js_security_key')->nullable();
            $table->string('ta_token')->nullable();
            $table->string('payeezy_mode')->nullable();
            $table->dropColumn('ftp_host');
            $table->dropColumn('ftp_user');
            $table->dropColumn('ftp_password');
            $table->dropColumn('ftp_path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropColumn('apikey');
            $table->dropColumn('apisecret');
            $table->dropColumn('merchant_token');
            $table->dropColumn('js_security_key');
            $table->dropColumn('ta_token');
            $table->dropColumn('payeezy_mode');
            $table->string('ftp_host')->nullable();
            $table->string('ftp_user')->nullable();
            $table->string('ftp_password')->nullable();
            $table->string('ftp_path')->nullable();
        });
    }
}
