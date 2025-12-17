<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isGifted = fake()->boolean(20);

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'city_id' => City::factory(),
            'title' => fake()->sentence(fake()->numberBetween(3, 6)),
            'description' => fake()->paragraphs(fake()->numberBetween(2, 4), true),
            'weight' => fake()->boolean(35) ? fake()->randomFloat(2, 0.1, 25) : null,
            'dimensions' => fake()->boolean(35)
                ? fake()->numberBetween(10, 80).'x'.fake()->numberBetween(10, 80).'x'.fake()->numberBetween(10, 80).' cm'
                : null,
            'status' => $isGifted ? 'gifted' : 'available',
            'gifted_at' => $isGifted ? fake()->dateTimeBetween('-3 months', 'now') : null,
        ];
    }
}
