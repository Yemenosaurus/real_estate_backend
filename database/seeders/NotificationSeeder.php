<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        // Créer 10 notifications pour l'utilisateur avec l'ID 11
        Notification::factory()->count(10)->create();
    }
} 