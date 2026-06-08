<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ResourceFileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->uuid() . '.jpg',
            'label' => $this->faker->words(3, true),
            'language' => 'en',
            'width' => 250,
            'height' => 250,
            'size' => 100,
        ];
    }
}
