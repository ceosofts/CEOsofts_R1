<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * แสดงหน้าหลักของเว็บไซต์
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('welcome');
    }

    /**
     * แสดงหน้าเกี่ยวกับเรา
     *
     * @return \Illuminate\View\View
     */
    public function about()
    {
        return view('about');
    }
}
