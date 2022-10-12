<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNotesDatesToTimestamp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn('entered_at');
            $table->dropColumn('readed_at');
        });
        Schema::table('notes', function (Blueprint $table) {
            $table->timestamp('entered_at')->nullable();
            $table->timestamp('readed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn('entered_at');
            $table->dropColumn('readed_at');
        });
        Schema::table('notes', function (Blueprint $table) {
            $table->dateTime('entered_at')->nullable();
            $table->dateTime('readed_at')->nullable();
        });
    }
}
