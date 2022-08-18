<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Host;
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
        if(Host::count() === 0) {
            $this->call(HostSeeder::class);
        }

        DB::table(Account::make()->getTable())
            ->truncate();

        Account::factory(7)
            ->create();
    }
}
