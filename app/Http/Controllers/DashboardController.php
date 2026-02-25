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
        $q = $request->input('q');

        $kapsules = $request->user()->kapsules()
            ->when($q, fn($query) => $query->where('name', 'like', "%{$q}%")
                                        ->orWhere('description', 'like', "%{$q}%"))
            ->get();

        return Inertia::render('Dashboard', [
            'kapsules' => $kapsules,
            'searchQuery' => $q ?? '',
        ]);
    }

   
}