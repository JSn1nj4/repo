<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Repo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RepoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(Account::count() === 0) {
            $this->call(AccountSeeder::class);
        }

        DB::table(Repo::make()->getTable())
            ->truncate();

        Repo::factory(12)
            ->create();
    }
}
