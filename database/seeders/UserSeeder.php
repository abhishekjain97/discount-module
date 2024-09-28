<?php 

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'User 1',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
        ]);

        User::create([
            'name' => 'User 2',
            'email' => 'user2@example.com',
            'password' => bcrypt('password'),
        ]);

        User::create([
            'name' => 'User 3',
            'email' => 'user3@example.com',
            'password' => bcrypt('password'),
        ]);

        User::create([
            'name' => 'User 4',
            'email' => 'user4@example.com',
            'password' => bcrypt('password'),
        ]);

        User::create([
            'name' => 'User 5',
            'email' => 'user5@example.com',
            'password' => bcrypt('password'),
        ]);

    }
}
