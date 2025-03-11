<?php

namespace Database\Factories;

use App\Models\InspectionReaction;
use App\Models\PropertyInspection;
use App\Models\EstateConfiguration;
use Illuminate\Database\Eloquent\Factories\Factory;

class InspectionReactionFactory extends Factory
{
    protected $model = InspectionReaction::class;

    public function definition()
    {
        return [
            'property_inspection_id' => PropertyInspection::factory(),
            'estate_configuration_id' => EstateConfiguration::factory(),
            'comment' => $this->faker->sentence,
            'analyse' => $this->faker->randomElement([null, json_encode(['key' => 'value'])]),
            'photo' => $this->faker->optional()->imageUrl(),
            'status' => $this->faker->randomElement(['en cours', 'pdf généré', 'en facturation', 'facturé']),
        ];
    }
} 