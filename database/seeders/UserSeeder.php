<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleAdmin = Role::findOrFail(1);
        $roleTesoreria = Role::findOrFail(2);
        $roleCuenta = Role::findOrFail(3);

        $user = User::create([
            'name' => 'Administrador',
            'last_name' => 'Administrador',
            'dui' => '12345678-9',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'name' => 'William',
            'email_verified_at' => now(),
        ]);
        $user->assignRole($roleAdmin);
    }
}
