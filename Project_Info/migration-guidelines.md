# แนวทางการสร้างและแก้ไขไฟล์ Migration

## การจัดการคอลัมน์ใน SQLite

SQLite ไม่รองรับคำสั่ง `MODIFY` หรือ `CHANGE` สำหรับการแก้ไขคอลัมน์โดยตรง ดังนั้น:

1. **ใช้วิธีการสร้างตารางใหม่และย้ายข้อมูล**

    - สร้างตารางใหม่ที่มีโครงสร้างที่ต้องการ
    - ย้ายข้อมูลจากตารางเดิมไปยังตารางใหม่
    - ลบตารางเดิมและเปลี่ยนชื่อตารางใหม่

2. **ตัวอย่างการแก้ไขคอลัมน์ใน SQLite**

```php
Schema::create('table_temp', function (Blueprint $table) {
    $table->id();
    $table->string('column_name')->default('default_value')->nullable(false);
    // คอลัมน์อื่นๆ...
});

// ย้ายข้อมูลจากตารางเดิมไปยังตารางใหม่
DB::statement('
    INSERT INTO table_temp (id, column_name)
    SELECT id, COALESCE(column_name, "default_value")
    FROM table_name
');

// ลบตารางเดิมและเปลี่ยนชื่อตารางใหม่
Schema::drop('table_name');
Schema::rename('table_temp', 'table_name');
```

3. **ตรวจสอบชนิดของฐานข้อมูล**

ใช้ `DB::getDriverName()` เพื่อตรวจสอบว่าฐานข้อมูลที่ใช้งานอยู่เป็น SQLite หรือไม่:

```php
$driver = DB::getDriverName();

if ($driver === 'sqlite') {
    // Logic สำหรับ SQLite
} else {
    // Logic สำหรับ MySQL หรือฐานข้อมูลอื่นๆ
}
```

## สรุป

การจัดการคอลัมน์ใน SQLite ต้องใช้วิธีการสร้างตารางใหม่และย้ายข้อมูลแทนการใช้คำสั่ง `MODIFY` หรือ `CHANGE` ที่ไม่รองรับ
