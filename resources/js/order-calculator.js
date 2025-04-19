/**
 * OrderCalculator - ไฟล์ JavaScript สำหรับคำนวณตัวเลขในใบสั่งขาย
 */
class OrderCalculator {
    constructor() {
        // ตรวจสอบว่ามี DOM elements ตามที่ต้องการหรือไม่
        if (!document.getElementById('productsTable')) {
            return;
        }

        // ค้นหา elements
        this.productsList = document.getElementById('productsList');
        this.addProductBtn = document.getElementById('addProductBtn');
        this.discountType = document.getElementById('discount_type');
        this.discountAmount = document.getElementById('discount_amount');
        this.taxRate = document.getElementById('tax_rate');
        this.shippingCost = document.getElementById('shipping_cost');
        
        // กำหนด event listeners
        this.setupEventListeners();
        
        // คำนวณยอดรวมครั้งแรก
        this.calculateTotals();
    }
    
    setupEventListeners() {
        // เพิ่มรายการสินค้า
        this.addProductBtn.addEventListener('click', () => this.addProductRow());
        
        // Event delegation สำหรับปุ่มลบสินค้า
        this.productsList.addEventListener('click', (e) => this.handleRemoveProduct(e));
        
        // Initialize row events สำหรับแถวที่มีอยู่แล้ว
        document.querySelectorAll('.product-row').forEach(row => {
            this.initializeRowEvents(row);
        });
        
        // Event listeners สำหรับการคำนวณยอดรวม
        this.discountType.addEventListener('change', () => this.calculateTotals());
        this.discountAmount.addEventListener('input', () => this.calculateTotals());
        this.taxRate.addEventListener('input', () => this.calculateTotals());
        
        if (this.shippingCost) {
            this.shippingCost.addEventListener('input', () => this.calculateTotals());
        }
    }
    
    addProductRow() {
        const rowCount = this.productsList.querySelectorAll('tr.product-row').length;
        const productTemplate = document.getElementById('product-row-template').content.cloneNode(true);
        
        // กำหนด index ใหม่สำหรับ input fields
        productTemplate.querySelectorAll('[name^="products["]').forEach(input => {
            const name = input.getAttribute('name');
            const newName = name.replace(/products\[\d+\]/, `products[${rowCount}]`);
            input.setAttribute('name', newName);
        });
        
        this.productsList.appendChild(productTemplate);
        
        // กำหนด event listeners สำหรับแถวใหม่
        const newRow = this.productsList.lastElementChild;
        this.initializeRowEvents(newRow);
        
        // คำนวณยอดรวมใหม่
        this.calculateTotals();
    }
    
    handleRemoveProduct(e) {
        if (e.target.closest('.remove-product')) {
            const row = e.target.closest('.product-row');
            
            // ถ้ามีมากกว่า 1 รายการสินค้า จึงลบได้
            if (this.productsList.querySelectorAll('.product-row').length > 1) {
                row.remove();
                this.reindexProductRows();
                this.calculateTotals();
            } else {
                alert('ต้องมีอย่างน้อย 1 รายการสินค้า');
            }
        }
    }
    
    initializeRowEvents(row) {
        const productSelect = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity');
        const unitPriceInput = row.querySelector('.unit-price');
        
        productSelect.addEventListener('change', () => {
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const price = selectedOption.getAttribute('data-price') || 0;
            unitPriceInput.value = parseFloat(price).toFixed(2);
            this.updateRowTotal(row);
            this.calculateTotals();
        });
        
        quantityInput.addEventListener('input', () => {
            this.updateRowTotal(row);
            this.calculateTotals();
        });
        
        unitPriceInput.addEventListener('input', () => {
            this.updateRowTotal(row);
            this.calculateTotals();
        });
    }
    
    updateRowTotal(row) {
        const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
        const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
        const subtotal = quantity * unitPrice;
        row.querySelector('.subtotal').value = subtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    
    reindexProductRows() {
        const rows = this.productsList.querySelectorAll('.product-row');
        rows.forEach((row, index) => {
            row.querySelectorAll('[name^="products["]').forEach(input => {
                const name = input.getAttribute('name');
                const newName = name.replace(/products\[\d+\]/, `products[${index}]`);
                input.setAttribute('name', newName);
            });
        });
    }
    
    calculateTotals() {
        // คำนวณยอดรวมก่อนหักส่วนลด
        let subtotal = 0;
        document.querySelectorAll('.product-row').forEach(row => {
            const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
            subtotal += quantity * unitPrice;
        });
        
        // แสดงยอดรวมก่อนหักส่วนลด
        document.getElementById('subtotalDisplay').value = this.formatNumber(subtotal);
        document.getElementById('subtotal').value = subtotal.toFixed(2);
        
        // คำนวณส่วนลด
        const discountType = this.discountType.value;
        const discountAmount = parseFloat(this.discountAmount.value) || 0;
        let discountValue = 0;
        
        if (discountType === 'percentage') {
            discountValue = subtotal * (discountAmount / 100);
        } else {
            discountValue = discountAmount;
        }
        
        // คำนวณราคาสุทธิหลังหักส่วนลด
        const netTotal = subtotal - discountValue;
        
        // คำนวณภาษี
        const taxRate = parseFloat(this.taxRate.value) || 0;
        const taxAmount = netTotal * (taxRate / 100);
        document.getElementById('tax_amount_display').value = this.formatNumber(taxAmount);
        document.getElementById('tax_amount').value = taxAmount.toFixed(2);
        
        // คำนวณยอดรวมทั้งหมด
        const shippingCost = parseFloat(document.getElementById('shipping_cost')?.value || 0);
        const totalAmount = netTotal + taxAmount + shippingCost;
        
        document.getElementById('total_amount_display').value = this.formatNumber(totalAmount);
        document.getElementById('total_amount').value = totalAmount.toFixed(2);
    }
    
    formatNumber(number) {
        return number.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
}

// Initialize เมื่อ DOM โหลดเสร็จ
document.addEventListener('DOMContentLoaded', () => {
    new OrderCalculator();
});

export default OrderCalculator;
