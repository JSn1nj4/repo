<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Repo>
 */
class RepoFactory extends Factory
{
    protected static ?Collection $accounts;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        if(!isset(self::$accounts)) self::$accounts = Account::all();

        return [
            'account_id' => self::$accounts->random()->id,
            'name' => $this->faker->unique()->name,
            'slug' => $this->faker->unique()->slug,
        ];
    }
}
