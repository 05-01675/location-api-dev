<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChekout extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chekout', function (Blueprint $table) {
            $table->id();
            $table->text('order_id');
            $table->text('request');
            $table->text('response');
            $table->text('status');
            $table->text('customer_id');
            $table->text('email');
            $table->integer('viewed');
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
        Schema::dropIfExists('chekout');
    }
}
