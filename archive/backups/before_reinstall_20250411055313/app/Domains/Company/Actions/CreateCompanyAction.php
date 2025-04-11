<?php

namespace App\Domains\Company\Actions;

use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateCompanyAction
{
    /**
     * Create a new company record
     *
     * @param array $data
     * @return Company
     */
    public function execute(array $data): Company
    {
        try {
            DB::beginTransaction();
            
            $company = new Company();
            $company->name = $data['name'];
            $company->tax_id = $data['tax_id'] ?? null;
            $company->address = $data['address'] ?? null;
            $company->phone = $data['phone'] ?? null;
            $company->email = $data['email'] ?? null;
            $company->website = $data['website'] ?? null;
            $company->logo = $data['logo'] ?? null;
            $company->is_active = $data['is_active'] ?? true;
            $company->save();
            
            // Create default departments, positions, etc.
            // You might call other actions here
            
            DB::commit();
            
            return $company;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create company: ' . $e->getMessage(), [
                'exception' => $e,
                'data' => $data
            ]);
            
            throw $e;
        }
    }
}
