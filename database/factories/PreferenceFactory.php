<?php

namespace Database\Factories;

use App\Models\Preference;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Preference>
 */
class PreferenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Preference::class;

    public function definition(): array
    {
        return [
            'user_id' => User::all()->random()->id,
            'categories' => json_encode($this->faker->randomElements([1, 2, 3, 4, 5], $this->faker->numberBetween(1, 3))),
            'sources' => json_encode($this->faker->randomElements(['BBC', 'CNN', 'Al Jazeera', 'Reuters', 'NY Times'], $this->faker->numberBetween(1, 3))),
            'authors' => json_encode($this->faker->randomElements(['John Doe', 'Jane Smith', 'Michael Brown', 'Emily Davis'], $this->faker->numberBetween(1, 3))),
        ];
    }
}
