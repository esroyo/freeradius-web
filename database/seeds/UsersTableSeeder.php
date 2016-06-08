<?php

namespace Database\Seeds;

use Illuminate\Database\Seeder;
use Freeradius\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'admin',
            'password' => bcrypt('admin'),
            'email' => 'admin@localhost',
            'is_admin' => true,
            'api_token' => str_random(64),
        ]);

        User::create([
            'name' => 'tester',
            'password' => bcrypt('1234'),
            'email' => 'tester@localhost',
            'is_admin' => false,
            'api_token' => str_random(64),
        ]);
    }
}
