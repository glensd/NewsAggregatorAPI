<?php

namespace Database\Seeders;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run()
    {
        $categories = [
            ['name' => 'Technology', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Health', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Science', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Sports', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Entertainment', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Politics', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Business', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Travel', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

        ];

        Category::insert($categories);
    }
}
