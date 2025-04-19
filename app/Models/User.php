<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Company;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The guard that should be used by default.
     *
     * @var string
     */
    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The companies that belong to the user.
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }
    
    /**
     * Get the user's current company.
     */
    public function getCurrentCompanyAttribute()
    {
        $companyId = session('current_company_id');
        
        if ($companyId) {
            return $this->companies->find($companyId);
        }
        
        // ถ้าไม่มีบริษัทในเซสชัน ให้ใช้บริษัทแรก (ถ้ามี)
        return $this->companies->first();
    }

    /**
     * ตรวจสอบว่าผู้ใช้มีบทบาทเป็น admin หรือ superadmin
     *
     * @return bool
     */
    public function isAdministrator()
    {
        return $this->hasRole(['superadmin', 'admin']);
    }

    /**
     * Get all companies accessible by the user
     */
    public function getAccessibleCompanies()
    {
        // Admin and superadmin can access all companies
        if ($this->isAdministrator()) {
            return Company::all();
        }
        
        // Regular users can only access their associated companies
        return $this->companies;
    }

    /**
     * Check if user has admin roles
     *
     * @return bool
     */
    public function hasAdminRole()
    {
        return $this->hasRole(['admin', 'superadmin']);
    }
}
