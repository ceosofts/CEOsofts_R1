// ตรวจสอบว่ามีการนำเข้า Alpine.js หรือไม่
import Alpine from 'alpinejs';
import './bootstrap';
import './auth'; // เพิ่มไฟล์ auth.js
import './order-calculator.js';

// Initialize Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Handle mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.querySelector('.sm\\:hidden button');
    const mobileMenu = document.querySelector('.sm\\:hidden > div');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            const isVisible = mobileMenu.classList.contains('block');
            
            if (isVisible) {
                mobileMenu.classList.remove('block');
                mobileMenu.classList.add('hidden');
            } else {
                mobileMenu.classList.remove('hidden');
                mobileMenu.classList.add('block');
            }
        });
    }
});
