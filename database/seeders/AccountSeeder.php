<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\RemoteSource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(RemoteSource::count() === 0) {
            $this->call(RemoteSourceSeeder::class);
        }

        DB::table(Account::make()->getTable())
            ->truncate();

        Account::factory(10)
            ->create();
    }
}
