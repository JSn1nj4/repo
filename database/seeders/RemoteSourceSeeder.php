<?php

namespace Database\Seeders;

use App\Models\RemoteSource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RemoteSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(RemoteSource::make()->getTable())
            ->truncate();

        RemoteSource::factory(3)
            ->create();
    }
}
