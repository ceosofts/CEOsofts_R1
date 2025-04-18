<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'quotation_id' => 'nullable|exists:quotations,id',
            'order_number' => 'required|string|unique:orders,order_number',
            'customer_po_number' => 'nullable|string', // เพิ่มฟิลด์ใหม่
            'order_date' => 'required|date',
            'delivery_date' => 'required|date|after_or_equal:order_date',
            'status' => ['required', Rule::in(['draft', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'])],
            'notes' => 'nullable|string',
            'payment_terms' => 'nullable|string',
            'shipping_address' => 'nullable|string',
            'shipping_method' => 'nullable|string',
            'shipping_cost' => 'nullable|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'discount_type' => 'nullable|in:fixed,percentage',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0',
            
            // รายการสินค้า
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.unit_price' => 'required|numeric|gt:0',
            'items.*.unit_id' => 'nullable|exists:units,id',
            'items.*.notes' => 'nullable|string',
        ];
    }
}
