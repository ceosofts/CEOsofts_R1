<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Sales\Models\Invoice;
use App\Domain\Sales\Models\InvoiceItem;
use App\Domain\Inventory\Models\Product;
use Illuminate\Support\Facades\Log;

class InvoiceItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all invoices that don't have items yet
        $invoices = Invoice::all();
        
        if ($invoices->isEmpty()) {
            Log::info('No invoices found to create invoice items for.');
            return;
        }

        Log::info('Starting to seed invoice items for ' . $invoices->count() . ' invoices.');
        
        foreach ($invoices as $invoice) {
            // Check if invoice already has items to prevent duplications
            if ($invoice->items()->count() > 0) {
                Log::info("Invoice #{$invoice->invoice_number} already has items. Skipping.");
                continue;
            }
            
            // If invoice came from an order, create invoice items based on order items
            if ($invoice->order_id) {
                $this->createInvoiceItemsFromOrder($invoice);
            } 
            // Otherwise, check if invoice has product data in its metadata
            else if ($invoice->metadata && isset(json_decode($invoice->metadata, true)['products'])) {
                $this->createInvoiceItemsFromMetadata($invoice);
            }
            // If no product data exists, create some random sample items
            else {
                $this->createSampleInvoiceItems($invoice);
            }
        }
        
        Log::info('Finished seeding invoice items.');
    }
    
    /**
     * Create invoice items from order items if the invoice is associated with an order
     */
    private function createInvoiceItemsFromOrder($invoice)
    {
        if (!$invoice->order || !$invoice->order->items) {
            return;
        }
        
        foreach ($invoice->order->items as $orderItem) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'order_item_id' => $orderItem->id,
                'product_id' => $orderItem->product_id,
                'description' => $orderItem->description,
                'quantity' => $orderItem->quantity,
                'unit' => $orderItem->unit,
                'unit_price' => $orderItem->unit_price,
                'discount_type' => $orderItem->discount_type,
                'discount_amount' => $orderItem->discount_amount,
                'tax_rate' => $invoice->tax_rate,
                'tax_amount' => ($orderItem->subtotal * ($invoice->tax_rate / 100)),
                'subtotal' => $orderItem->subtotal,
                'total' => $orderItem->total + ($orderItem->subtotal * ($invoice->tax_rate / 100)),
                'notes' => $orderItem->notes,
                'metadata' => json_encode([
                    'origin' => 'order_item',
                    'original_order_item_id' => $orderItem->id
                ])
            ]);
        }
        
        Log::info("Created invoice items from order for invoice #{$invoice->invoice_number}");
    }
    
    /**
     * Create invoice items from metadata if the invoice has product data stored in its metadata
     */
    private function createInvoiceItemsFromMetadata($invoice)
    {
        $metadata = json_decode($invoice->metadata, true);
        $products = $metadata['products'] ?? [];
        
        if (empty($products)) {
            return;
        }
        
        foreach ($products as $product) {
            // Try to find a matching product in the database
            $productModel = Product::where('name', 'like', '%' . $product['name'] . '%')->first();
            
            $invoiceItem = new InvoiceItem([
                'invoice_id' => $invoice->id,
                'product_id' => $productModel ? $productModel->id : null,
                'description' => $product['name'],
                'quantity' => $product['quantity'],
                'unit' => 'ชิ้น', // Default unit
                'unit_price' => $product['price'],
                'discount_type' => 'fixed',
                'discount_amount' => 0,
                'tax_rate' => $invoice->tax_rate,
                'tax_amount' => 0, // Will be calculated
                'subtotal' => $product['quantity'] * $product['price'],
                'total' => $product['total'], 
                'notes' => 'รายการสินค้าจากข้อมูล metadata',
                'metadata' => json_encode([
                    'origin' => 'metadata',
                    'original_product_name' => $product['name']
                ])
            ]);
            
            // Calculate totals (tax amount and total)
            $invoiceItem->calculateTotals();
            $invoiceItem->save();
        }
        
        Log::info("Created invoice items from metadata for invoice #{$invoice->invoice_number}");
    }
    
    /**
     * Create sample invoice items if the invoice has no associated data
     */
    private function createSampleInvoiceItems($invoice)
    {
        // Get some random products or create dummy data if no products exist
        $products = Product::inRandomOrder()->take(rand(1, 3))->get();
        
        if ($products->isEmpty()) {
            // Sample product data if no real products exist
            $dummyProducts = [
                ['name' => 'สินค้าตัวอย่าง A', 'price' => 1000],
                ['name' => 'สินค้าตัวอย่าง B', 'price' => 2500],
                ['name' => 'สินค้าตัวอย่าง C', 'price' => 3200],
                ['name' => 'สินค้าตัวอย่าง D', 'price' => 4500],
                ['name' => 'สินค้าตัวอย่าง E', 'price' => 5800],
            ];
            
            // Create 1-3 random items
            $numItems = rand(1, 3);
            $subtotal = 0;
            
            for ($i = 0; $i < $numItems; $i++) {
                $product = $dummyProducts[array_rand($dummyProducts)];
                $quantity = rand(1, 5);
                $price = $product['price'];
                $itemSubtotal = $price * $quantity;
                $subtotal += $itemSubtotal;
                
                $invoiceItem = new InvoiceItem([
                    'invoice_id' => $invoice->id,
                    'description' => $product['name'],
                    'quantity' => $quantity,
                    'unit' => 'ชิ้น',
                    'unit_price' => $price,
                    'discount_type' => 'fixed',
                    'discount_amount' => 0,
                    'tax_rate' => $invoice->tax_rate,
                    'tax_amount' => 0, // Will be calculated
                    'subtotal' => $itemSubtotal,
                    'total' => 0, // Will be calculated
                    'notes' => 'รายการสินค้าตัวอย่าง',
                    'metadata' => json_encode([
                        'origin' => 'sample',
                        'product_name' => $product['name']
                    ])
                ]);
                
                // Calculate totals (tax amount and total)
                $invoiceItem->calculateTotals();
                $invoiceItem->save();
            }
        } else {
            // Create invoice items from real products
            foreach ($products as $product) {
                $quantity = rand(1, 5);
                $price = $product->price ?? rand(500, 5000);
                $itemSubtotal = $price * $quantity;
                
                $invoiceItem = new InvoiceItem([
                    'invoice_id' => $invoice->id,
                    'product_id' => $product->id,
                    'description' => $product->name,
                    'quantity' => $quantity,
                    'unit' => $product->unit ?? 'ชิ้น',
                    'unit_price' => $price,
                    'discount_type' => 'fixed',
                    'discount_amount' => 0,
                    'tax_rate' => $invoice->tax_rate,
                    'tax_amount' => 0, // Will be calculated
                    'subtotal' => $itemSubtotal,
                    'total' => 0, // Will be calculated
                    'notes' => 'รายการสินค้าจริง',
                    'metadata' => json_encode([
                        'origin' => 'real_product',
                        'product_id' => $product->id
                    ])
                ]);
                
                // Calculate totals (tax amount and total)
                $invoiceItem->calculateTotals();
                $invoiceItem->save();
            }
        }
        
        Log::info("Created sample invoice items for invoice #{$invoice->invoice_number}");
    }
}