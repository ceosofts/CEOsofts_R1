<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * แสดงรายการลูกค้าทั้งหมด
     */
    public function index()
    {
        $customers = []; // ในอนาคตจะเปลี่ยนเป็น Customer::all() หรือมีการ query ตามเงื่อนไข

        return view('customers.index', compact('customers'));
    }

    /**
     * แสดงหน้าฟอร์มสำหรับสร้างลูกค้าใหม่
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * เก็บข้อมูลลูกค้าที่สร้างใหม่ลงในฐานข้อมูล
     */
    public function store(Request $request)
    {
        // Logic for storing a customer
        return redirect()->route('customers.index');
    }

    /**
     * แสดงข้อมูลของลูกค้าตาม ID
     */
    public function show($id)
    {
        // In the future: $customer = Customer::findOrFail($id);
        $customer = null; // Placeholder

        return view('customers.show', compact('customer'));
    }

    /**
     * แสดงหน้าฟอร์มสำหรับแก้ไขข้อมูลลูกค้า
     */
    public function edit($id)
    {
        // In the future: $customer = Customer::findOrFail($id);
        $customer = null; // Placeholder

        return view('customers.edit', compact('customer'));
    }

    /**
     * อัปเดตข้อมูลลูกค้าในฐานข้อมูล
     */
    public function update(Request $request, $id)
    {
        // Logic for updating the customer
        return redirect()->route('customers.index');
    }

    /**
     * ลบลูกค้าออกจากฐานข้อมูล
     */
    public function destroy($id)
    {
        // Logic for deleting the customer
        return redirect()->route('customers.index');
    }
}
