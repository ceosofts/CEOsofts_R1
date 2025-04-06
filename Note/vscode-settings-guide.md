# วิธีแก้ปัญหา "Unknown at rule" ใน VS Code สำหรับ Tailwind CSS

VS Code มักจะแสดงข้อผิดพลาดและคำเตือนเกี่ยวกับ directive ของ Tailwind CSS เนื่องจากมันไม่ใช่ CSS มาตรฐาน ต่อไปนี้คือวิธีแก้ไขปัญหานี้:

## 1. ติดตั้ง Extensions ที่จำเป็น

เปิด VS Code และติดตั้ง extensions ต่อไปนี้:

1. **Tailwind CSS IntelliSense** - ให้คำแนะนำและ autocomplete สำหรับ Tailwind CSS
2. **PostCSS Language Support** - ช่วยให้เข้าใจไวยากรณ์ PostCSS (ซึ่งรวมถึง Tailwind directives)

## 2. สร้างโฟลเดอร์ .vscode และไฟล์ settings.json

```bash
# สร้างโฟลเดอร์ .vscode ในโปรเจค
mkdir -p .vscode

# สร้างไฟล์ settings.json
touch .vscode/settings.json
```

## 3. เพิ่มการตั้งค่า VS Code เฉพาะโปรเจค

เปิดไฟล์ `.vscode/settings.json` และเพิ่มการตั้งค่าต่อไปนี้:

```json
{
    "css.validate": false,
    "less.validate": false,
    "scss.validate": false,
    "tailwindCSS.emmetCompletions": true,
    "tailwindCSS.includeLanguages": {
        "blade": "html",
        "javascript": "javascript",
        "php": "php"
    },
    "editor.quickSuggestions": {
        "strings": true
    },
    "files.associations": {
        "*.blade.php": "blade"
    }
}
```

การตั้งค่านี้จะทำ:

-   ปิดการตรวจสอบ CSS มาตรฐานสำหรับไฟล์ CSS, LESS และ SCSS
-   เปิดใช้งาน Tailwind CSS completions ในไฟล์ Blade, JavaScript และ PHP
-   เปิดใช้งาน quick suggestions ในสตริงสำหรับคลาส Tailwind
-   ตั้งค่าให้ไฟล์ .blade.php ถูกตีความเป็นไฟล์ Blade template

## 4. รีโหลด VS Code

หลังจากเพิ่มการตั้งค่า ให้รีโหลด VS Code หรือเปิดใหม่เพื่อให้การตั้งค่ามีผล

## 5. ปัญหาอื่นที่เกี่ยวข้อง

### กรณีที่ยังเห็นคำเตือน

หากยังคงเห็นคำเตือนหลังจากการตั้งค่าด้านบน ลองทำสิ่งต่อไปนี้:

1. **ตรวจสอบว่าไฟล์ CSS ของคุณมีนามสกุล .css** - ไฟล์ที่มีนามสกุลอื่น อาจไม่ได้รับการตรวจสอบด้วยกฎที่ตั้งไว้
2. **ติดตั้ง extension เพิ่มเติม** - เช่น "stylelint" และตั้งค่าให้ใช้งานร่วมกับ Tailwind CSS
3. **สร้างไฟล์ .stylelintrc.json** - หากใช้ stylelint เพื่อตั้งค่าให้ยอมรับ Tailwind directives

### ถ้าใช้โปรเจค Laravel Jetstream หรือ Breeze

Laravel Jetstream และ Breeze มักจะมีการตั้งค่า Tailwind CSS มาให้แล้ว อย่าลืมรัน:

```bash
npm install
npm run dev
```

เพื่อให้แน่ใจว่า Tailwind CSS ถูกตั้งค่าอย่างถูกต้องและการแปลง CSS ใช้งานได้
