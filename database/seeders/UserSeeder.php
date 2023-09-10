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
            'first_name' => 'Vlad',
            'last_name' => 'Khytrov',
            'role' => 'student',
            'email' => 'student@email.com',
            'password' => Hash::make('password'),
        ]);

        DB::table('users')->insert([
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'role' => 'teacher',
            'email' => 'teacher@email.com',
            'password' => Hash::make('password'),
        ]);
    }
}
