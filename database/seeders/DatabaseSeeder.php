<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Critic;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([            
            LanguageSeeder::class,
            FilmSeeder::class,
            ActorSeeder::class,
            FilmActorSeeder::class,
            RoleSeeder::class
        ]);

        //admin user
        User::create([
            'login' => 'adminUser',
            'password' => Hash::make('adminPassword'),
            'email' => 'admin@gmail.com',
            'last_name' => 'Reeves',
            'first_name' => 'Keanu',
            'role_id' => 2
        ]);

        User::factory(3)->make([
            'password' => 'password123',
            'role_id' => 1
        ]);

		//Ne sera pas fait dans le cadre de ce TP, les users et les critiques seront créés par vous
        //User::factory(10)->has(Critic::factory(30))->create();
    }
}
