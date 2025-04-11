# การตั้งค่า OrbStack สำหรับ Laravel Sail บน macOS

OrbStack เป็นทางเลือกที่ดีที่สุดแทน Docker Desktop บน macOS เนื่องจากเบากว่า เร็วกว่า และใช้ทรัพยากรน้อยกว่า

## ขั้นตอนการติดตั้ง OrbStack

1. **ดาวน์โหลด OrbStack**

    - ไปที่ [orbstack.dev](https://orbstack.dev/)
    - ดาวน์โหลดไฟล์ติดตั้ง

2. **ติดตั้ง OrbStack**

    - เปิดไฟล์ `.dmg` ที่ดาวน์โหลด
    - ลาก OrbStack ไปยังโฟลเดอร์ Applications
    - เปิด OrbStack จากโฟลเดอร์ Applications
    - ทำตามขั้นตอนการตั้งค่าเริ่มต้น

3. **ตรวจสอบการติดตั้ง**

    ```bash
    # ตรวจสอบว่า docker command พร้อมใช้งาน
    docker --version

    # ตรวจสอบว่า docker-compose พร้อมใช้งาน
    docker-compose --version
    ```

## การใช้ OrbStack กับ Laravel Sail

OrbStack ติดตั้งคำสั่ง Docker ให้โดยอัตโนมัติ ดังนั้นหลังจากติดตั้งแล้ว คุณสามารถใช้ Laravel Sail ได้ทันที:

```bash
# กลับไปที่โปรเจค Laravel
cd /Users/iwasbornforthis/MyProject/CEOsofts_R1

# เริ่มการทำงาน containers
./vendor/bin/sail up -d

# ตรวจสอบสถานะ
./vendor/bin/sail ps
```

## ข้อดีของ OrbStack เทียบกับ Docker Desktop

1. **ทรัพยากรระบบ**: ใช้ RAM และ CPU น้อยกว่ามาก
2. **ความเร็ว**: เริ่มทำงานเร็วกว่าและมอนต์โฟลเดอร์เร็วกว่า (file I/O เร็วกว่า 2-4 เท่า)
3. **การติดตั้ง**: ไฟล์ติดตั้งเล็กกว่าและไม่ต้องการสิทธิ์ admin
4. **ประสบการณ์**: UI ที่เรียบง่ายแต่มีประสิทธิภาพ

## ข้อควรระวังและเคล็ดลับ

1. **ความเข้ากันได้**: OrbStack รองรับ Docker API มาตรฐาน ดังนั้นควรทำงานกับ Laravel Sail ได้โดยไม่มีปัญหา
2. **Port forwarding**: OrbStack จะทำ port forwarding ให้โดยอัตโนมัติ คุณสามารถเข้าถึงแอพ Laravel ได้ที่ http://localhost
3. **File sharing**: OrbStack แชร์โฟลเดอร์ `/Users` โดยอัตโนมัติ ไม่จำเป็นต้องตั้งค่าเพิ่มเติม
4. **การแก้ไขปัญหา**: ถ้าเกิดปัญหา ลองรีสตาร์ท OrbStack จาก menu bar icon

## คำสั่งเพิ่มเติมสำหรับ OrbStack

```bash
# แสดงหน้า dashboard (ทางเบราวเซอร์)
orb dashboard

# จัดการ machines
orb machine ls
orb machine stop

# เปิด shell ใน container
orb shell [container_name]

# ดู logs
orb logs [container_name]
```

## การถอนการติดตั้ง OrbStack (ถ้าจำเป็น)

```bash
# วิธีถอนการติดตั้ง OrbStack
orb uninstall
```
