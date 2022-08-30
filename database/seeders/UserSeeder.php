<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserDocument;
use App\Models\DocumentType;
use App\Models\UserSetting;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $users = User::factory()->count(20)->create();
       foreach($users as $user){
            $user->assignRole([2]);
            // add user setting
            $us = new UserSetting;
            $us->user_id = $user->id;
            $us->provider = 1;
            $us->online = 1;
            $us->notification = 1;
            $us->save();
       }
    }
}
