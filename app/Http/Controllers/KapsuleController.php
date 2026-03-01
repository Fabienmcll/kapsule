<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kapsule;
use Inertia\Inertia;

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

    public function join (Request $request, Kapsule $kapsule)
    {
        if ($kapsule && !$kapsule->members()->where('user_id', Auth::id())->exists() && $kapsule->user_id !== Auth::id()) {
            // Logique pour ajouter l'utilisateur à la kapsule
            // Par exemple, créer une relation entre l'utilisateur et la kapsule

            //Nouvelle méthode pour attacher l'utilisateur à la kapsule via la relation many-to-many
            Auth::user()->joinedKapsules()->attach($kapsule->id);

            return back()->with('success', __('you_joined_kapsule', ['kapsulename' => $kapsule->name]));
        } else if (!$kapsule) {
            return back()->with('error', __('kapsule_not_found'));
        } else {
            return back()->with('error', __('already_member_of_kapsule'));
        }
    }

    public function load(Kapsule $kapsule)
    {
        return Inertia::render('Kapsule', [
            'kapsule' => $kapsule->only(['id', 'name', 'description']),
            'owner' => $kapsule->user()->first()->only(['id', 'username']),
            'members' => $kapsule->members()->get()->map(fn($member) => $member->only(['id', 'username'])),
            'media' => $kapsule->getMedia('images')->map(function ($item) {
            return [
                    'id' => $item->id,
                    'url' => $item->getUrl(),
                    'thumb' => $item->hasGeneratedConversion('preview') ? $item->getUrl('preview') : $item->getUrl(),
                    'file_name' => $item->file_name,
                    'mime_type' => $item->mime_type,
                    'size' => $item->human_readable_size,
                ];
        }),

        ]);
    }
}
