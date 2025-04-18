<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $orderDate = $this->faker->dateTimeBetween('-3 months', 'now');
        $subtotal = $this->faker->randomFloat(2, 1000, 50000);
        $discountType = $this->faker->randomElement([null, 'fixed', 'percentage']);
        $discountAmount = 0;
        
        if ($discountType === 'fixed') {
            $discountAmount = $this->faker->randomFloat(2, 100, min(5000, $subtotal * 0.2));
        } elseif ($discountType === 'percentage') {
            $discountPercentage = $this->faker->numberBetween(5, 20);
            $discountAmount = $subtotal * ($discountPercentage / 100);
        }
        
        $netTotal = $subtotal - $discountAmount;
        $taxRate = $this->faker->randomElement([0, 7]);
        $taxAmount = $taxRate ? $netTotal * ($taxRate / 100) : 0;
        $shippingCost = $this->faker->randomFloat(2, 0, 1000);
        $totalAmount = $netTotal + $taxAmount + $shippingCost;
        
        return [
            'company_id' => 1,
            'customer_id' => Customer::factory(),
            'quotation_id' => null,
            'order_number' => 'SO' . date('Ym') . $this->faker->unique()->numerify('###'),
            'customer_po_number' => $this->faker->boolean(70) ? 'PO' . $this->faker->bothify('##??##') : null,
            'order_date' => $orderDate,
            'delivery_date' => $this->faker->dateTimeBetween($orderDate, '+2 weeks'),
            'status' => $this->faker->randomElement(['draft', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled']),
            'notes' => $this->faker->optional(0.7)->sentence(),
            'payment_terms' => $this->faker->optional()->randomElement(['ชำระเงินทันที', '7 วัน', '15 วัน', '30 วัน']),
            'shipping_address' => $this->faker->optional(0.8)->address(),
            'shipping_method' => $this->faker->optional()->randomElement(['รถบริษัท', 'Kerry', 'Flash', 'ไปรษณีย์ไทย']),
            'shipping_cost' => $shippingCost,
            'subtotal' => $subtotal,
            'discount_type' => $discountType,
            'discount_amount' => $discountAmount,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'created_by' => User::factory(),
        ];
    }

    // สถานะต่างๆ
    public function draft(): self
    {
        return $this->state(['status' => 'draft']);
    }
    
    public function confirmed(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'confirmed',
                'confirmed_by' => User::factory(),
                'confirmed_at' => $this->faker->dateTimeBetween($attributes['order_date'], 'now'),
            ];
        });
    }
    
    // เพิ่ม state อื่นๆ ตามความเหมาะสม
}
