<?php

namespace App\Domain\Settings\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CompanySessionService
{
    /**
     * Get the current company ID from session or from the user's first company.
     *
     * @return int|null
     */
    public function getCurrentCompanyId(): ?int
    {
        // First check if we have a company ID in the session
        if (Session::has('current_company_id')) {
            return Session::get('current_company_id');
        }
        
        // If not, check if we have a company ID in the request header
        $request = request();
        if ($request->hasHeader('X-Company-Id')) {
            $companyId = (int) $request->header('X-Company-Id');
            if ($this->userHasAccessToCompany($companyId)) {
                $this->setCurrentCompanyId($companyId);
                return $companyId;
            }
        }
        
        // If not, get the first company associated with the user
        if (Auth::check()) {
            $user = Auth::user();
            $firstCompany = $user->companies()->first();
            
            if ($firstCompany) {
                $this->setCurrentCompanyId($firstCompany->id);
                return $firstCompany->id;
            }
        }
        
        return null;
    }
    
    /**
     * Set the current company ID in session.
     *
     * @param int $companyId
     * @return void
     */
    public function setCurrentCompanyId(int $companyId): void
    {
        if ($this->userHasAccessToCompany($companyId)) {
            Session::put('current_company_id', $companyId);
        }
    }
    
    /**
     * Check if the authenticated user has access to the given company.
     *
     * @param int $companyId
     * @return bool
     */
    public function userHasAccessToCompany(int $companyId): bool
    {
        if (!Auth::check()) {
            return false;
        }
        
        $user = Auth::user();
        
        // If the user is a superadmin, they have access to all companies
        if ($user->hasRole('superadmin')) {
            return true;
        }
        
        // Otherwise, check if the user is associated with this company
        return $user->companies()->where('companies.id', $companyId)->exists();
    }
    
    /**
     * Get all companies the user has access to.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserCompanies()
    {
        if (!Auth::check()) {
            return collect();
        }
        
        $user = Auth::user();
        
        // If the user is a superadmin, return all companies
        if ($user->hasRole('superadmin')) {
            return \App\Domain\Organization\Models\Company::all();
        }
        
        // Otherwise, return the companies associated with this user
        return $user->companies;
    }
}
