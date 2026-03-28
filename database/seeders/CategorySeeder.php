<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Gündem', 'slug' => 'gundem', 'order' => 1],
            ['name' => 'Ekonomi', 'slug' => 'ekonomi', 'order' => 2],
            ['name' => 'Spor', 'slug' => 'spor', 'order' => 3],
            ['name' => 'Kültür Sanat', 'slug' => 'kultur-sanat', 'order' => 4],
            ['name' => 'Sağlık', 'slug' => 'saglik', 'order' => 5],
            ['name' => 'Eğitim', 'slug' => 'egitim', 'order' => 6],
            ['name' => 'Teknoloji', 'slug' => 'teknoloji', 'order' => 7],
            ['name' => 'Yerel', 'slug' => 'yerel', 'order' => 8],
        ];

        foreach ($categories as $category) {
            Category::create(array_merge($category, ['is_active' => true]));
        }
    }
}
