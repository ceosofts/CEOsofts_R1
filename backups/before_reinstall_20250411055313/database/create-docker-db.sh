#!/bin/bash

# สร้างฐานข้อมูลใน Container MySQL ของ Docker
echo "กำลังสร้างฐานข้อมูล ceosofts_db_R1 ใน Docker container..."

# เช็คว่า container mysql กำลังทำงานอยู่หรือไม่
if ! docker ps | grep -q "mysql"; then
  echo "ไม่พบ container mysql ที่กำลังทำงาน"
  echo "กรุณาเริ่ม Laravel Sail ก่อน: ./vendor/bin/sail up -d"
  exit 1
fi

# สร้างฐานข้อมูลใน container
docker exec mysql mysql -u root -ppassword -e "CREATE DATABASE IF NOT EXISTS ceosofts_db_R1;"
docker exec mysql mysql -u root -ppassword -e "GRANT ALL PRIVILEGES ON ceosofts_db_R1.* TO 'sail'@'%';"
docker exec mysql mysql -u root -ppassword -e "FLUSH PRIVILEGES;"

# ตรวจสอบว่าคำสั่งทำงานเสร็จสมบูรณ์หรือไม่
if [ $? -eq 0 ]; then
  echo "สร้างฐานข้อมูล ceosofts_db_R1 สำเร็จแล้ว"
else
  echo "เกิดข้อผิดพลาดในการสร้างฐานข้อมูล"
  exit 1
fi

# แสดงฐานข้อมูลที่มีอยู่
echo "รายชื่อฐานข้อมูล:"
docker exec mysql mysql -u root -ppassword -e "SHOW DATABASES;"
