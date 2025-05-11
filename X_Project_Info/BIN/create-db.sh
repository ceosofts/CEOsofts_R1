#!/bin/bash
# สคริปต์สำหรับสร้างฐานข้อมูล SQLite และกำหนดสิทธิ์ที่เหมาะสม

# สร้างไฟล์ SQLite ใหม่
touch database/ceosofts_db_R1.sqlite

# กำหนดสิทธิ์
chmod 664 database/ceosofts_db_R1.sqlite

echo "Created SQLite database: database/ceosofts_db_R1.sqlite"

# แสดงไฟล์ที่มีอยู่ในโฟลเดอร์ database
ls -la database/*.sqlite
