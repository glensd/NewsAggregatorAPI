<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Article;
use App\Models\Category;
use App\Models\Preference;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //commenting factory as I have used seeder and rest data is coming from news apis if u want u can uncomment it
//        $categories = Category::factory(10)->create();
//        Article::factory(50)->create();
//        Preference::factory(10)->create();
        $this->call([
            CategoriesTableSeeder::class,
        ]);
    }
}
