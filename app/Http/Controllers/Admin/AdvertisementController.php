<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdvertisementRequest;
use App\Http\Requests\UpdateAdvertisementRequest;
use App\Models\Advertisement;
use Illuminate\Support\Facades\Storage;

class AdvertisementController extends Controller
{
    public function index()
    {
        $advertisements = Advertisement::query()
            ->orderByDesc('priority')
            ->orderByDesc('id')
            ->paginate(20);

        $stats = [
            'total' => Advertisement::count(),
            'active' => Advertisement::where('is_active', true)->count(),
            'left' => Advertisement::where('placement', Advertisement::PLACEMENT_LEFT)->count(),
            'right' => Advertisement::where('placement', Advertisement::PLACEMENT_RIGHT)->count(),
        ];

        return view('admin.advertisements.index', compact('advertisements', 'stats'));
    }

    public function create()
    {
        $placements = Advertisement::placements();

        return view('admin.advertisements.create', compact('placements'));
    }

    public function store(StoreAdvertisementRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['priority'] = (int) ($data['priority'] ?? 0);
        $data['created_by'] = $request->user()?->id;
        unset($data['image_file'], $data['mobile_image_file']);

        if (($data['type'] ?? 'image') === 'image') {
            $data['html_code'] = null;
            if ($request->hasFile('image_file')) {
                $path = $request->file('image_file')->store('advertisements', 'public');
                $data['image_url'] = asset('storage/' . $path);
            }
            if ($request->hasFile('mobile_image_file')) {
                $mobilePath = $request->file('mobile_image_file')->store('advertisements', 'public');
                $data['mobile_image_url'] = asset('storage/' . $mobilePath);
            }
        } else {
            $data['image_url'] = null;
            $data['mobile_image_url'] = null;
            $data['target_url'] = null;
            $data['alt_text'] = null;
        }

        Advertisement::create($data);

        return redirect()->route('admin.advertisements.index')->with('success', 'Reklam oluşturuldu.');
    }

    public function edit(Advertisement $advertisement)
    {
        $placements = Advertisement::placements();

        return view('admin.advertisements.edit', compact('advertisement', 'placements'));
    }

    public function update(UpdateAdvertisementRequest $request, Advertisement $advertisement)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['priority'] = (int) ($data['priority'] ?? 0);
        unset($data['image_file'], $data['mobile_image_file']);

        if (($data['type'] ?? 'image') === 'image') {
            $data['html_code'] = null;
            if ($request->hasFile('image_file')) {
                $oldPath = $this->extractStoragePath($advertisement->image_url);
                if ($oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }
                $path = $request->file('image_file')->store('advertisements', 'public');
                $data['image_url'] = asset('storage/' . $path);
            }
            if ($request->hasFile('mobile_image_file')) {
                $oldMobilePath = $this->extractStoragePath($advertisement->mobile_image_url);
                if ($oldMobilePath) {
                    Storage::disk('public')->delete($oldMobilePath);
                }
                $mobilePath = $request->file('mobile_image_file')->store('advertisements', 'public');
                $data['mobile_image_url'] = asset('storage/' . $mobilePath);
            }
        } else {
            $oldPath = $this->extractStoragePath($advertisement->image_url);
            if ($oldPath) {
                Storage::disk('public')->delete($oldPath);
            }
            $oldMobilePath = $this->extractStoragePath($advertisement->mobile_image_url);
            if ($oldMobilePath) {
                Storage::disk('public')->delete($oldMobilePath);
            }
            $data['image_url'] = null;
            $data['mobile_image_url'] = null;
            $data['target_url'] = null;
            $data['alt_text'] = null;
        }

        $advertisement->update($data);

        return redirect()->route('admin.advertisements.index')->with('success', 'Reklam güncellendi.');
    }

    public function destroy(Advertisement $advertisement)
    {
        $oldPath = $this->extractStoragePath($advertisement->image_url);
        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }
        $oldMobilePath = $this->extractStoragePath($advertisement->mobile_image_url);
        if ($oldMobilePath) {
            Storage::disk('public')->delete($oldMobilePath);
        }

        $advertisement->delete();

        return redirect()->route('admin.advertisements.index')->with('success', 'Reklam silindi.');
    }

    private function extractStoragePath(?string $url): ?string
    {
        if (!$url || !str_contains($url, '/storage/')) {
            return null;
        }

        return ltrim(str_replace(url('/storage/'), '', $url), '/');
    }
}
