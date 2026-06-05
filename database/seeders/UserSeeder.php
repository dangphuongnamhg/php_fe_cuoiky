<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create(['name' => 'Admin FieldBook', 'email' => 'admin@fieldbook.vn', 'password' => Hash::make('password'), 'role' => 'admin', 'status' => 'active']);
        User::create(['name' => 'Nguyễn An', 'email' => 'an.nguyen@example.com', 'password' => Hash::make('password'), 'role' => 'user', 'status' => 'active']);
        User::create(['name' => 'Trần Bình', 'email' => 'binh.tran@example.com', 'password' => Hash::make('password'), 'role' => 'user', 'status' => 'active']);
        User::create(['name' => 'Lê Chi', 'email' => 'chi.le@example.com', 'password' => Hash::make('password'), 'role' => 'user', 'status' => 'active']);
    }
}
