<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'category_id',
        'user_id',
        'status',
        'featured_image',
        'tags',
        'is_breaking',
        'is_featured',
        'views',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_breaking' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * Haberin kategorisi
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Haberi yazan editör/admin
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Görüntülenme sayısını artır
     */
    public function incrementViews(): void
    {
        $this->increment('views');
    }
}
