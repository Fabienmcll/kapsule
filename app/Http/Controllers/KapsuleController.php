<?php

namespace App\Http\Controllers;

use App\Models\Kapsule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use ZanySoft\Zip\Facades\Zip;

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

    public function join(Request $request, Kapsule $kapsule)
    {
        if ($kapsule && ! $kapsule->members()->where('user_id', Auth::id())->exists() && $kapsule->user_id !== Auth::id() && ! $kapsule->members()->where('user_id', Auth::id())->wherePivot('is_banned', true)->exists()) {
            // Logique pour ajouter l'utilisateur à la kapsule
            // Par exemple, créer une relation entre l'utilisateur et la kapsule

            // Nouvelle méthode pour attacher l'utilisateur à la kapsule via la relation many-to-many
            Auth::user()->joinedKapsules()->attach($kapsule->id);

            return back()->with('success', __('you_joined_kapsule', ['kapsulename' => $kapsule->name]));
        } elseif (! $kapsule) {
            return back()->with('error', __('kapsule_not_found'));
        } elseif ($kapsule->members()->where('user_id', Auth::id())->wherePivot('is_banned', true)->exists()) {
            return back()->with('error', __('you_are_banned_from_this_kapsule'));
        } else {
            return back()->with('error', __('already_member_of_kapsule'));
        }
    }

    public function load(Kapsule $kapsule)
    {
        $isAccepted = $kapsule->members()->where('user_id', Auth::id())->wherePivot('accepted', true)->exists();
        $isPending = $kapsule->members()->where('user_id', Auth::id())->wherePivot('is_pending', true)->exists();

        $allMembers = $kapsule->members()
            ->get()
            ->map(fn ($member) => [
                'id' => $member->id,
                'username' => $member->username,
                'accepted' => $member->pivot->accepted,
                'is_pending' => $member->pivot->is_pending,
            ]);

        // On récupère uniquement les membres acceptés ou en attente, et on réindexe la collection
        $members = $allMembers->where(fn ($member) => $member['accepted'] || $member['is_pending'])->values();

        $bannedUsers = $kapsule->members()
            ->wherePivot('is_banned', true)
            ->get()
            ->map(fn ($user) => [
                'id' => $user->id,
                'username' => $user->username,
            ]);

        return Inertia::render('Kapsule/Kapsule', [
            'kapsule' => $kapsule->only(['id', 'name', 'description', 'share_code']),
            'owner' => $kapsule->user()->first()->only(['id', 'username']),
            'isAccepted' => $isAccepted,
            'isPending' => $isPending,
            'members' => $members,
            'bannedUsers' => $bannedUsers,
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

    public function accept(Request $request, Kapsule $kapsule, $member)
    {

        // Vérifier que l'utilisateur actuel est le propriétaire de la kapsule
        if ($kapsule->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mettre à jour le statut de la demande d'adhésion dans la table pivot
        $kapsule->members()->updateExistingPivot($member, [
            'accepted' => true,
            'is_pending' => false,
        ]);

        return back()->with('success', __('membership_request_accepted'));
    }

    public function reject(Request $request, Kapsule $kapsule, $member)
    {

        // Vérifier que l'utilisateur actuel est le propriétaire de la kapsule
        if ($kapsule->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mettre à jour le statut de la demande d'adhésion dans la table pivot
        $kapsule->members()->updateExistingPivot($member, [
            'accepted' => false,
            'is_pending' => false,
        ]);

        return back()->with('success', __('membership_request_rejected'));
    }

    public function ban(Request $request, Kapsule $kapsule, $member)
    {

        // Vérifier que l'utilisateur actuel est le propriétaire de la kapsule
        if ($kapsule->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mettre à jour le statut de la demande d'adhésion dans la table pivot
        $kapsule->members()->updateExistingPivot($member, [
            'accepted' => false,
            'is_pending' => false,
            'is_banned' => true,
        ]);

        return back()->with('success', __('member_banned_successfully'));
    }

    public function edit(Request $request, Kapsule $kapsule)
    {
        return Inertia::render('Kapsule/ModifyKapsule', [
            'kapsule' => $kapsule->only(['id', 'name', 'description']),
        ]);
    }

    public function modify(Request $request, Kapsule $kapsule)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $kapsule->update($validated);

        return redirect()->route('kapsule.load', $kapsule)
            ->with('success', 'Kapsule modifiée avec succès.');
    }

    public function downloadZip(Kapsule $kapsule)
    {
        $mediaItems = $kapsule->getMedia('images');

        if ($mediaItems->isEmpty()) {
            return back()->with('error', __('no_files_to_download', [], 'fr') ?? 'Aucun fichier à télécharger.');
        }

        $zipFileName = \Illuminate\Support\Str::slug($kapsule->name).'-'.time().'.zip';
        $zipPath = storage_path('app/'.$zipFileName);

        $zip = Zip::create($zipPath, true);

        $filesToAdd = [];
        foreach ($mediaItems as $media) {
            $path = $media->getPath();
            if (File::exists($path)) {
                $filesToAdd[] = $path;
            }
        }

        if (empty($filesToAdd)) {
            return back()->with('error', __('files_not_found_on_server', [], 'fr') ?? 'Fichiers introuvables sur le serveur.');
        }

        $zip->add($filesToAdd, true);
        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function leave(Request $request, Kapsule $kapsule)
    {
        // Vérifier que l'utilisateur est membre de la kapsule
        if (! $kapsule->members()->where('user_id', Auth::id())->exists()) {
            return back()->with('error', __('not_a_member_of_kapsule'));
        }

        // Vérifier que l'utilisateur n'est pas le propriétaire de la kapsule
        if ($kapsule->user_id === Auth::id()) {
            return back()->with('error', __('owner_cannot_leave_kapsule'));
        }

        // Supprimer la relation entre l'utilisateur et la kapsule
        Auth::user()->joinedKapsules()->detach($kapsule->id);

        return redirect()->route('dashboard')->with('success', __('you_left_kapsule', ['kapsulename' => $kapsule->name]));
    }
}
