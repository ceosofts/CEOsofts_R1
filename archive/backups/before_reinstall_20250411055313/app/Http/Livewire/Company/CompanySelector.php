<?php

namespace App\Http\Livewire\Company;

use App\Infrastructure\Support\Services\CompanySessionService;
use App\Models\Company;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class CompanySelector extends Component
{
    use AuthorizesRequests;

    public $companies;
    public $currentCompany;

    protected $listeners = ['refreshCompanySelector' => '$refresh'];

    public function mount()
    {
        $this->loadCompanies();
        $this->loadCurrentCompany();
    }

    protected function loadCompanies()
    {
        // โหลดเฉพาะบริษัทที่ผู้ใช้มีสิทธิ์เข้าถึง
        $this->companies = Company::active()->get();
    }

    protected function loadCurrentCompany()
    {
        $companyService = app(CompanySessionService::class);
        $companyId = $companyService->getCurrentCompanyId();

        if ($companyId) {
            $this->currentCompany = Company::find($companyId);
        } else {
            // ถ้ายังไม่เลือกบริษัท และมีบริษัทอยู่ ให้เลือกบริษัทแรก
            if ($this->companies->isNotEmpty()) {
                $this->currentCompany = $this->companies->first();
                $companyService->setCurrentCompany($this->currentCompany->id);
            } else {
                $this->currentCompany = null;
            }
        }
    }

    public function selectCompany($companyId)
    {
        $company = Company::findOrFail($companyId);
        
        // ตรวจสอบสิทธิ์การเข้าถึงบริษัท
        $this->authorize('view', $company);
        
        // ตั้งค่าบริษัทปัจจุบัน
        app(CompanySessionService::class)->setCurrentCompany($companyId);
        
        // แจ้งเตือนว่ามีการเปลี่ยนบริษัท
        $this->dispatch('company-changed');
    }

    public function render()
    {
        return view('livewire.company.company-selector');
    }
}
