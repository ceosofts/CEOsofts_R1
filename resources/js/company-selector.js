document.addEventListener('DOMContentLoaded', function() {
    // ดึงปุ่มเลือกบริษัททั้งหมด
    const companySelectors = document.querySelectorAll('.company-selector-ajax');
    
    if (companySelectors.length > 0) {
        companySelectors.forEach(selector => {
            selector.addEventListener('click', function(e) {
                e.preventDefault();
                
                const companyId = this.getAttribute('data-company-id');
                const url = this.getAttribute('href');
                
                // แสดง loading state
                document.getElementById('employee-data-container').innerHTML = '<div class="text-center py-10"><div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div><p class="mt-2">กำลังโหลดข้อมูล...</p></div>';
                
                // ส่งคำขอ AJAX เพื่อเปลี่ยน company และรับข้อมูลใหม่
                fetch('/api/set-company/' + companyId + '/get-employees', {
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // อัพเดทหน้า UI โดยไม่ต้อง refresh
                        document.getElementById('employee-data-container').innerHTML = data.html;
                        
                        // อัพเดท URL โดยไม่ refresh หน้า
                        window.history.pushState({}, '', url);
                        
                        // อัพเดทสถานะตัวเลือกบริษัท
                        document.querySelectorAll('.company-card').forEach(card => {
                            if (card.getAttribute('data-company-id') == companyId) {
                                card.classList.add('bg-blue-50', 'border-blue-300');
                                card.classList.remove('bg-white', 'border-gray-200');
                                card.querySelector('.status-badge').innerHTML = '<span class="px-2 py-1 bg-blue-500 text-white text-xs rounded-full">กำลังเลือก</span>';
                            } else {
                                card.classList.remove('bg-blue-50', 'border-blue-300');
                                card.classList.add('bg-white', 'border-gray-200');
                                card.querySelector('.status-badge').innerHTML = '<span class="px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded-full">คลิกเพื่อเลือก</span>';
                            }
                        });
                    } else {
                        console.error('เกิดข้อผิดพลาดในการเปลี่ยนบริษัท');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
    }
});
