<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Kategoriye ait haberler
     */
    public function news()
    {
        return $this->hasMany(News::class);
    }

    /**
     * Yayında olan haberler
     */
    public function publishedNews()
    {
        return $this->hasMany(News::class)->where('status', 'published');
    }
}
