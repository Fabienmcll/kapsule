<?php

namespace App\Http\Middleware;

use App\Models\Kapsule;
use Closure;
use Illuminate\Http\Request; // Importe le bon modèle
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class CheckIfMediaIsFromOwnerOrImporter
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. On récupère l'ID (ou l'objet) depuis la route
        $mediaRouteParam = $request->route('media');

        // 2. Si c'est une string (ID), on va chercher le modèle en base
        $media = $mediaRouteParam instanceof Media
            ? $mediaRouteParam
            : Media::findOrFail($mediaRouteParam);

        $user = $request->user();

        // 3. On récupère la Kapsule associée (model_id)
        $kapsule = Kapsule::find($media->model_id);

        if (! $kapsule) {
            abort(404, 'Kapsule introuvable pour ce média.');
        }

        // Log de debug corrigé (on utilise $media->id maintenant que c'est un objet)
        Log::info("Permissions : Media {$media->id}, User {$user->id}, Kapsule {$kapsule->id}");

        // 4. Vérification : l'utilisateur est soit l'uploadeur du média, soit le proprio de la kapsule
        if ($media->user_id !== $user->id && $kapsule->user_id !== $user->id) {
            abort(403, __('you_do_not_have_permission_to_delete_this_media'));
        }

        return $next($request);
    }
}
