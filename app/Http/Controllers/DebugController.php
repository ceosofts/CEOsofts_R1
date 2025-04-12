<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DebugController extends Controller
{
    /**
     * แสดงข้อมูลบริษัทในรูปแบบพื้นฐาน
     *
     * @return \Illuminate\Http\Response
     */
    public function companies()
    {
        try {
            // ดึงข้อมูลจากฐานข้อมูลโดยตรง
            $companiesFromDB = DB::table('companies')->get();
            $companiesCount = $companiesFromDB->count();

            // ดึงข้อมูลผ่าน Model
            $companiesFromModel = Company::all();
            $modelCount = $companiesFromModel->count();

            // บันทึกข้อมูลลง log
            Log::info("DebugController: DB direct - {$companiesCount} companies, Model - {$modelCount} companies");

            // แสดงผลเรียบง่าย
            echo "<h1>ข้อมูลบริษัททั้งหมด</h1>";
            echo "<p>จำนวนบริษัทจาก DB โดยตรง: {$companiesCount}</p>";
            echo "<p>จำนวนบริษัทจาก Model: {$modelCount}</p>";

            echo "<h2>รายการบริษัท (จาก Model)</h2>";
            echo "<ul>";
            foreach ($companiesFromModel as $company) {
                echo "<li>ID: {$company->id}, ชื่อ: {$company->name}, สถานะ: " . ($company->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน') . "</li>";
            }
            echo "</ul>";

            echo "<h2>รายการบริษัท (จาก DB โดยตรง)</h2>";
            echo "<ul>";
            foreach ($companiesFromDB as $company) {
                echo "<li>ID: {$company->id}, ชื่อ: {$company->name}</li>";
            }
            echo "</ul>";

            return "";
        } catch (\Exception $e) {
            Log::error("DebugController Error: " . $e->getMessage());
            return response("เกิดข้อผิดพลาด: " . $e->getMessage(), 500);
        }
    }
}
