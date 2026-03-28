<?php

namespace Database\Seeders;

use App\Models\EditorProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class EditorProfileSeeder extends Seeder
{
    public function run(): void
    {
        $profiles = [
            'ahmet@rehagazetesi.com' => [
                'bio' => '10 yılı aşkın gazetecilik deneyimi. Gündem ve politika haberleri uzmanı.',
                'title' => 'Kıdemli Muhabir',
            ],
            'ayse@rehagazetesi.com' => [
                'bio' => 'Kültür sanat ve magazin haberleri editörü. İletişim mezunu.',
                'title' => 'Kültür Sanat Editörü',
            ],
            'mehmet@rehagazetesi.com' => [
                'bio' => 'Spor muhabiri. Futbol ve basketbol uzmanı.',
                'title' => 'Spor Muhabiri',
            ],
        ];

        foreach ($profiles as $email => $data) {
            $user = User::where('email', $email)->first();
            if ($user) {
                EditorProfile::create([
                    'user_id' => $user->id,
                    'bio' => $data['bio'],
                    'title' => $data['title'],
                ]);
            }
        }
    }
}
