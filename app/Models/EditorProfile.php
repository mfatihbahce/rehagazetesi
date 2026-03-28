<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EditorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'profile_photo',
        'title',
        'twitter',
        'linkedin',
    ];

    /**
     * Profil sahibi kullanıcı
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
