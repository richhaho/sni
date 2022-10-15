<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemplateLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('template_lines', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('template_id');
            $table->text('description');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('price');
            $table->string('status')->nullable();
            $table->string('type');
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
        Schema::dropIfExists('template_lines');
    }
}
