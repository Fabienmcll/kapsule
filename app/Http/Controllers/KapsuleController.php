<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class KapsuleController extends Controller
{
    /**
     * Enregistrer une nouvelle kapsule
     */
    public function store(Request $request)
    {
       $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        Auth::user()->kapsules()->create($validated);

        return back();
    }
}