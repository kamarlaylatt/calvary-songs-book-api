<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        // \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');
        // Category::truncate();
        // \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $categories = [
            ['name' => 'Worship', 'description' => 'Songs for worship services', 'sort_no' => 1],
            ['name' => 'Praise', 'description' => 'Upbeat praise songs', 'sort_no' => 2],
            ['name' => 'Hymns', 'description' => 'Traditional hymns', 'sort_no' => 3],
            ['name' => 'Seasonal', 'description' => 'Christmas, Easter and other seasonal songs', 'sort_no' => 4],
            ['name' => 'Special', 'description' => 'Special occasion songs', 'sort_no' => 5],
        ];

        foreach ($categories as $index => $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => \Illuminate\Support\Str::slug($category['name']),
                'description' => $category['description'],
                'sort_no' => $category['sort_no'],
            ]);
        }
    }
}
