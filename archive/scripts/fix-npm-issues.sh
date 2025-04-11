#!/bin/bash

echo "===== ตรวจสอบและแก้ไขปัญหา npm สำหรับ CEOsofts_R1 ====="

# 1. ตรวจสอบเวอร์ชัน node และ npm
echo "ตรวจสอบเวอร์ชันของ Node.js และ npm..."
echo "Node version:"
node -v
echo "npm version:"
npm -v

# 2. ลบโฟลเดอร์ node_modules และ package-lock.json
echo "กำลังลบโฟลเดอร์ node_modules และไฟล์ package-lock.json..."
rm -rf node_modules package-lock.json

# 3. ล้าง npm cache
echo "กำลังล้าง npm cache..."
npm cache clean --force

# 4. ติดตั้ง dependencies
echo "กำลังติดตั้ง dependencies..."
npm install

# 5. ตรวจสอบว่า Vite ติดตั้งอยู่หรือไม่
echo "กำลังตรวจสอบ Vite..."
if command -v ./node_modules/.bin/vite &> /dev/null; then
    echo "Vite ติดตั้งเรียบร้อยแล้ว"
else
    echo "กำลังติดตั้ง Vite..."
    npm install vite --save-dev
fi

# 6. ทดสอบคำสั่ง vite
echo "กำลังทดสอบคำสั่ง Vite..."
./node_modules/.bin/vite --version

echo "===== การแก้ไขปัญหาเสร็จสิ้น ====="
echo ""
echo "คุณสามารถลองรันคำสั่งต่อไปนี้:"
echo "npm run dev"
echo ""
echo "หากวิธีการนี้ไม่ได้ผล คุณสามารถลองทำตามขั้นตอนต่อไปนี้:"
echo "1. ตรวจสอบให้แน่ใจว่า Node.js เวอร์ชัน 16+ (node -v)"
echo "2. ลองรันคำสั่ง: npx vite"
echo "3. ตรวจสอบความถูกต้องของไฟล์ package.json"

# ทำให้ไฟล์นี้สามารถรันได้
chmod +x fix-npm-issues.sh

# ทดสอบรัน vite ด้วย npx
echo ""
echo "กำลังทดสอบรัน Vite ด้วย npx..."
npx vite --version

# ตรวจสอบว่าสามารถรัน npm run dev ได้หรือไม่
echo ""
echo "กำลังทดสอบ npm run dev..."
if npm run dev -- --help > /dev/null 2>&1; then
    echo "npm run dev สามารถทำงานได้ คุณสามารถรันคำสั่ง 'npm run dev' ได้ตามปกติ"
else
    echo "ยังมีปัญหากับคำสั่ง npm run dev กรุณาตรวจสอบความถูกต้องของไฟล์ package.json และ vite.config.js"
fi

echo ""
echo "หากคุณยังมีปัญหา กรุณารัน: bash dev-start.sh"
