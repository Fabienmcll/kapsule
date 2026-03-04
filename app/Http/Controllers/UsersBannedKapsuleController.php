<?php

namespace App\Http\Controllers;

use App\Models\Kapsule;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UsersBannedKapsuleController extends Controller
{
    public function index($kapsuleId)
    {
        $kapsule = Kapsule::findOrFail($kapsuleId);

        // Récupérer les utilisateurs bannis de la kapsule
        $bannedUsers = $kapsule->members()
            ->wherePivot('is_banned', true)
            ->get();

        return Inertia::render('Kapsule/BannedUsers', [
            'kapsule' => $kapsule,
            'bannedUsers' => $bannedUsers,
        ]);
    }

    public function unban($kapsuleId, $userId)
    {
        $kapsule = Kapsule::findOrFail($kapsuleId);

        // Vérifier que l'utilisateur est bien membre de la kapsule et est banni
        if (!$kapsule->members()->where('user_id', $userId)->wherePivot('is_banned', true)->exists()) {
            return redirect()->back()->withErrors(__('user_not_banned_in_kapsule'));
        }

        // On supprime la relation de ban entre l'utilisateur et la kapsule pour le débannir
        $kapsule->members()->detach($userId);

        return redirect()->back()->with('success', __('user_unbanned_successfully'));
    }
}
