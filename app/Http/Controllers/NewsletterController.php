<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Notifications\NewsletterConfirmation;


class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Vérifier si c'est un utilisateur existant
        $user = User::where('email', $request->email)->first();
        
        // Vérifier si déjà abonné
        $existingSubscriber = Subscriber::where('email', $request->email)->first();
        
        if ($existingSubscriber) {
            if ($existingSubscriber->is_active) {
                return response()->json([
                    'message' => 'Vous êtes déjà abonné à notre newsletter'
                ], 400);
            }
            
            // Réactiver l'abonnement
            $existingSubscriber->update([
                'is_active' => true,
                'status' => 'pending'
            ]);

            return response()->json([
                'message' => 'Votre abonnement a été réactivé'
            ]);
        }

        // Créer un nouvel abonné
        $subscriber = Subscriber::create([
            'email' => $request->email,
            'user_id' => $user ? $user->id : null,
            'confirmation_token' => Str::random(32),
            'status' => 'pending'
        ]);
        
        // Envoyer la notification
        $subscriber->notify(new NewsletterConfirmation($subscriber));

        return response()->json([
            'message' => 'Merci de votre inscription ! Un email de confirmation vous a été envoyé.'
        ]);
    }

    public function confirm($token)
    {
        $subscriber = Subscriber::where('confirmation_token', $token)->first();

        if (!$subscriber) {
            return response()->json([
                'message' => 'Token de confirmation invalide'
            ], 400);
        }

        $subscriber->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
            'confirmation_token' => null
        ]);

        return response()->json([
            'message' => 'Votre inscription à la newsletter a été confirmée'
        ]);
    }

    public function unsubscribe($token)
    {
        $subscriber = Subscriber::where('email', decrypt($token))->first();

        if (!$subscriber) {
            return response()->json([
                'message' => 'Abonné non trouvé'
            ], 404);
        }

        $subscriber->update([
            'is_active' => false,
            'status' => 'unsubscribed'
        ]);

        return response()->json([
            'message' => 'Vous avez été désabonné avec succès'
        ]);
    }
} 