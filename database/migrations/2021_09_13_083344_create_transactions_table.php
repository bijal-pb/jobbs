<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->nullable();
            $table->bigInteger('user_id')->unsigned(); 
            $table->foreign('user_id')->references('id')->on('users'); 
            $table->bigInteger('order_id')->unsigned(); 
            $table->foreign('order_id')->references('id')->on('orders'); 
            $table->bigInteger('user_request_id')->unsigned(); 
            $table->foreign('user_request_id')->references('id')->on('user_requests'); 
            $table->double('amount')->nullable();;
            $table->string('payment_type')->nullable();;
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
        Schema::dropIfExists('transactions');
    }
}
