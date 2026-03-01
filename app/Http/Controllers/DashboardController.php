<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kapsule;
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

        $codeToJoin = $request->input('codeToJoin');
        $kapsuleWithCode = null; //On définit la recherche de kapsule avec code à null par défaut
        $userOfTheKapsuleWithCode = null; //On définit la recherche de l'utilisateur de la kapsule avec code à null par défaut

        if ($codeToJoin) {
            $kapsuleWithCode = Kapsule::where('share_code', $codeToJoin)->first();
            if ($kapsuleWithCode) {
                $userOfTheKapsuleWithCode = $kapsuleWithCode->user()->first();
            }
        }

        // Récupérer les IDs des kapsules créées et rejointes par l'utilisateur

        $created = $request->user()->kapsules()->pluck('id')->toArray();
        $joined = $request->user()->joinedKapsules()->pluck('id')->toArray();

        $allIds = array_unique(array_merge($created, $joined));

        $kapsules = Kapsule::whereIn('id', $allIds)
            ->when($q, fn($query) => $query->where('name', 'like', "%{$q}%")
        ->orWhere('description', 'like', "%{$q}%"))
            ->orderBy('created_at', $orderDate)
            ->orderBy('id', $orderDate)
            ->paginate(8);
        // Retourner les kapsules à la vue avec Inertia avec les résultats de la recherche

        return Inertia::render('Dashboard/Dashboard', [
            'kapsules' => $kapsules->items(),
            'totalPages' => $kapsules->lastPage(),
            'totalKapsules' => $kapsules->total(),
            'currentPage' => $kapsules->currentPage(),
            'searchQuery' => $q ?? '',
            'dateOrder' => $orderDate,
            'kapsuleWithCode' => $kapsuleWithCode ? $kapsuleWithCode->only(['id', 'name', 'description']) : null,
            'userOfTheKapsuleWithCode' => $userOfTheKapsuleWithCode ? $userOfTheKapsuleWithCode->only(['id', 'username']) : null,
        ]);
    }
}