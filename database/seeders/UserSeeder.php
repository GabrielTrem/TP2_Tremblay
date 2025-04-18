<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'login' => 'adminUser',
            'password' => Hash::make('adminPassword'),
            'email' => 'admin@gmail.com',
            'last_name' => 'Reeves',
            'first_name' => 'Keanu',
            'role_id' => 2
        ]);

        User::create([
            'login' => 'user',
            'password' => Hash::make('password'),
            'email' => 'user@gmail.com',
            'last_name' => 'Jacob',
            'first_name' => 'Noel',
            'role_id' => 1
        ]);
    }
}
