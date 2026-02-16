<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            'Smartphones',
            'Laptops',
            'Smartwatches',
            'Headphones',
            'Cameras',
            'Gaming Consoles',
            'Computer Accessories',
            'Home Electronics'
        ];

        foreach ($categories as $category) {
            \App\Models\Category::firstOrCreate(
                ['name' => $category],
                ['slug' => \Illuminate\Support\Str::slug($category)]
            );
        }
    }
}
