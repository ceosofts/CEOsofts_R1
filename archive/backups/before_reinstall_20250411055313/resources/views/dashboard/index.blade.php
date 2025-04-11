<x-app-layout>
    <x-slot name="header">
        แดชบอร์ด
    </x-slot>

    <div class="space-y-6">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <x-ui.stat-card 
                title="พนักงานทั้งหมด" 
                value="{{ $stats['employee_count'] }}" 
                icon="users"
                color="blue"
            />
            
            <x-ui.stat-card 
                title="ลูกค้าทั้งหมด" 
                value="{{ $stats['customer_count'] }}" 
                icon="user-group"
                color="green"
            />
            
            <x-ui.stat-card 
                title="ใบแจ้งหนี้" 
                value="{{ $stats['invoice_count'] }}" 
                subtitle="฿{{ number_format($stats['invoice_total'], 2) }}"
                icon="document-text"
                color="purple"
            />
            
            <x-ui.stat-card 
                title="ออเดอร์ที่รอดำเนินการ" 
                value="{{ $stats['pending_order_count'] }}" 
                icon="clock"
                color="amber"
            />
            
            <x-ui.stat-card 
                title="สินค้าใกล้หมด" 
                value="{{ $stats['low_stock_products'] }}" 
                icon="exclamation-triangle"
                color="red"
            />
        </div>
        
        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Activities -->
            <div class="lg:col-span-1">
                <x-ui.card title="กิจกรรมล่าสุด">
                    <div class="flow-root">
                        <ul class="-my-5 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($recentActivities as $activity)
                                <li class="py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-primary-100 text-primary-700">
                                                <span class="text-sm font-medium">{{ strtoupper(substr($activity->event, 0, 1)) }}</span>
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                                {{ $activity->description ?? $activity->event }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                                {{ $activity->user->name ?? 'System' }} &bull; {{ $activity->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="py-4">
                                    <div class="text-center text-gray-500 dark:text-gray-400">
                                        ไม่มีกิจกรรมล่าสุด
                                    </div>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </x-ui.card>
            </div>
            
            <!-- Charts & Quick Actions -->
            <div class="lg:col-span-2 grid gap-6">
                <!-- Sales Chart -->
                <livewire:dashboard.sales-chart />
                
                <!-- Quick Actions -->
                <x-ui.card title="ดำเนินการด่วน">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <x-ui.quick-action-button
                            route="quotations.create"
                            icon="document-plus"
                            label="สร้างใบเสนอราคา"
                        />
                        
                        <x-ui.quick-action-button
                            route="orders.create"
                            icon="shopping-cart"
                            label="สร้างคำสั่งซื้อ"
                        />
                        
                        <x-ui.quick-action-button
                            route="invoices.create"
                            icon="document-text"
                            label="สร้างใบแจ้งหนี้"
                        />
                        
                        <x-ui.quick-action-button
                            route="customers.create"
                            icon="user-plus"
                            label="เพิ่มลูกค้า"
                        />
                        
                        <x-ui.quick-action-button
                            route="products.create"
                            icon="plus"
                            label="เพิ่มสินค้า"
                        />
                        
                        <x-ui.quick-action-button
                            route="employees.create"
                            icon="user-circle"
                            label="เพิ่มพนักงาน"
                        />
                    </div>
                </x-ui.card>
            </div>
        </div>
    </div>
</x-app-layout>
