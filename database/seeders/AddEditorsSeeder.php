<?php

namespace Database\Seeders;

use App\Models\EditorProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AddEditorsSeeder extends Seeder
{
    public function run(): void
    {
        $editors = [
            [
                'name' => 'Zeynep Aydın',
                'email' => 'zeynep@rehagazetesi.com',
                'title' => 'Ekonomi Editörü',
                'bio' => 'Ekonomi ve iş dünyası haberleri uzmanı. 8 yıllık tecrübe.',
            ],
            [
                'name' => 'Can Öztürk',
                'email' => 'can@rehagazetesi.com',
                'title' => 'Teknoloji Muhabiri',
                'bio' => 'Teknoloji ve dijital dünya haberleri. Bilgisayar mühendisi.',
            ],
        ];

        foreach ($editors as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => 'editor',
                ]
            );

            if ($user->wasRecentlyCreated) {
                EditorProfile::create([
                    'user_id' => $user->id,
                    'title' => $data['title'],
                    'bio' => $data['bio'],
                ]);
            }
        }
    }
}
