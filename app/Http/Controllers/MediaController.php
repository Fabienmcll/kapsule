<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kapsule;
use Illuminate\Support\Facades\Log;

class MediaController extends Controller
{
    /**
     * Reçoit le fichier de FilePond, le valide et l'associe à la Kapsule via Spatie MediaLibrary.
     */
    public function store(Request $request)
    {
        try {
            // Vérifie que le fichier fait moins de 10Mo et que la Kapsule existe
            $request->validate([
                'media' => 'required|file|max:10240',
                'kapsule_id' => 'required|exists:kapsules,id'
            ]);
            // Récupère l'instance de la Kapsule concernée
            $kapsule = Kapsule::findOrFail($request->kapsule_id);
            // Déplace le fichier vers le stockage et crée l'enregistrement en base de données
            $kapsule->addMediaFromRequest('media')
                ->toMediaCollection('images');

            return response()->json(['success' => true]);
        }
        catch (\Exception $e) {
            // Enregistre l'erreur dans storage/logs/laravel.log si l'upload échoue
            Log::error('Upload error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
