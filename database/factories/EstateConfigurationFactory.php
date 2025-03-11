<?php

namespace Database\Factories;

use App\Models\Estate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EstateConfiguration>
 */
class EstateConfigurationFactory extends Factory
{
    private array $possibleRoomTypes = [
        'Chambre',
        'Salon',
        'Cuisine',
        'Salle de bain',
        'WC',
        'Bureau',
        'Garage',
        'Cave',
        'Buanderie',
        'Stockage'
    ];

    private array $possibleFloorMaterials = [
        'Carrelage',
        'Parquet',
        'Béton',
        'Moquette',
        'Stratifié'
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $rooms = $this->generateRooms();
        
        return [
            'estate_id' => Estate::factory(),
            'level' => $this->faker->randomElement(['Rez-de-chaussée', '1er étage', '2ème étage', '3ème étage', 'Sous-sol']),
            'details' => json_encode([]),
            'pieces' => json_encode($rooms),
            'room_count' => collect($rooms)->sum('nombre')
        ];
    }

    private function generateRooms(): array
    {
        $rooms = [];
        $numberOfRoomTypes = $this->faker->numberBetween(2, 4);

        $selectedRoomTypes = $this->faker->randomElements(
            $this->possibleRoomTypes,
            $numberOfRoomTypes
        );

        foreach ($selectedRoomTypes as $roomType) {
            $rooms[] = [
                'type' => $roomType,
                'nombre' => $this->faker->numberBetween(
                    1,
                    $this->getMaxRoomsForType($roomType)
                ),
            ];
        }

        return $rooms;
    }

    private function getMaxRoomsForType(string $type): int
    {
        return match($type) {
            'Chambre' => 4,
            'Salon' => 1,
            'Cuisine' => 1,
            'Salle de bain' => 2,
            'WC' => 2,
            'Bureau' => 2,
            'Garage' => 1,
            'Cave' => 1,
            'Buanderie' => 1,
            'Stockage' => 2,
            default => 1
        };
    }
}
