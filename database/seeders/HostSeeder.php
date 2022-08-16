<?php

namespace Database\Seeders;

use App\Models\Host;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(Host::make()->getTable())
            ->truncate();

        Host::factory(3)
            ->create();
    }
}
