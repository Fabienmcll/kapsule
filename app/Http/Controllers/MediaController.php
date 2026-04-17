<?php

namespace App\Http\Controllers;

use App\Models\Kapsule;
use Illuminate\Http\Request;
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
                'media' => 'required|file|max:102400', // 100MB en kilo-octets
                'kapsule_id' => 'required|exists:kapsules,id',
            ]);
            // Récupère l'instance de la Kapsule concernée
            $kapsule = Kapsule::findOrFail($request->kapsule_id);
            // Déplace le fichier vers le stockage et crée l'enregistrement en base de données
            $media = $kapsule->addMediaFromRequest('media')
                ->toMediaCollection('images');

            \DB::table('media')
                ->where('id', $media->id)
                ->update(['user_id' => auth()->id()]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            // Enregistre l'erreur dans storage/logs/laravel.log si l'upload échoue
            Log::error('Upload error: '.$e->getMessage());

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($mediaId)
    {
        try {
            $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::findOrFail($mediaId);
            $media->delete();

            return back()->with('success', __('media_deleted'));
        } catch (\Exception $e) {
            Log::error('Delete media error: '.$e->getMessage());

            return back()->with('error', __('media_delete_failed'));
        }
    }
}
