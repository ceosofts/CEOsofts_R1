<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Inventory\Models\Unit;
use App\Domain\Organization\Models\Company;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();
        foreach ($companies as $company) {
            $this->createUnitsForCompany($company->id);
        }
    }

    private function createUnitsForCompany($companyId)
    {
        $units = [
            [
                'company_id' => $companyId,
                'name' => 'ชิ้น',
                'code' => 'PCS',
                'symbol' => 'ชิ้น',
                'base_unit_id' => null,
                'conversion_factor' => 1.0,
                'is_base_unit' => true,
                'is_active' => true,
            ],
            [
                'company_id' => $companyId,
                'name' => 'กล่อง',
                'code' => 'BOX',
                'symbol' => 'กล่อง',
                'conversion_factor' => 12.0,
                'is_base_unit' => false,
                'is_active' => true,
            ],
            [
                'company_id' => $companyId,
                'name' => 'กิโลกรัม',
                'code' => 'KG',
                'symbol' => 'กก.',
                'base_unit_id' => null,
                'conversion_factor' => 1.0,
                'is_base_unit' => true,
                'is_active' => true,
            ],
            [
                'company_id' => $companyId,
                'name' => 'กรัม',
                'code' => 'G',
                'symbol' => 'ก.',
                'conversion_factor' => 0.001,
                'is_base_unit' => false,
                'is_active' => true,
            ],
        ];

        foreach ($units as $unit) {
            $createdUnit = Unit::firstOrCreate(
                ['company_id' => $companyId, 'code' => $unit['code']],
                $unit
            );

            // Set base_unit_id for non-base units
            if (!$unit['is_base_unit']) {
                $baseUnit = Unit::where('company_id', $companyId)
                    ->where('is_base_unit', true)
                    ->first();
                if ($baseUnit) {
                    $createdUnit->base_unit_id = $baseUnit->id;
                    $createdUnit->save();
                }
            }
        }
    }
}
