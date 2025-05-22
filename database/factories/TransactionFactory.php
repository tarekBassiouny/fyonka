<?php

namespace Database\Factories;

use App\Models\Store;
use App\Models\TransactionType;
use App\Models\TransactionSubtype;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = TransactionType::factory()->create();
        $subtype = TransactionSubtype::factory()->create([
            'transaction_type_id' => $type->id,
        ]);

        return [
            'amount' => $this->faker->randomFloat(2, -500, 500),
            'description' => $this->faker->sentence(),
            'date' => $this->faker->date(),
            'store_id' => Store::factory(),
            'type_id' => $type->id,
            'subtype_id' => $subtype->id,
            'is_temp' => $this->faker->boolean(),
        ];
    }
}
