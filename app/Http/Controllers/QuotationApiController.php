<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use Illuminate\Http\Request;

class QuotationApiController extends Controller
{
    public function getData(Quotation $quotation)
    {
        try {
            $quotation->load(['items.product', 'customer']);
            return response()->json($quotation);
        } catch (\Exception $e) {
            \Log::error('Error loading quotation data: ' . $e->getMessage(), [
                'quotation_id' => $quotation->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'เกิดข้อผิดพลาดในการโหลดข้อมูลใบเสนอราคา'], 500);
        }
    }
}
