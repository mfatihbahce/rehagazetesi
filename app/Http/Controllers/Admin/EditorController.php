<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CacheKeys;
use App\Http\Controllers\Controller;
use App\Models\EditorProfile;
use App\Models\User;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EditorController extends Controller
{
    public function __construct(
        protected MediaService $mediaService
    ) {}
    public function index()
    {
        $editors = User::where('role', 'editor')
            ->with(['editorProfile', 'news'])
            ->withCount('news')
            ->orderBy('name')
            ->get();

        return view('admin.editors.index', compact('editors'));
    }

    public function create()
    {
        return view('admin.editors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'title' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'legacy_user_id' => ['nullable', 'integer', 'min:1'],
            'can_access_archive' => ['nullable', 'boolean'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'editor',
            'legacy_user_id' => $validated['legacy_user_id'] ?? null,
            'can_access_archive' => (bool) ($validated['can_access_archive'] ?? false),
        ]);

        EditorProfile::create([
            'user_id' => $user->id,
            'title' => $validated['title'] ?? null,
            'bio' => $validated['bio'] ?? null,
        ]);

        return redirect()->route('admin.editors.index')->with('success', 'Editör eklendi.');
    }

    public function edit(User $editor)
    {
        if ($editor->role !== 'editor') {
            abort(404);
        }

        $editor->load('editorProfile');

        return view('admin.editors.edit', compact('editor'));
    }

    public function update(Request $request, User $editor)
    {
        if ($editor->role !== 'editor') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($editor->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'title' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'legacy_user_id' => ['nullable', 'integer', 'min:1'],
            'can_access_archive' => ['nullable', 'boolean'],
        ]);

        $editor->name = $validated['name'];
        $editor->email = $validated['email'];
        $editor->legacy_user_id = $validated['legacy_user_id'] ?? null;
        $editor->can_access_archive = (bool) ($validated['can_access_archive'] ?? false);
        if (!empty($validated['password'])) {
            $editor->password = Hash::make($validated['password']);
        }
        $editor->save();

        $profile = $editor->editorProfile ?? new EditorProfile(['user_id' => $editor->id]);
        $profile->title = $validated['title'] ?? null;
        $profile->bio = $validated['bio'] ?? null;
        if ($request->hasFile('profile_photo')) {
            $request->validate(['profile_photo' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048']]);
            $profile->profile_photo = $this->mediaService->uploadProfilePhoto($request->file('profile_photo'));
            CacheKeys::clearNewsCaches();
        }
        $profile->save();

        return redirect()->route('admin.editors.index')->with('success', 'Editör güncellendi.');
    }

    public function destroy(User $editor)
    {
        if ($editor->role !== 'editor') {
            abort(404);
        }

        $editor->delete();

        return redirect()->route('admin.editors.index')->with('success', 'Editör silindi.');
    }
}
