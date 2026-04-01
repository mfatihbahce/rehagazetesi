<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'legacy_user_id',
        'can_access_archive',
        'editor_order',
        'is_chief_columnist',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'can_access_archive' => 'boolean',
        'is_chief_columnist' => 'boolean',
    ];

    /**
     * Kullanıcının editör profili
     */
    public function editorProfile()
    {
        return $this->hasOne(EditorProfile::class);
    }

    /**
     * Kullanıcının yazdığı haberler
     */
    public function news()
    {
        return $this->hasMany(News::class);
    }

    /**
     * Admin mi kontrol et
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Editör mü kontrol et
     */
    public function isEditor(): bool
    {
        return $this->role === 'editor';
    }
}
