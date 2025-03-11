<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $notifications = Notification::all(); // Remplacez 12 par l'ID de l'utilisateur connecté
        return response()->json($notifications);
    }
} 