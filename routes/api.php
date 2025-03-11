<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EstateController;
use App\Http\Controllers\PropertyInspectionController;
use App\Http\Controllers\InspectionReactionController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|

| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!

|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Routes publiques
Route::post('/login', [AuthController::class, 'login']);
Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});

// Routes protégées
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    
    // Routes pour les utilisateurs
    Route::get('/user/{id}', [UserController::class, 'show']);
    Route::put('/edit-profile/{id}', [UserController::class, 'update']);
    Route::put('reset-password/{id}', [UserController::class, 'resetPassword']);

    // Routes pour les estates
    Route::get('estates/{id}', [EstateController::class, 'index']);
    Route::get('estates/{estate}', [EstateController::class, 'show']);
    Route::post('estates', [EstateController::class, 'store']);
    Route::put('estates/{estate}', [EstateController::class, 'update']);
    Route::delete('estates/{estate}', [EstateController::class, 'destroy']);

    // Routes pour les property inspections
    Route::get('property-inspections/{id}', [PropertyInspectionController::class, 'index']);
    Route::get('property-inspections-test/{id}', [PropertyInspectionController::class, 'test']);
    Route::post('property-inspections', [PropertyInspectionController::class, 'store']);
    Route::get('property-inspections/{propertyInspection}', [PropertyInspectionController::class, 'show']);
    Route::put('property-inspections/{propertyInspection}', [PropertyInspectionController::class, 'update']);
    Route::delete('property-inspections/{propertyInspection}', [PropertyInspectionController::class, 'destroy']);
    Route::get('property-inspections/{id}/generate-pdf', [PropertyInspectionController::class, 'generate_pdf']);
    Route::put('property-inspections/{id}/accept', [PropertyInspectionController::class, 'accept_edl']);
    Route::put('property-inspections/{id}/decline', [PropertyInspectionController::class, 'decline_edl']);
    Route::put('property-inspections/{id}/edl-in-progress', [PropertyInspectionController::class, 'edl_in_progress']);
    Route::put('property-inspections/{id}/close', [PropertyInspectionController::class, 'close_inspection']);
    Route::put('property-inspections/{id}/complete', [PropertyInspectionController::class, 'complete_inspection']);

    // Routes pour les inspection reactions
    Route::post('inspection-reactions/{id}', [InspectionReactionController::class, 'store'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    Route::get('inspection-reactions/{id}', [InspectionReactionController::class, 'index']);
    Route::get('/inspection-reactions-only/{id}', [InspectionReactionController::class, 'only_inspections_reactions']);
    Route::post('inspection-reactions', [InspectionReactionController::class, 'store']);
});

// Route pour accéder aux fichiers du storage
Route::get('storage/{path}', function($path) {
    return response()->file(storage_path('app/public/' . $path));
})->where('path', '.*');