# การแก้ไขปัญหา CSS Warning ใน VS Code

## ปัญหา "Unknown at rules"

คำเตือน "Unknown at rules" ใน VS Code เกิดจาก IDE ไม่เข้าใจไวยากรณ์เฉพาะของ Tailwind CSS เช่น `@tailwind`, `@apply`, `@layer` เป็นต้น

## วิธีแก้ไข

### 1. ติดตั้ง Extension สำหรับ VS Code

ติดตั้ง extension ต่อไปนี้:

-   Tailwind CSS IntelliSense
-   PostCSS Language Support

### 2. ปิดการตรวจสอบ CSS มาตรฐาน

เพิ่มการตั้งค่าใน `.vscode/settings.json` ของโปรเจค:

```json
{
    "css.validate": false,
    "tailwindCSS.includeLanguages": {
        "blade": "html",
        "javascript": "javascript",
        "php": "php"
    },
    "tailwindCSS.experimental.configFile": "tailwind.config.js",
    "editor.quickSuggestions": {
        "strings": true
    }
}
```

### 3. แก้ไขไฟล์ CSS

แก้ไขไฟล์ `resources/css/app.css` ให้ใช้เฉพาะ directive ที่ถูกต้องของ Tailwind CSS:

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

/* Custom Components */
@layer components {
    .btn-primary {
        @apply px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors;
    }

    .btn-secondary {
        @apply px-4 py-2 bg-secondary-500 text-white rounded-lg hover:bg-secondary-600 transition-colors;
    }

    /* เพิ่ม components อื่นๆ */
}

/* Custom Utilities */
@layer utilities {
    .text-shadow {
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
    }
}
```

คำเตือนใน VS Code เป็นเพียงเรื่องของเครื่องมือตรวจสอบเท่านั้น ไม่มีผลต่อการทำงานจริงของ Tailwind CSS เมื่อคอมไพล์ด้วย PostCSS

### 4. ตรวจสอบให้แน่ใจว่า PostCSS กำลังทำงานถูกต้อง

ไฟล์ `postcss.config.js` ควรมีการตั้งค่าดังนี้:

```javascript
export default {
    plugins: {
        tailwindcss: {},
        autoprefixer: {},
    },
};
```

ไฟล์ `package.json` ควรมี dependencies สำหรับ tailwindcss และ postcss

### 5. รันการคอมไพล์ CSS

```bash
sail npm run dev
```

หรือสำหรับ production:

```bash
sail npm run build
```
