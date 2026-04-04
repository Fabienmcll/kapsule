<?php

namespace App\Http\Controllers;

use App\Models\Kapsule;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Afficher les kapsules de l'utilisateur
     */
    public function index(Request $request)
    {

        // Récuperer la requête de recherche

        $q = $request->input('q');

        $orderDate = $request->input('dateOrder', 'desc');

        $shareCode = $request->input('shareCode');
        $codeToJoin = $request->input('codeToJoin');

        $kapsuleWithCode = null;
        $userOfTheKapsuleWithCode = null;

        if ($codeToJoin) {
            $kapsuleWithCode = Kapsule::where('share_code', $codeToJoin)->first();
            if ($kapsuleWithCode) {
                $userOfTheKapsuleWithCode = $kapsuleWithCode->user()->first();
            }
        }

        // Récupérer les IDs des kapsules créées et rejointes par l'utilisateur

        $created = $request->user()->kapsules()->pluck('id')->toArray();
        $joined = $request->user()->joinedKapsules()->pluck('id')->toArray();
        $banned = $request->user()->joinedKapsules()->wherePivot('is_banned', true)->pluck('id')->toArray();

        $allIds = array_unique(array_merge($created, $joined));
        $allIds = array_diff($allIds, $banned); // Exclure les kapsules dont l'utilisateur est banni

        // Filtre de base : l'utilisateur ne voit que les Kapsules dont il est propriétaire ou membre
        $kapsules = Kapsule::whereIn('id', $allIds)
            // Si un 'shareCode' est fourni par le champ de recherche par code, on filtre par correspondance exacte
            ->when($shareCode, function ($query) use ($shareCode) {
                return $query->where('share_code', $shareCode);
            })
            // Si une recherche textuelle est fournie, on cherche dans le nom, la description ou le code
            ->when($q, function ($query) use ($q) {
                return $query->where(function ($subQuery) use ($q) {
                    $subQuery->where('name', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%")
                        ->orWhere('share_code', 'like', "%{$q}%");
                }
                );
            })
            ->orderBy('created_at', $orderDate)
            ->orderBy('id', $orderDate)
            ->paginate(8);
        // Retourner les kapsules à la vue avec Inertia avec les résultats de la recherche

        // Savoir si la demande est en attente
        // Je récupère tous les IDs des Kapsules dont l'utilisateur est membre et dont la demande est en attente
        $allKapsulesWithIsPending = $request->user()->joinedKapsules()
            ->wherePivot('is_pending', true)
            ->pluck('id')
            ->toArray();

        // j'ajoute une propriété "is_pending" à chaque Kapsule pour savoir si la demande d'adhésion est en attente
        foreach ($kapsules as $kapsule) {
            $kapsule->is_pending = in_array($kapsule->id, $allKapsulesWithIsPending);
        }

        return Inertia::render('Dashboard/Dashboard', [
            'kapsules' => $kapsules->items(),
            'totalPages' => $kapsules->lastPage(),
            'totalKapsules' => $kapsules->total(),
            'currentPage' => $kapsules->currentPage(),
            'searchQuery' => $q ?? '',
            'dateOrder' => $orderDate,
            'shareCode' => $shareCode ?? '',
            'kapsuleWithCode' => $kapsuleWithCode,
            'userOfTheKapsuleWithCode' => $userOfTheKapsuleWithCode,

        ]);
    }
}
