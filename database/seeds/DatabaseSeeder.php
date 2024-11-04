<?php

use App\Admin;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SettingSeeder::class);
        Role::create([
            'name'       => 'user',
            'guard_name' => 'web',
        ]);

        Role::create([
            'name'       => 'sales_person',
            'guard_name' => 'web',
        ]);

        Role::create([
            'name'       => 'merchant',
            'guard_name' => 'web',
        ]);

        Role::create([
            'name'       => 'user',
            'guard_name' => 'sanctum',
        ]);

        Role::create([
            'name'       => 'sales_person',
            'guard_name' => 'sanctum',
        ]);

        Role::create([
            'name'       => 'merchant',
            'guard_name' => 'sanctum',
        ]);

        Admin::create([
        'name'=>'admin',
        'email'=>'admin@acai.com',
        'password'=>bcrypt('?Acai@123#'),
        ]);
    }
}
