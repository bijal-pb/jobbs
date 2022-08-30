<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from');
            $table->foreign('from')->references('id')->on('users');
            $table->unsignedBigInteger('to');
            $table->foreign('to')->references('id')->on('users');
            $table->bigInteger('user_service_id')->unsigned(); 
            $table->foreign('user_service_id')->references('id')->on('user_services'); 
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();	
            $table->longtext('address');
            $table->string('lat')->nullable();
            $table->string('lang')->nullable();
            $table->tinyInteger('status')->default(0)->comment('1-accept | 2 - decline');
            $table->Integer('subtotal')->nullable();
            $table->Integer('service_charge')->nullable();
            $table->Integer('discount')->nullable();
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
        Schema::dropIfExists('user_requests');
    }
}
