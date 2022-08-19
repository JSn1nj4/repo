<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class HostFactory extends Factory
{
    private static array $prefixes = ['git@', 'https://'];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $prefix = $this->faker->randomElement(self::$prefixes);

        return [
            'name' => $this->faker->unique()->name,
            'url_base' => "{$prefix}{$this->faker->unique()->domainName}",
            'separator' => match($prefix) {
                "git@" => ":",
                default => "/",
            },
            'shorthand' => $this->faker->unique()->userName
        ];
    }
}
