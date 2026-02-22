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
        return Inertia::render('Dashboard', [
            'kapsules' => $request->user()->kapsules,
        ]);
    }
}