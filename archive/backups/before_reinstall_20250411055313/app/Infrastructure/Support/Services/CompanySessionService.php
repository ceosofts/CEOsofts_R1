<?php

namespace App\Infrastructure\Support\Services;

use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CompanySessionService
{
    protected $session;
    protected const COMPANY_SESSION_KEY = 'current_company_id';

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * กำหนดรหัสบริษัทปัจจุบันในเซสชัน
     *
     * @param int $companyId
     * @return void
     */
    public function setCurrentCompany(int $companyId): void
    {
        $this->session->put(self::COMPANY_SESSION_KEY, $companyId);

        // บันทึกลงในประวัติการใช้งาน
        $this->logCompanyChange($companyId);
    }

    /**
     * ดึงรหัสบริษัทปัจจุบันจากเซสชัน
     *
     * @return int|null
     */
    public function getCurrentCompanyId(): ?int
    {
        return $this->session->get(self::COMPANY_SESSION_KEY);
    }

    /**
     * บันทึกประวัติการเปลี่ยนบริษัท
     *
     * @param int $companyId
     * @return void
     */
    protected function logCompanyChange(int $companyId): void
    {
        if (Auth::check()) {
            Log::info('User switched company', [
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'company_id' => $companyId
            ]);
            
            // อาจจะบันทึกลงในตาราง activity_log หรือตารางอื่นๆ ถ้าต้องการ
        }
    }

    /**
     * ล้างบริษัทปัจจุบันออกจากเซสชัน
     *
     * @return void
     */
    public function clearCurrentCompany(): void
    {
        $this->session->forget(self::COMPANY_SESSION_KEY);
    }
}
