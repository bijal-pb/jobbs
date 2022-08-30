<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_request_id')->unsigned(); 
            $table->foreign('user_request_id')->references('id')->on('user_requests'); 
            $table->timestamp('reach_time')->nullable();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('complete_time')->nullable();
            $table->double('price',8,2)->nullable();
            $table->double('service_fee',8,2)->nullable();
            $table->double('discount',8,2)->nullable();
            $table->double('total_amount',8,2)->nullable();
            $table->bigInteger('status')->unsigned(); 
            $table->foreign('status')->references('id')->on('order_statuses'); 
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
        Schema::dropIfExists('orders');
    }
}
