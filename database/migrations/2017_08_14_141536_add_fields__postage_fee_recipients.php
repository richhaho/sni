<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsPostageFeeRecipients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_order_recipients', function (Blueprint $table) {
            $table->decimal('postage')->default(0);
            $table->decimal('fee')->default(0);
            $table->decimal('other')->default(0);
            $table->decimal('total')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_order_recipients', function (Blueprint $table) {
            $table->dropColumn('postage');
            $table->dropColumn('fee');
            $table->dropColumn('other');
            $table->dropColumn('total');
        });
    }
}
