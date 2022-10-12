<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJobFileds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('private_type')->nullable();
            $table->tinyInteger('is_mall_unit')->default(0);
            $table->tinyInteger('is_tenant')->default(0);
            $table->tinyInteger('is_condo')->default(0);
            $table->string('association_name')->nullable();
            $table->string('a_unit_number')->nullable();

            $table->string('mall_name')->nullable();
            $table->string('m_unit_number')->nullable();
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
            $table->dropColumn('commercial_residential');
            $table->dropColumn('is_mall_unit');
            $table->dropColumn('is_tenant');
            $table->dropColumn('is_condo');
            $table->dropColumn('association_name');
            $table->dropColumn('a_unit_number');
            $table->dropColumn('mall_name');
            $table->dropColumn('m_unit_number');
        });
    }
}
