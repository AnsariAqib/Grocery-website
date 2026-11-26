<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Without this contract Filament only lets users in when APP_ENV=local, so
     * the panel 403s on the deployed site. There is no public registration and
     * no role column — every row in `users` is an admin created on purpose —
     * so being authenticated is the whole check.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
