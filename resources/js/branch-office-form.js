document.addEventListener('DOMContentLoaded', function() {
    const isHeadquartersCheckbox = document.getElementById('is_headquarters');
    const taxBranchIdField = document.getElementById('tax_branch_id');
    const companySelect = document.getElementById('company_id');
    const managerSelect = document.getElementById('manager_id');
    const codeInput = document.getElementById('code');
    
    // ตั้งค่า default tax_branch_id เป็น 00000 ถ้าเป็นสำนักงานใหญ่
    function updateTaxBranchId() {
        if (isHeadquartersCheckbox && taxBranchIdField && isHeadquartersCheckbox.checked) {
            taxBranchIdField.value = '00000';
        }
    }
    
    // เรียกใช้ฟังก์ชันตอนโหลดหน้า
    updateTaxBranchId();
    
    // ตั้ง event listener สำหรับการเปลี่ยนแปลงช่อง is_headquarters
    if (isHeadquartersCheckbox) {
        isHeadquartersCheckbox.addEventListener('change', updateTaxBranchId);
    }
    
    // กรองผู้จัดการตามบริษัท
    function filterManagersByCompany() {
        if (!managerSelect || !companySelect) return;
        
        const companyId = companySelect.value;
        
        // เก็บตัวเลือกทั้งหมดของผู้จัดการไว้
        if (!managerSelect.dataset.initialized) {
            managerSelect.dataset.initialized = 'true';
            managerSelect.dataset.allOptions = managerSelect.innerHTML;
        }
        
        // ถ้าไม่ได้เลือกบริษัท ให้แสดงผู้จัดการทั้งหมด
        if (!companyId) {
            managerSelect.innerHTML = managerSelect.dataset.allOptions;
            return;
        }
        
        // เก็บตัวเลือก "ไม่มีผู้จัดการ" ไว้
        const defaultOption = managerSelect.querySelector('option[value=""]');
        managerSelect.innerHTML = '';
        
        if (defaultOption) {
            managerSelect.appendChild(defaultOption);
        }
        
        // กรองเฉพาะผู้จัดการที่อยู่ในบริษัทที่เลือก
        const allOptions = managerSelect.dataset.allOptions;
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = allOptions;
        
        const options = tempDiv.querySelectorAll('option');
        options.forEach(option => {
            if (option.value === '' || option.dataset.companyId === companyId) {
                managerSelect.appendChild(option.cloneNode(true));
            }
        });
    }
    
    // ตั้ง event listener สำหรับการเปลี่ยนแปลงบริษัท
    if (companySelect) {
        companySelect.addEventListener('change', filterManagersByCompany);
        
        // กรองข้อมูลตอนโหลดหน้า
        filterManagersByCompany();
    }
    
    // ทำการตรวจสอบความถูกต้องก่อน submit
    const form = document.querySelector('form[action*="branch-offices"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // ตรวจสอบว่าเลือกบริษัทหรือยัง
            if (companySelect && !companySelect.value) {
                isValid = false;
                alert('กรุณาเลือกบริษัท');
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // อัพเดทรหัสสาขาเมื่อเลือกบริษัท
    function updateBranchCode() {
        if (!companySelect || !codeInput) return;
        
        const companyId = companySelect.value;
        if (!companyId) return;
        
        // ถ้าเป็นสำนักงานใหญ่
        if (isHeadquartersCheckbox && isHeadquartersCheckbox.checked) {
            const companyPrefix = String(companyId).padStart(2, '0');
            codeInput.placeholder = `HQ-${companyPrefix}`;
            return;
        }
        
        // ถ้าเป็นสาขาปกติ
        fetch(`/api/branch-offices/next-code/${companyId}`)
            .then(response => response.json())
            .then(data => {
                if (data.code) {
                    codeInput.placeholder = data.code;
                }
            })
            .catch(error => {
                console.error('Error fetching next branch code:', error);
                
                // สร้างรหัสตัวอย่างในกรณีที่ API ไม่สามารถใช้งานได้
                const companyPrefix = String(companyId).padStart(2, '0');
                codeInput.placeholder = `BRA-${companyPrefix}-XXX`;
            });
    }
    
    // เรียกใช้งานเมื่อโหลดหน้า
    if (companySelect) {
        updateBranchCode();
        
        // เมื่อเลือกบริษัท ให้อัพเดตรหัสสาขา
        companySelect.addEventListener('change', updateBranchCode);
    }
    
    // เมื่อคลิกที่ checkbox สำนักงานใหญ่
    if (isHeadquartersCheckbox) {
        isHeadquartersCheckbox.addEventListener('change', updateBranchCode);
    }
});
