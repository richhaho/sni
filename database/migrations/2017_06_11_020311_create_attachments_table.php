<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_mime')->nullable();
            $table->string('thumb_path')->nullable();
            $table->string('original_name')->nullable();
            $table->unsignedInteger('file_size')->default(0);
            $table->unsignedInteger('user_id');
            $table->tinyInteger('generated_id')->default(0);
            $table->timestamp('printed_at')->nullable();
            $table->morphs('attachable');
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
        Schema::dropIfExists('attachments');
    }
}
