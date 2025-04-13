<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quotation;

class QuotationController extends Controller
{
    /**
     * แสดงรายการใบเสนอราคาทั้งหมด
     */
    public function index()
    {
        $quotations = []; // ในอนาคตจะใช้ Quotation::all()

        return view('quotations.index', compact('quotations'));
    }

    /**
     * แสดงหน้าฟอร์มสำหรับสร้างใบเสนอราคาใหม่
     */
    public function create()
    {
        return view('quotations.create');
    }

    /**
     * เก็บข้อมูลใบเสนอราคาที่สร้างใหม่ลงในฐานข้อมูล
     */
    public function store(Request $request)
    {
        // Logic for storing a quotation
        return redirect()->route('quotations.index');
    }

    /**
     * แสดงข้อมูลของใบเสนอราคาตาม ID
     */
    public function show($id)
    {
        // In the future: $quotation = Quotation::findOrFail($id);
        $quotation = null; // Placeholder

        return view('quotations.show', compact('quotation'));
    }

    /**
     * แสดงหน้าฟอร์มสำหรับแก้ไขข้อมูลใบเสนอราคา
     */
    public function edit($id)
    {
        // In the future: $quotation = Quotation::findOrFail($id);
        $quotation = null; // Placeholder

        return view('quotations.edit', compact('quotation'));
    }

    /**
     * อัปเดตข้อมูลใบเสนอราคาในฐานข้อมูล
     */
    public function update(Request $request, $id)
    {
        // Logic for updating the quotation
        return redirect()->route('quotations.index');
    }

    /**
     * ลบใบเสนอราคาออกจากฐานข้อมูล
     */
    public function destroy($id)
    {
        // Logic for deleting the quotation
        return redirect()->route('quotations.index');
    }
}
