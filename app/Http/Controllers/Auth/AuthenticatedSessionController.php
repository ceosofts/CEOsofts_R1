
// ในฟังก์ชันหลังจาก login สำเร็จ
protected function authenticated(Request $request, $user)
{
    // ตั้งค่า company_id ให้กับ session
    if ($user->company_id) {
        session(['company_id' => $user->company_id]);
    } else {
        // ถ้า user ไม่มี company_id ให้ใช้ค่าเริ่มต้น (บริษัทแรกในระบบ)
        $defaultCompany = \App\Models\Company::first();
        if ($defaultCompany) {
            session(['company_id' => $defaultCompany->id]);
        }
    }
    
    return redirect()->intended(RouteServiceProvider::HOME);
}
