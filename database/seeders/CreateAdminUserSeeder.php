<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
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
            'name'       => 'Ahmed Nabil', 
            'email'      => 'ahmednassag@gmail.com',
            'mobile'     => '01016856433',
            'password'   => bcrypt('12345678'),
            'roles_name' => ["Admin"],
            'Status'     => 1,
            'avatar'     => 'avatar.jpg',
        ]);

        $user_2 = User::create([
            'name'       => 'Ahmed', 
            'email'      => 'ahmed@gmail.com',
            'mobile'     => '01141090116',
            'password'   => bcrypt('12345678'),
            'roles_name' => ["Admin"],
            'Status'     => 1,
        ]);
    
        $role        = Role::create(['name' => 'Admin']);
        $permissions = Permission::pluck('id','id')->all();
        $role->syncPermissions($permissions);
        
        $user->assignRole([$role->id]);
        $user_2->assignRole([$role->id]);

    }
}