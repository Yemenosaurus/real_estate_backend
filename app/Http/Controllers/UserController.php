<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function show($id)
    {
        $user = User::findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'message' => 'User retrieved successfully',
            'user' => $user
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validatedData = [];

        // Valider et ajouter le nom s'il est présent
        if ($request->has('name')) {
            $validatedData['name'] = $request->validate([
                'name' => 'string|max:255'
            ])['name'];
        }

        // Valider et ajouter l'email s'il est présent
        if ($request->has('email')) {
            $validatedData['email'] = $request->validate([
                'email' => 'string|email|max:255|unique:users,email,' . $id
            ])['email'];
        }

        // Valider et ajouter les settings s'ils sont présents
        if ($request->has('settings')) {
            $validatedData['settings'] = $request->validate([
                'settings' => 'json'
            ])['settings'];
        }

        // Gérer l'avatar s'il est présent
        if ($request->hasFile('avatar')) {
            $request->validate([
                'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Supprimer l'ancien avatar s'il existe
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Stocker le nouvel avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            $validatedData['avatar'] = $path;
        }

        // Mettre à jour uniquement si des données sont présentes
        if (!empty($validatedData)) {
            $user->update($validatedData);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string',
        ]);

        if (!Hash::check($validatedData['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], 400);
        }

        $user->update([
            'password' => Hash::make($validatedData['new_password'])
        ]);

        return response()->json([
            'message' => 'Password reset successfully'
        ]);
    }
} 