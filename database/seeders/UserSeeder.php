<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Emilio Francisco',
            'lastname1' => 'Vázquez',
            'lastname2' => 'Perez',
            'phone' => '4151805038',
            'email' => 'emiliovpsis@gmail.com',
            'password' => hash::make('Emilio72@#'),
            'photo' => 'default.png',
            'userType' => 'admin',
            ]);
    }
}
