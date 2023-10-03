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
        	'name' => 'Admin', 
        	'email' => 'admin@gmail.com',
        	'phone_number' => '8160958662',
            'country_code' => 'IN',
            'dial_code'=>'+91',
            'is_phone_verify' => 1,
            'is_active' => 1,
            'create_by' => 1
        ]); 
  
        $role = Role::create(['name' => 'Super-Admin']);
        $userRole = Role::create(['name' => 'User']);
   
        $permissions = Permission::pluck('id','id')->all();
  
        $role->syncPermissions($permissions);
        // $userRole->syncPermissions(['9' =>'9']); 
        $user->assignRole([$role->id]); 
    }
}
