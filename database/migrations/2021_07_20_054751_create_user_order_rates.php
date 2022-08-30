<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserOrderRates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_order_rates', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->unsigned(); 
            $table->foreign('order_id')->references('id')->on('orders'); 
            $table->bigInteger('rate_by')->unsigned(); 
            $table->foreign('rate_by')->references('id')->on('users'); 
            $table->bigInteger('rate_to')->unsigned(); 
            $table->foreign('rate_to')->references('id')->on('users');
            $table->longtext('review');
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
        Schema::dropIfExists('user_order_rates');
    }
}
