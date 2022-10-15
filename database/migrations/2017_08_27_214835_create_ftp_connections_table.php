<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFtpConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ftp_connections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ftp_name')->nullable();
            $table->string('ftp_host')->nullable();
            $table->string('ftp_user')->nullable();
            $table->string('ftp_password')->nullable();
            $table->string('ftp_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ftp_connections');
    }
}
