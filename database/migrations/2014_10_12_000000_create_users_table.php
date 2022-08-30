<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->tinyInteger('gender')->comment('1-Male | 2 - Female')->nullable();
            $table->string('photo')->nullable();
            $table->string('country_code');
            $table->string('phone')->unique();
            $table->tinyInteger('phone_verified')->default(0)->comment('0- Not Verify | 1 - Verify');
            $table->string('device_type')->nullable();
            $table->string('device_id')->nullable();
            $table->text('device_token')->nullable();
            $table->string('lat')->nullable();
            $table->string('lang')->nullable();
            $table->longText('bio')->nullable();
            $table->boolean('is_banned')->default(1)->comment('0 - banned | 1 - not banned ');
            $table->string('firebase_id')->nullable();
            $table->string('twillio_id')->nullable();
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
        Schema::dropIfExists('users');
    }
}
