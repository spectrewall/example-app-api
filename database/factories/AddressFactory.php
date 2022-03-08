<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cep' => $this->faker->regexify('[0-9]{8}'),
            'street' => $this->faker->streetAddress(),
            'neighborhood' => $this->faker->city(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state()
        ];
    }
}
