<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'user_id' => \Illuminate\Support\Str::uuid(),
            'username' => 'admin',
            'email' => 'admin@pickynet.com',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'user_id' => \Illuminate\Support\Str::uuid(),
            'username' => 'userdemo',
            'email' => 'user@pickynet.com',
            'password' => Hash::make('user1234'),
        ]);

        // Tambahkan 3 user dummy lainnya
        User::factory()->count(3)->create();
    }
}