<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UploadedFile>
 */
class UploadedFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'filename' => $this->faker->unique()->word() . '.csv',
            'path' => 'uploads/' . now()->format('Y-m-d') . '/' . $this->faker->uuid . '.csv',
        ];
    }
}
