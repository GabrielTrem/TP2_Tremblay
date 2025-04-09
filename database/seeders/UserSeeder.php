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

        User::factory(3)->state([
            'password' => 'password123',
            'role_id' => 1
        ])->create();
    }
}
