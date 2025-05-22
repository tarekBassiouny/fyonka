<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\Store;
use App\Models\TransactionType;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $types = TransactionType::with('subtypes')->get();
        $stores = Store::all();

        foreach (range(1, 50) as $i) {
            $type = $types->random();
            $subtype = $type->subtypes->random();

            Transaction::create([
                'amount' => $type->name == 'income' ? rand(50, 1500) : rand(-1000, -50),
                'description' => fake()->sentence(),
                'type' => $type->name,
                'type_id' => $type->id,
                'subtype_id' => $subtype->id,
                'date' => now()->subDays(rand(0, 90)),
                'store_id' => $stores->random()->id,
                'is_temp' => fake()->boolean(20),
            ]);
        }
    }
}

