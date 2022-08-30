<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserServices;
class UserServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserServices::factory()->count(20)->create();
    }
}
