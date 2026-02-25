<?php

namespace App\Http\Controllers;

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

        $kapsules = $request->user()->kapsules()
            ->when($q, fn($query) => $query->where('name', 'like', "%{$q}%")
                                        ->orWhere('description', 'like', "%{$q}%"))
            ->orderBy('created_at', $orderDate)
            ->orderBy('id', $orderDate) // Assurer un ordre stable en cas de même created_at
            ->paginate(8);
        // Retourner les kapsules à la vue avec Inertia avec les résultats de la recherche

        return Inertia::render('Dashboard', [
            'kapsules' => $kapsules->items(),
            'totalPages' => $kapsules->lastPage(),
            'totalKapsules' => $kapsules->total(),
            'currentPage' => $kapsules->currentPage(),
            'searchQuery' => $q ?? '',
            'dateOrder' => $orderDate,
        ]);
    }

   
}