<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public const KEPALA  = 'kepala_gudang';
    public const STAF    = 'staf_gudang';
    public const AUDITOR = 'auditor';

    protected $fillable = ['name', 'username', 'email', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            self::KEPALA  => 'Kepala Gudang',
            self::STAF    => 'Staf Gudang',
            self::AUDITOR => 'Auditor',
            default       => $this->role,
        };
    }
}
