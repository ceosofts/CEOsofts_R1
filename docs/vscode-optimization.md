# การแก้ไขปัญหา VS Code ทำงานหนักเกินไป

## ปัญหาที่พบ
โปรเซส VS Code ต่อไปนี้กำลังใช้ CPU สูงถึง 70-80%:
- Code Helper (Renderer)
- Code Helper (GPU)

## สาเหตุทั่วไป
1. **ส่วนขยาย (Extensions) ที่มีปัญหา**
   - ส่วนขยายบางตัวอาจวิเคราะห์โค้ดตลอดเวลาหรือใช้ทรัพยากรสูง
   - ส่วนขยายที่มักมีปัญหา: TypeScript, ESLint, Prettier, GitLens เมื่อเปิดโปรเจคขนาดใหญ่

2. **การทำ Indexing โปรเจคขนาดใหญ่**
   - VS Code จะทำการ index ไฟล์ทั้งหมดเพื่อให้ค้นหาได้เร็วขึ้น 
   - จำนวนไฟล์ในโปรเจคมีมากเกิน 50,000+ ไฟล์ (รวม node_modules)

3. **การเปิดไฟล์ขนาดใหญ่**
   - ไฟล์ log ขนาด 38.47 MB (laravel.log) ในโปรเจคของคุณอาจทำให้ VS Code ทำงานหนัก

4. **โปรเจคซับซ้อน**
   - โปรเจคของคุณมีโครงสร้างซับซ้อนและมีไฟล์จำนวนมาก

## วิธีแก้ไขปัญหา

### 1. จัดการกับส่วนขยาย

ปิดส่วนขยายที่ไม่จำเป็น:
```
1. กด Ctrl+Shift+X (หรือ Cmd+Shift+X บน Mac) เพื่อเปิดหน้าส่วนขยาย
2. คลิกขวาที่ส่วนขยายและเลือก "Disable" หรือ "Disable (Workspace)"
3. ลองปิดส่วนขยายทีละตัวเพื่อดูว่าตัวไหนเป็นสาเหตุ
```

ส่วนขยายที่ควรตรวจสอบเป็นพิเศษ:
- โปรแกรมวิเคราะห์โค้ด (Linters, Formatters)
- ส่วนขยายที่ทำงานกับ Git หรือ intellisense ที่ซับซ้อน
- ส่วนขยายที่แสดงผลแบบกราฟิก

### 2. ปรับการตั้งค่า VS Code

เพิ่มไฟล์ `.vscode/settings.json` ในโปรเจค:

```json
{
  "files.watcherExclude": {
    "**/.git/objects/**": true,
    "**/.git/subtree-cache/**": true,
    "**/node_modules/**": true,
    "**/vendor/**": true,
    "**/storage/**": true,
    "**/public/build/**": true
  },
  "search.exclude": {
    "**/node_modules": true,
    "**/vendor": true,
    "**/storage": true,
    "**/public/build": true
  },
  "files.exclude": {
    "**/.git": true,
    "**/node_modules": true,
    "**/vendor": true
  },
  "editor.formatOnSave": false,
  "editor.renderWhitespace": "none",
  "editor.minimap.enabled": false,
  "editor.wordWrap": "off",
  "typescript.disableAutomaticTypeAcquisition": true,
  "php.validate.enable": false,
  "javascript.validate.enable": false
}
```

### 3. เพิ่มทรัพยากรให้ VS Code

แก้ไขไฟล์ `vscode-settings.json` เพื่อเพิ่มหน่วยความจำ:

```json
{
  "window.zoomLevel": 0,
  "workbench.colorTheme": "Default Dark+",
  "window.title": "${dirty}${activeEditorShort}${separator}${rootName}",
  "workbench.editor.enablePreview": false,
  "editor.unicodeHighlight.ambiguousCharacters": false
}
```

### 4. ลดขนาด Log files

```bash
# ล้างไฟล์ log ที่มีขนาดใหญ่
> storage/logs/laravel.log
# หรือ
echo "" > storage/logs/laravel.log
```

### 5. ใช้โปรแกรม Editor อื่นสำหรับไฟล์ขนาดใหญ่

เปิดไฟล์ log หรือไฟล์ขนาดใหญ่ด้วยโปรแกรมที่ออกแบบมาเพื่องานนี้:
- macOS: BBEdit, Sublime Text
- Windows: Notepad++
- ทุกระบบ: Vim, nano

### 6. รีสตาร์ท VS Code อย่างสมบูรณ์

```bash
# macOS
killall "Code Helper"
killall "Code Helper (Renderer)"
killall "Code Helper (GPU)"
killall "Visual Studio Code"

# Windows - รีสตาร์ทคอมพิวเตอร์หรือใช้ Task Manager
```

## การป้องกันปัญหาในอนาคต

1. แยกโปรเจคเป็นส่วนย่อยๆ (Workspaces)
2. ตั้งเวลาล้าง log files อัตโนมัติ
3. ติดตามปริมาณไฟล์และขนาดของโปรเจค
4. อัปเดต VS Code เป็นเวอร์ชันล่าสุดเสมอ
