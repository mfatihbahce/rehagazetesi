<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@rehagazetesi.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $editors = [
            ['name' => 'Ahmet Yılmaz', 'email' => 'ahmet@rehagazetesi.com'],
            ['name' => 'Ayşe Demir', 'email' => 'ayse@rehagazetesi.com'],
            ['name' => 'Mehmet Kaya', 'email' => 'mehmet@rehagazetesi.com'],
        ];

        foreach ($editors as $editor) {
            User::create([
                'name' => $editor['name'],
                'email' => $editor['email'],
                'password' => Hash::make('password'),
                'role' => 'editor',
            ]);
        }
    }
}
