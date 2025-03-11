<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Estate;
use App\Models\Image;
use App\Models\Like;
use App\Models\PropertyInspection;
use App\Models\Role;
use App\Models\EstateConfiguration;
use App\Models\InspectionReaction;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 10 users
        $users = User::factory(10)->create();

        // Create 3 roles
        $roles = Role::factory(2)->create();

        // Create 20 estates with random users and their configurations
        $estates = Estate::factory(20)->create([
            'user_id' => fn() => $users->random()->id
        ])->each(function ($estate) {
            // Chaque estate aura entre 1 et 4 configurations (étages)
            EstateConfiguration::factory()
                ->count(rand(1, 4))
                ->create([
                    'estate_id' => $estate->id
                ]);
        });

        // Create 60 images (approximately 3 per estate)
        Image::factory(60)->create([
            'estate_id' => fn() => $estates->random()->id
        ]);

        // Create 40 likes with random users and estates
        Like::factory(40)->create([
            'user_id' => fn() => $users->random()->id,
            'estate_id' => fn() => $estates->random()->id
        ]);

        // Create 30 property inspections
        $inspections = PropertyInspection::factory(30)->create([
            'user_id' => fn() => $users->random()->id,
            'estate_id' => fn() => $estates->random()->id
        ]);

        // Create 10 inspection reactions
        InspectionReaction::factory(10)->create([
            'property_inspection_id' => fn() => $inspections->random()->id,
            'estate_configuration_id' => fn() => EstateConfiguration::inRandomOrder()->first()->id
        ]);
    }
}
