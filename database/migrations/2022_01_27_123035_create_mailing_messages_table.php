<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailingMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailing_messages', function (Blueprint $table) {
            $table->id();
            $table->string('channel')->nullable();
            $table->string('status')->default('new');
            $table->bigInteger('client_id')->unsigned();
            // $table->foreign('client_id')->references('id')->on('clients');
            $table->bigInteger('mailing_list_id')->unsigned();
            $table->integer('opened')->default(0)->unsigned();
            // $table->foreign('mailing_list_id')->references('id')->on('mailing_lists');
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
        Schema::dropIfExists('mailing_messages');
    }
}
