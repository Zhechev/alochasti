<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\City>
 */
class CityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->city();

        $base = Str::slug($name);
        if ($base === '') {
            $base = 'city';
        }

        return [
            'name' => $name,
            'slug' => $base.'-'.strtolower(dechex(crc32($name))),
        ];
    }
}
