<?php

namespace App\Http\Controllers;

use App\Models\PropertyInspection;
use App\Models\EstateConfiguration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\EstateConfigurationResource;
use App\Models\Estate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\InspectionCreated;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\InspectionReaction;

class PropertyInspectionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = PropertyInspection::query()
            ->with(['estate.configurations']);

        if ($request->has('sort')) {
            $order = $request->input('order', 'asc');
            $query->orderBy($request->input('sort'), $order);
        }

        $propertyInspections = $query->get()->map(function ($inspection) {
            // On transforme les configurations en utilisant notre Resource
            if ($inspection->estate) {
                $inspection->estate->configurations = $inspection->estate->configurations
                    ->map(fn($config) => new EstateConfigurationResource($config));
            }
            return $inspection;
        });

        return response()->json([
            // 'status' => 'success',
            'data' => $propertyInspections
        ]);
    }

    public function test(Request $request): JsonResponse
    {
        $query = PropertyInspection::query()
            ->with(['estate.configurations']);

        if ($request->has('sort')) {
            $order = $request->input('order', 'asc');
            $query->orderBy($request->input('sort'), $order);
        }

        $propertyInspections = $query->get()->map(function ($inspection) {
            // Transformation des configurations en utilisant notre Resource
            if ($inspection->estate) {
                $inspection->estate->configurations = $inspection->estate->configurations
                    ->map(fn($config) => new EstateConfigurationResource($config));
            }
            return $inspection;
        });

        return response()->json([
            'status' => 'success',
            'data' => $propertyInspections->toArray(),
        ]);
    }


    public function store(Request $request): JsonResponse
    {
        // Validation des données
        $validatedData = $request->validate([
            'estate_id' => 'nullable|integer',
            'user_id' => 'required|integer',
            'status' => 'required|string',
            'who' => 'required|string',
            'config' => 'nullable|string',
            'date' => 'required|date',
            'comments' => 'nullable|string',
            'level' => 'nullable|string',
            // 'room_type' => 'required|string',
            'room_count' => 'nullable|integer',
            'additional_info' => 'nullable|string',
            'details' => 'nullable|json',
            'pieces' => 'nullable|json',
            'estate_name' => 'nullable|string',
            'location' => 'nullable|string',
            'title' => 'nullable|string',
            'price' => 'nullable|numeric',
            'description' => 'nullable|string',
        ]);

        // Vérifier si 'config' est vide et la remplacer par une valeur par défaut
        if (empty($validatedData['config'])) {
            $validatedData['config'] = json_encode([
                'some_setting' => false, // Valeur par défaut
                'another_setting' => 'sit', // Valeur par défaut
            ]);
        }

        // Création de l'Estate seulement si estate_name est rempli
        $estate = null;
        if (!empty($validatedData['estate_name'])) {
            $estate = Estate::create([
                // 'estate_name' => $validatedData['estate_name'],
                'user_id' => $validatedData['user_id'],
                'location' => $validatedData['location'],
                'title' => $validatedData['estate_name'],
                'price' => $validatedData['price'],
                'description' => $validatedData['description'],
            ]);
        } 
        // else if estate where id is estateid input
        else if(Estate::where('id', $validatedData['estate_id'])->exists()) {
            $estate = Estate::where('id', $validatedData['estate_id'])->first();
        }
        // else if estates infos are empty create a new one with some default values to edit later
        else if(empty($validatedData['estate_name']) && empty($validatedData['location']) && empty($validatedData['title']) && empty($validatedData['price']) && empty($validatedData['description'])) {
            $estate = Estate::create([
                'estate_name' => 'Nouvelle propriété',
                'user_id' => $validatedData['user_id'],
                'location' => 'Nouvelle localisation',
                'title' => 'Nouveau titre',
            ]);
        }

        // 

        // Ajout de l'ID de l'estate dans les données validées
        $validatedData['estate_id'] = $estate->id;

        // Décoder la configuration JSON
        $config = json_decode($validatedData['config'], true);

        // Vérifier si le décodage a réussi
        if (!is_array($config)) {
            return response()->json([
                'message' => 'Invalid configuration format.'
            ], 400);
        }

        // Création de la property inspection
        $propertyInspection = PropertyInspection::create([
            'user_id' => $validatedData['user_id'],
            'status' => $validatedData['status'],
            'who' => $validatedData['who'],
            'config' => $validatedData['config'],
            'date' => $validatedData['date'],
            'comments' => $validatedData['comments'],
            'estate_id' => $estate->id,
        ]);

        // Création des configurations
        // Création des estate configurations
        if ($request->input('estate_id')) {
            foreach ($config as $type => $rooms) {
                if (!is_array($rooms)) {
                    Log::error('Invalid rooms format for type: ' . $type);
                    continue;
                }
                foreach ($rooms as $roomEntry) {
                    // Extraction du room_type et de la quantité depuis la chaîne "room|quantity"
                    [$roomType, $quantity] = explode('|', $roomEntry);
                    EstateConfiguration::create([
                        'estate_id' => $estate->id,
                        'level' => $validatedData['level'],
                        'room_type' => $roomType,
                        'room_count' => (int) $quantity,  // Utiliser la quantité extraite
                        'additional_info' => $validatedData['additional_info'],
                        'details' => $validatedData['details'],
                        'pieces' => $validatedData['pieces'],
                    ]);
                }
            }
        } else if ($request->input('config')) {
            $config = json_decode($request->input('config'), true);
            foreach ($config as $floorName => $rooms) {
                if (!is_array($rooms)) {
                    Log::error('Invalid rooms format for floor: ' . $floorName);
                    continue;
                }
                
                // Préparer le tableau des pièces pour ce niveau
                $pieces = [];
                $total_rooms = 0;
                
                foreach ($rooms as $roomEntry) {
                    [$roomType, $quantity] = explode('|', $roomEntry);
                    $pieces[] = [
                        'type' => $roomType,
                        'nombre' => (int) $quantity
                    ];
                    $total_rooms += (int) $quantity;
                }
                
                EstateConfiguration::create([
                    'estate_id' => $estate->id,
                    'level' => $floorName, // Utiliser directement le nom de l'étage
                    'room_type' => null,
                    'room_count' => $total_rooms,
                    'additional_info' => $validatedData['additional_info'] ?? null,
                    'details' => $validatedData['details'] ?? null,
                    'pieces' => json_encode($pieces, JSON_UNESCAPED_UNICODE),
                ]);
            }
        }
        
        else {
            Log::error('Estate not found');
        }

        // Envoi d'un email via Mailtrap avec la vue Blade
        Notification::route('mail', 'destinataire@test.com')
            ->notify(new InspectionCreated($propertyInspection));

        return response()->json([
            'property_inspection' => $propertyInspection,
            'message' => 'Property inspection and estate configurations created successfully.'
        ], 201);
    }


    public function generate_pdf($id)
    {
        // Récupérer les réactions associées à cette inspection
        $reactions = InspectionReaction::with([
            'propertyInspection.estate',
            'estateConfiguration.estate',
        ])->where('property_inspection_id', $id)->get();

        // Transformer les chemins de photos en URLs complètes
        $reactions->transform(function ($reaction) {
            if ($reaction->photo) {
                $reaction->photo_url = config('app.url') . '/storage/' . $reaction->photo;
            }
            return $reaction;
        });

        // Récupérer l'estate à partir de la première réaction
        $estate = $reactions->first()->estateConfiguration->estate ?? null;

        // Regrouper les réactions par estate
        $groupedReactions = $reactions->groupBy('estateConfiguration.estate.id')
            ->map(function ($estateReactions) {
                $firstReaction = $estateReactions->first();
                return [
                    'estate' => [
                        'id' => $firstReaction->estateConfiguration->estate->id,
                        'name' => $firstReaction->estateConfiguration->estate->name,
                        'title' => $firstReaction->estateConfiguration->estate->title,
                    ],
                    'reactions' => $estateReactions->map(function ($reaction) {
                        return [
                            'id' => $reaction->id,
                            'comment' => $reaction->comment,
                            'photo' => $reaction->photo,
                            'photo_url' => $reaction->photo_url,
                            'estate_configuration' => $reaction->estateConfiguration,
                            'property_inspection' => $reaction->propertyInspection,
                        ];
                    })
                ];
            });

        // Préparer les données pour la vue PDF
        $data = [
            'estate' => $estate,
            'groupedReactions' => $groupedReactions
        ];

        $pdf = PDF::loadView('pdf.property_inspection', $data);

        return $pdf->stream('property_inspection.pdf');
    }

    public function accept_edl($id)
    {
        $inspection = PropertyInspection::findOrFail($id);
        $inspection->status = 'Approved';
        $inspection->save();

        return response()->json([
            'message' => 'Inspection accepted successfully',
            'inspection' => $inspection
        ]);
    }

    public function decline_edl($id)
    {
        $inspection = PropertyInspection::findOrFail($id);
        $inspection->status = 'Rejected';
        $inspection->save();

        return response()->json([
            'message' => 'Inspection declined successfully',
            'inspection' => $inspection
        ]);
    }

    public function edl_in_progress($id)
    {
        $inspection = PropertyInspection::findOrFail($id);
        $inspection->status = 'In Progress';
        $inspection->save();

        return response()->json([
            'message' => 'Inspection status changed to In Progress',
            'inspection' => $inspection
        ]);
    }

    public function close_inspection($id)
    {
        $inspection = PropertyInspection::findOrFail($id);
        $inspection->status = 'Closed';
        $inspection->save();
    }

    public function complete_inspection($id)
    {
        $inspection = PropertyInspection::findOrFail($id);
        $inspection->status = 'Completed';
        $inspection->save();
    }
}
