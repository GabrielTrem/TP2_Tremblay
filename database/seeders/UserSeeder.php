<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Repository\RoleRepositoryInterface;

class UserSeeder extends Seeder
{
    private RoleRepositoryInterface $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository){
        $this->roleRepository = $roleRepository;
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //admin
        User::create([
            'login' => 'adminUser',
            'password' => Hash::make('adminPassword'),
            'email' => 'admin@gmail.com',
            'last_name' => 'Reeves',
            'first_name' => 'Keanu',
            'role_id' => $this->roleRepository->getIdByName('admin')
        ]);

        //normal user
        User::create([
            'login' => 'user',
            'password' => Hash::make('password'),
            'email' => 'user@gmail.com',
            'last_name' => 'Jacob',
            'first_name' => 'Noel',
            'role_id' => $this->roleRepository->getIdByName('user')
        ]);
    }
}
