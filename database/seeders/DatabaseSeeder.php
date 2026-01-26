<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed the unified WorkBalance (employee) + HumanOps (aggregated) dataset.
        // WorkBalanceCompleteSeeder also runs AggregationService to populate HumanOps tables.
        $this->call([
            WorkBalanceCompleteSeeder::class,
        ]);
    }
}
