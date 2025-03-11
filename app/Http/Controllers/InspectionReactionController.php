<?php

namespace App\Http\Controllers;

use App\Models\InspectionReaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Feature\Type;
use Google\Cloud\Vision\V1\Image;
use Illuminate\Support\Facades\Notification;
use App\Notifications\InspectionCreated;
use App\Models\PropertyInspection;
class InspectionReactionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = InspectionReaction::query()
            ->with([
                'propertyInspection',
                'estateConfiguration.estate',
            ]);

        if ($request->has('property_inspection_id')) {
            $query->where('property_inspection_id', $request->input('property_inspection_id'));
        }

        if ($request->has('user_id')) {
            $query->whereHas('propertyInspection.estate', function ($q) use ($request) {
                $q->where('user_id', $request->input('user_id'));
            });
        }

        $reactions = $query->get();

        // Transformer les chemins de photos en URLs complètes
        $reactions->transform(function ($reaction) {
            if ($reaction->photo) {
                $reaction->photo_url = config('app.url') . '/storage/' . $reaction->photo;
            }
            return $reaction;
        });

        // Regrouper les réactions par estate
        $groupedReactions = $reactions->groupBy('estateConfiguration.estate.id')
            ->map(function ($estateReactions) {
                $firstReaction = $estateReactions->first();
                return [
                    'estate' => [
                        'id' => $firstReaction->estateConfiguration->estate->id,
                        'name' => $firstReaction->estateConfiguration->estate->name,
                        'title' => $firstReaction->estateConfiguration->estate->title,
                        // Ajoutez d'autres informations sur l'estate si nécessaire
                    ],
                    'reactions' => $estateReactions->map(function ($reaction) {
                        return [
                            'id' => $reaction->id,
                            'comment' => $reaction->comment,
                            'photo' => $reaction->photo,
                            'photo_url' => $reaction->photo_url,
                            'estate_configuration' => $reaction->estateConfiguration,
                            'property_inspection' => $reaction->propertyInspection,
                            // Ajoutez d'autres champs si nécessaire
                        ];
                    })
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $groupedReactions
        ]);
    }

    public function only_inspections_reactions(Request $request): JsonResponse
    {
        $reactions = InspectionReaction::with(['propertyInspection.estate'])
            ->whereHas('propertyInspection.estate', function($q) use ($request) {
                $q->where('user_id', $request->id);
            })
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $reactions
        ]);
    }

    private function analyzeImage($imagePath)
    {
        try {
            // Chemin absolu vers l'image
            $absoluteImagePath = storage_path('app/public/' . $imagePath);
            \Log::info('Début analyse Vision API', ['image_path' => $absoluteImagePath]);

            // Charger l'image en tant que chaîne d'octets
            $imageContent = file_get_contents($absoluteImagePath);

            // Vérifiez si le contenu de l'image a été correctement chargé
            if ($imageContent === false) {
                throw new \Exception("Impossible de charger l'image depuis le chemin : " . $absoluteImagePath);
            }

            $imageAnnotator = new ImageAnnotatorClient([
                'credentials' => storage_path('app/google-credentials.json'),
            ]);

            // Préparer l'image pour l'analyse
            $image = [
                'content' => $imageContent, // Utiliser le contenu de l'image
            ];

            $features = [
                ['type' => Type::LABEL_DETECTION],
                ['type' => Type::TEXT_DETECTION],
            ];

            // Appel de l'API
            $response = $imageAnnotator->annotateImage($image, $features);

            $analysis = [
                'labels' => [],
                'texts' => [],
            ];

            foreach ($response->getLabelAnnotations() as $label) {
                $analysis['labels'][] = [
                    'description' => $label->getDescription(),
                    'score' => $label->getScore(),
                ];
            }

            foreach ($response->getTextAnnotations() as $text) {
                $analysis['texts'][] = $text->getDescription();
            }

            $imageAnnotator->close();
            \Log::info('Analyse terminée', [
                'nb_labels' => count($analysis['labels']),
                'nb_texts' => count($analysis['texts'])
            ]);

            return $analysis;

        } catch (\Exception $e) {
            \Log::error('Erreur globale analyse', [
                'error' => $e->getMessage(), // Afficher l'erreur complète
                'line' => $e->getLine()
            ]);
            return null;
        }
    }


    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_inspection_id' => 'required|exists:property_inspections,id',
            'estate_configuration_id' => 'required|exists:estate_configurations,id',
            'comment' => 'nullable|string',
            'photo' => 'nullable|image|max:5120', // 5MB max
        ]);

        // CHanger le statut de la propertyinspection en 'In Progress'
        // $propertyInspection = PropertyInspection::find($validated['property_inspection_id']);
        // $propertyInspection->status = 'Completed';
        // $propertyInspection->save();

        if ($request->hasFile('photo')) {
            \Log::info('Photo reçue dans la requête');
            $path = $request->file('photo')->store('reactions', 'public');
            $validated['photo'] = $path;
            $validated['photo_url'] = config('app.url') . '/storage/' . $path;

            // Ajout de l'analyse d'image
            \Log::info('Début analyse photo', ['path' => $path]);
            $imageAnalysis = $this->analyzeImage($path);
            if ($imageAnalysis) {
                \Log::info('Analyse réussie', ['analysis' => $imageAnalysis]);
                $validated['analyse'] = $imageAnalysis;
            } else {
                \Log::error('Analyse échouée');
            }
        }

        $reaction = InspectionReaction::create($validated);


        return response()->json([
            'status' => 'success',
            'message' => 'Réaction créée avec succès',
            'data' => $reaction->load(['propertyInspection', 'estateConfiguration'])
        ], 201);
    }
}
