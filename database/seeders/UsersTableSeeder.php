<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usersCount = max((int)$this->command->ask('How many users would you like?', 20), 1);
        User::factory([
            'name' => 'John Doe',
            'email' => 'mail@mail.com',
            'password' => Hash::make('secret'),
            'is_admin' => true
        ])->create();

        User::factory([
            'name' => 'User',
            'email' => 'user@user.com',
            'password' => Hash::make('user'),
            'is_admin' => false
        ])->create();

        User::factory($usersCount)->create();
    }
}
