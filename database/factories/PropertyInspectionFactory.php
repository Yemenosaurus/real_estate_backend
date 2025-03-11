<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Estate;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PropertyInspection>
 */
class PropertyInspectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'estate_id' => Estate::factory(),
            'user_id' => User::factory(),
            'status' => $this->faker->randomElement(['Submitted', 'Approved', 'Rejected', 'In Progress', 'Completed', 'Closed']),
            'who' => $this->faker->randomElement(['particulier', 'agence', 'investisseur']),
            'config' => json_encode([
                'some_setting' => $this->faker->boolean(),
                'another_setting' => $this->faker->word(),
                // Ajoutez d'autres configurations selon vos besoins
            ]),
            'date' => $this->faker->date(),
            'comments' => $this->faker->paragraph(),
        ];
    }
} 