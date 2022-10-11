<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTablesToSoftdeletes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('contact_infos', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('entities', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('jobs', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('job_parties', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('notes', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('work_orders', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('work_order_payments', function (Blueprint $table) {
            $table->softDeletes();
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
            $table->dropSoftDeletes();
        });
        Schema::table('contact_infos', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('entities', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('job_parties', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('notes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('work_order_payments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
