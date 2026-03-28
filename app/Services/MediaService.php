<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class MediaService
{
    /**
     * Haber öne çıkan görseli yükle
     */
    public function uploadNewsImage(UploadedFile $file): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('news', $filename, 'public');
    }

    /**
     * Haber içeriği görseli yükle (içerik editörü için)
     */
    public function uploadContentImage(UploadedFile $file): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('news/content', $filename, 'public');
    }

    /**
     * Video yükle
     */
    public function uploadVideo(UploadedFile $file): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('videos', $filename, 'public');
    }

    /**
     * Profil fotoğrafı yükle
     */
    public function uploadProfilePhoto(UploadedFile $file): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('profiles', $filename, 'public');
    }

    /**
     * Site logosu yükle
     */
    public function uploadSiteLogo(UploadedFile $file): string
    {
        $filename = 'logo.' . $file->getClientOriginalExtension();
        return $file->storeAs('site', $filename, 'public');
    }

    /**
     * Favicon yükle
     */
    public function uploadFavicon(UploadedFile $file): string
    {
        $filename = 'favicon.' . $file->getClientOriginalExtension();
        return $file->storeAs('site', $filename, 'public');
    }
}
