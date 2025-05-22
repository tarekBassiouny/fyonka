<?php

namespace Database\Seeders;

use App\Models\TransactionType;
use App\Models\TransactionSubtype;
use Illuminate\Database\Seeder;

class TransactionTypeSeeder extends Seeder
{
    public function run(): void
    {
        $income = TransactionType::create(['name' => 'income']);
        $outcome = TransactionType::create(['name' => 'outcome']);

        // Income subtypes
        TransactionSubtype::create(['name' => 'Sales', 'transaction_type_id' => $income->id]);
        TransactionSubtype::create(['name' => 'Interest', 'transaction_type_id' => $income->id]);

        // Outcome subtypes
        TransactionSubtype::create(['name' => 'Rent', 'transaction_type_id' => $outcome->id]);
        TransactionSubtype::create(['name' => 'Utilities', 'transaction_type_id' => $outcome->id]);
        TransactionSubtype::create(['name' => 'Salaries', 'transaction_type_id' => $outcome->id]);
    }
}

