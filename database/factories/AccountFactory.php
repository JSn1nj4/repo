<?php

namespace Database\Factories;

use App\Models\RemoteSource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    protected ?Collection $remote_sources;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $this->remote_sources = RemoteSource::all();

        return [
            'remote_source_id' => $this->remote_sources->random()->id,
            'name' => $this->faker->unique()->name,
            'slug' => $this->faker->unique()->slug,
            'shorthand' => $this->faker->unique()->asciify('***'),
        ];
    }
}
