<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CacheKeys;
use App\Http\Controllers\Controller;
use App\Models\EditorProfile;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct(
        protected MediaService $mediaService
    ) {}

    public function edit()
    {
        $user = auth()->user();
        $user->load('editorProfile');

        return view('admin.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];

        if ($user->isEditor()) {
            $rules['title'] = ['nullable', 'string', 'max:255'];
            $rules['bio'] = ['nullable', 'string'];
        }

        $validated = $request->validate($rules);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        if ($user->isEditor()) {
            $profile = $user->editorProfile ?? EditorProfile::create(['user_id' => $user->id]);
            $profile->title = $validated['title'] ?? null;
            $profile->bio = $validated['bio'] ?? null;

            if ($request->hasFile('profile_photo')) {
                $request->validate(['profile_photo' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048']]);
                $profile->profile_photo = $this->mediaService->uploadProfilePhoto($request->file('profile_photo'));
                CacheKeys::clearNewsCaches();
            }

            $profile->save();
        }

        return back()->with('success', 'Profil güncellendi.');
    }
}
