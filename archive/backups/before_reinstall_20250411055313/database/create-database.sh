#!/bin/bash

# สร้างฐานข้อมูล MySQL สำหรับ CEOsofts
echo "กำลังสร้างฐานข้อมูล ceosofts_db_R1..."

# ถ้าต้องการระบุรหัสผ่าน ให้ใช้ -p ตามด้วยรหัสผ่าน
mysql -u root <<EOF
CREATE DATABASE IF NOT EXISTS ceosofts_db_R1;
GRANT ALL PRIVILEGES ON ceosofts_db_R1.* TO 'root'@'localhost';
FLUSH PRIVILEGES;
EOF

# ตรวจสอบว่าคำสั่งทำงานเสร็จสมบูรณ์หรือไม่
if [ $? -eq 0 ]; then
  echo "สร้างฐานข้อมูล ceosofts_db_R1 สำเร็จแล้ว"
else
  echo "เกิดข้อผิดพลาดในการสร้างฐานข้อมูล"
  exit 1
fi

echo "สามารถใช้งานฐานข้อมูลได้แล้ว"
