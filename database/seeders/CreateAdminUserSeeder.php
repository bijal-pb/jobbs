<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'first_name' => 'Jobbs',
            'last_name' => 'admin',
            'gender' => 1,
            'email' => 'admin@jobbs.com',
            'password' => bcrypt('123456789'),
            'country_code' => '+91',
            'phone' => '9999999999'
        ]);
        $user1 = User::create([
            'first_name' => 'Developer',
            'last_name' => 'Ingeniousmindslab', 
            'gender' => 1,
            'email' => 'developer@ingeniousmindslab.com',
            'password' => bcrypt('123456789'),
            'country_code' => '+91',
            'phone' => '9999999998'
        ]);

        $role = Role::create(['name' => 'Admin']);
        $role1 = Role::create(['name' => 'User']);
        $role2 = Role::create(['name' => 'Developer']);
     
        $permissions = Permission::where('name','!=','developer')->pluck('id','id')->all();
        $permissions1 = Permission::pluck('id','id')->all();    
        $role->syncPermissions($permissions);
        $role2->syncPermissions($permissions1);  
        $user->assignRole([$role->id]);
        $user1->assignRole([$role2->id]);
    }
}
