<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailingListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailing_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('text')->nullable();
            $table->unsignedInteger('chunk')->nullable();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->dateTime('start')->nullable();
            $table->string('status')->default('blueprint');
            $table->time('allow_send_from')->nullable();
            $table->time('allow_send_to')->nullable();
            $table->string('email_teplate')->nullable();
            $table->string('selected_channels')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('mailing_lists');
    }
}
