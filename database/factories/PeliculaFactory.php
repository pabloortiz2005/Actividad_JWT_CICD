<?php

namespace Database\Factories;

use App\Models\Pelicula;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pelicula>
 */
class PeliculaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    return [
        'title' => fake()->sentence(),
        'release_date' => fake()->date(),
        'sinopsis' => fake()->paragraph(),
        'duration' => fake()->numberBetween(80, 180),
        'gendre' => fake()->word(),
        'director_id' => \App\Models\Director::factory(), 
    ];
}
}
