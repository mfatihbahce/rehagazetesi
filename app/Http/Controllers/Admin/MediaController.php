<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    private const IMAGE_EXT = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private const VIDEO_EXT = ['mp4', 'webm', 'ogg', 'mov'];

    public function __construct(
        protected MediaService $mediaService
    ) {}

    /**
     * Medya kütüphanesi sayfası
     */
    public function index()
    {
        $files = $this->getAllMedia();

        return view('admin.media.index', compact('files'));
    }

    /**
     * Medya listesi API (picker için JSON)
     */
    public function list(Request $request)
    {
        $files = $this->getAllMedia();

        if ($request->query('type') === 'images') {
            $files = $files->filter(fn ($f) => $f['type'] === 'image');
        }
        if ($request->query('type') === 'videos') {
            $files = $files->filter(fn ($f) => $f['type'] === 'video');
        }

        return response()->json(['media' => $files->values()->all()]);
    }

    /**
     * Görsel yükle (AJAX)
     */
    public function upload(Request $request)
    {
        $file = $request->file('file');

        if ($request->has('type') && $request->type === 'content') {
            $request->validate(['file' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048']]);
            $path = $this->mediaService->uploadContentImage($file);
        } elseif ($request->has('type') && $request->type === 'video') {
            $request->validate(['file' => ['required', 'file', 'mimes:mp4,webm,ogg,mov', 'max:102400']]); // 100MB
            $path = $this->mediaService->uploadVideo($file);
        } else {
            $request->validate(['file' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048']]);
            $path = $this->mediaService->uploadNewsImage($file);
        }

        return response()->json(['url' => asset('storage/' . $path), 'path' => $path]);
    }

    private function getAllMedia(): \Illuminate\Support\Collection
    {
        $all = collect();

        foreach (['news', 'profiles', 'videos'] as $folder) {
            if (!Storage::disk('public')->exists($folder)) {
                continue;
            }
            $paths = Storage::disk('public')->allFiles($folder);
            foreach ($paths as $path) {
                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                $type = in_array($ext, self::IMAGE_EXT) ? 'image' : (in_array($ext, self::VIDEO_EXT) ? 'video' : null);
                if ($type) {
                    $all->push([
                        'path' => $path,
                        'url' => asset('storage/' . $path),
                        'name' => basename($path),
                        'folder' => dirname($path),
                        'type' => $type,
                        'time' => Storage::disk('public')->lastModified($path),
                    ]);
                }
            }
        }

        return $all->sortByDesc('time')->values();
    }
}
