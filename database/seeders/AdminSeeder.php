<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
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
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'avatar' => asset('dummy.jpeg'),
            'user_name' => 'admin',
            'user_role' => 'admin',
            'registered_at' => Carbon::now()
        ]);
    }
}
