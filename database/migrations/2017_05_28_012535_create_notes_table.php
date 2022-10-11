<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type',50)->nullable();
            $table->longText('text')->nullable();
            $table->dateTime('entered_at')->nullable();
            $table->dateTime('readed_at')->nullable();
            $table->morphs('noteable');
            $table->unsignedInteger('entered_by');
            $table->unsignedInteger('client_id')->default(0);
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
        Schema::dropIfExists('notes');
    }
}
