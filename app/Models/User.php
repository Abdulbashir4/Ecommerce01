<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name', 'phone', 'password', 'profile_image', 'is_admin', 'status',
        'last_login_at', 'last_login_ip', 'failed_login_attempts', 'locked_until',
        'force_password_change',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_admin' => 'boolean',
        'force_password_change' => 'boolean',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
    ];

    public function getAuthIdentifierName() { return 'id'; }

    public function orders() { return $this->hasMany(Order::class, 'user_id'); }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function permissions()
    {
        return Permission::query()
            ->whereHas('roles', fn ($q) => $q->whereIn('roles.id', $this->roles()->pluck('roles.id')))
            ->distinct();
    }

    public function isActive(): bool
    {
        return ($this->status ?: 'active') === 'active';
    }

    public function isSuperAdmin(): bool
    {
        return $this->is_admin || $this->roles()->where('slug', 'super-admin')->exists();
    }

    public function hasRole(string|array $roles): bool
    {
        if ($this->isSuperAdmin()) return true;
        $roles = is_array($roles) ? $roles : [$roles];
        return $this->roles()->whereIn('slug', $roles)->exists();
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) return true;
        return $this->permissions()->where('permissions.slug', $permission)->exists();
    }

    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->isSuperAdmin()) return true;
        return $this->permissions()->whereIn('permissions.slug', $permissions)->exists();
    }
}
