# คู่มือการเชื่อมต่อฐานข้อมูล CEOsofts

จากการทดสอบเห็นว่ามีความสับสนในการเชื่อมต่อกับฐานข้อมูล ในเอกสารนี้จะอธิบายวิธีการเชื่อมต่อฐานข้อมูลที่ถูกต้องในสถานการณ์ต่างๆ

## สรุปผลการทดสอบ

1. **คำสั่งปกติ:** `php artisan db:show`

    - **ผลลัพธ์:** เกิดข้อผิดพลาด "getaddrinfo for mysql failed"
    - **สาเหตุ:** ไฟล์ `.env` ระบุ `DB_HOST=mysql` ซึ่งใช้ได้เฉพาะใน Docker

2. **คำสั่งผ่าน Sail:** `./vendor/bin/sail artisan db:show`

    - **ผลลัพธ์:** เกิดข้อผิดพลาด "Access denied for user 'sail'@'%' to database 'ceosofts'"
    - **สาเหตุ:** ฐานข้อมูล 'ceosofts' ยังไม่ได้ถูกสร้างหรือ user 'sail' ไม่มีสิทธิ์เข้าถึง

3. **คำสั่งผ่าน ENV file:** `php -d variables_order=EGPCS artisan db:show --env=local`

    - **ผลลัพธ์:** สำเร็จ! แสดงข้อมูลฐานข้อมูลทั้งหมด
    - **สาเหตุความสำเร็จ:** ใช้ `.env.local` ซึ่งมี `DB_HOST=127.0.0.1` และ `DB_USERNAME=root`

4. **คำสั่งผ่าน alias:** `alias artisan-local="DB_HOST=127.0.0.1 php artisan"`
    - **ผลลัพธ์:** เกิดข้อผิดพลาด "Access denied for user 'sail'@'localhost'"
    - **สาเหตุ:** แก้เฉพาะ host แต่ยังใช้ username 'sail' จาก `.env` ซึ่งไม่มีสิทธิ์ในฐานข้อมูล local MySQL

## วิธีการแก้ไขที่แนะนำ

เนื่องจากเห็นว่าการใช้ `.env.local` ทำงานได้ถูกต้อง จึงแนะนำให้ใช้วิธีนี้:

### วิธีที่ 1: ใช้คำสั่งพร้อม --env flag (แนะนำ)

```bash
php -d variables_order=EGPCS artisan db:show --env=local
```

วิธีนี้จะใช้การตั้งค่าจากไฟล์ `.env.local` ที่มีการตั้งค่าฐานข้อมูลสำหรับ local MySQL โดยเฉพาะ

### วิธีที่ 2: สร้าง script หรือ alias ที่ครบถ้วน

```bash
# เพิ่มในไฟล์ ~/.bashrc หรือ ~/.zshrc
alias artisan-local="php -d variables_order=EGPCS artisan --env=local"
```

แล้วใช้:

```bash
artisan-local db:show
```

### วิธีที่ 3: แก้ไข Docker database

หากต้องการใช้ Sail แต่พบปัญหา "Access denied":

1. เชื่อมต่อกับ MySQL ใน Docker:

    ```bash
    ./vendor/bin/sail mysql -u root
    ```

2. สร้างฐานข้อมูลและให้สิทธิ์:

    ```sql
    CREATE DATABASE ceosofts;
    GRANT ALL PRIVILEGES ON ceosofts.* TO 'sail'@'%';
    FLUSH PRIVILEGES;
    ```

3. ลองอีกครั้ง:
    ```bash
    ./vendor/bin/sail artisan db:show
    ```

## โครงสร้างฐานข้อมูลที่พบ

จากผลลัพธ์ของคำสั่ง `db:show` พบว่ามีฐานข้อมูลที่มีข้อมูลอยู่แล้ว 2 ฐานข้อมูล:

1. **ceosofts** - มีตาราง 36 ตาราง
2. **ceosofts_db** - มีตาราง 32 ตาราง

ทั้งสองฐานข้อมูลมีตารางที่คล้ายกันมาก (เช่น users, roles, permissions, products ฯลฯ) ซึ่งอาจเป็นเวอร์ชันที่แตกต่างกันหรือใช้สำหรับวัตถุประสงค์ที่แตกต่างกัน

### คำแนะนำ:

1. ตรวจสอบว่าควรใช้ฐานข้อมูลใดเป็นหลัก
2. พิจารณารวมทั้งสองฐานข้อมูลเข้าด้วยกัน หรือ
3. ระบุชัดเจนว่าแต่ละฐานข้อมูลมีวัตถุประสงค์แตกต่างกันอย่างไร

## สรุป

1. **สำหรับการพัฒนา Local (ไม่ใช้ Docker):**

    - ใช้คำสั่ง `php -d variables_order=EGPCS artisan --env=local [command]`
    - หรือ สร้าง alias `artisan-local` และใช้ `artisan-local [command]`

2. **สำหรับการพัฒนาใน Docker:**
    - สร้างฐานข้อมูล 'ceosofts' และให้สิทธิ์กับ user 'sail'
    - ใช้คำสั่ง `./vendor/bin/sail artisan [command]`
