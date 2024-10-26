<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition()
    {
        return [
            'title' => fake()->sentence(),
            'content' => fake()->paragraph(),
            'author' => fake()->name(),
            'source' => fake()->company(),
            'category_id' => Category::all()->random()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
