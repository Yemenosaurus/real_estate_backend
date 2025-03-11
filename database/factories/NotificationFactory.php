<?php

namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition()
    {
        return [
            'user_id' => 11, // ID de l'utilisateur pour lequel les notifications sont créées
            'title' => $this->faker->sentence,
            'message' => $this->faker->paragraph,
            'read_at' => null, // Par défaut, les notifications ne sont pas lues
        ];
    }
} 