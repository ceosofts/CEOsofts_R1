<?php

namespace App\Domain\Settings\Models;

use App\Domain\Organization\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'is_system_admin',
        'language',
        'timezone',
        'last_login_at',
        'last_login_ip',
        'settings',
        'metadata',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'is_system_admin' => 'boolean',
        'last_login_at' => 'datetime',
        'settings' => 'json',
        'metadata' => 'json',
    ];

    /**
     * Get the companies associated with the user.
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_user')
                    ->withTimestamps()
                    ->withPivot('is_default');
    }

    /**
     * Get user's default company
     */
    public function defaultCompany()
    {
        return $this->companies()->wherePivot('is_default', true)->first();
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Update last login information
     */
    public function updateLastLogin($ip = null)
    {
        $this->last_login_at = now();
        $this->last_login_ip = $ip;
        return $this->save();
    }
}
