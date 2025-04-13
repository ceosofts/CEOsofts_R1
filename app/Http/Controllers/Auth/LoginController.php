<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * แสดงหน้า login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * ดำเนินการ login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // ถ้ามีการเก็บ intended URL ไว้ ให้ redirect ไปยัง URL นั้น
            // ถ้าไม่มีให้ redirect ไปยัง dashboard
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'ข้อมูลที่ให้ไม่ตรงกับระบบของเรา',
        ])->onlyInput('email');
    }

    /**
     * ดำเนินการ logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
