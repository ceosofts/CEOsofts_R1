<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * แสดงรายการใบสั่งขายทั้งหมด
     */
    public function index()
    {
        $orders = []; // ในอนาคตจะใช้ Order::all()

        return view('orders.index', compact('orders'));
    }

    /**
     * แสดงหน้าฟอร์มสำหรับสร้างใบสั่งขายใหม่
     */
    public function create()
    {
        return view('orders.create');
    }

    /**
     * เก็บข้อมูลใบสั่งขายที่สร้างใหม่ลงในฐานข้อมูล
     */
    public function store(Request $request)
    {
        // Logic for storing an order
        return redirect()->route('orders.index');
    }

    /**
     * แสดงข้อมูลของใบสั่งขายตาม ID
     */
    public function show($id)
    {
        // In the future: $order = Order::findOrFail($id);
        $order = null; // Placeholder

        return view('orders.show', compact('order'));
    }

    /**
     * แสดงหน้าฟอร์มสำหรับแก้ไขข้อมูลใบสั่งขาย
     */
    public function edit($id)
    {
        // In the future: $order = Order::findOrFail($id);
        $order = null; // Placeholder

        return view('orders.edit', compact('order'));
    }

    /**
     * อัปเดตข้อมูลใบสั่งขายในฐานข้อมูล
     */
    public function update(Request $request, $id)
    {
        // Logic for updating the order
        return redirect()->route('orders.index');
    }

    /**
     * ลบใบสั่งขายออกจากฐานข้อมูล
     */
    public function destroy($id)
    {
        // Logic for deleting the order
        return redirect()->route('orders.index');
    }
}
