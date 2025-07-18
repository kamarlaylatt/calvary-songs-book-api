<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Category::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $categories = [
            ['name' => 'Worship', 'description' => 'Songs for worship services'],
            ['name' => 'Praise', 'description' => 'Upbeat praise songs'],
            ['name' => 'Hymns', 'description' => 'Traditional hymns'],
            ['name' => 'Seasonal', 'description' => 'Christmas, Easter and other seasonal songs'],
            ['name' => 'Special', 'description' => 'Special occasion songs'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => \Illuminate\Support\Str::slug($category['name']),
                'description' => $category['description'],
            ]);
        }
    }
}
