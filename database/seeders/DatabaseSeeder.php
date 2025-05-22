<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (App::environment('production')) {
            $this->call(DefaultUsersAndTypesSeeder::class);
        } else {
            $this->call([
                UserSeeder::class,
                TransactionTypeSeeder::class,
                StoreSeeder::class,
                TransactionSeeder::class,
            ]);
        }
    }
}
