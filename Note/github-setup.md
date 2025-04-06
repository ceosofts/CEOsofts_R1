# การตั้งค่า GitHub Repository

## แก้ไขปัญหา "Repository not found"

เมื่อรันคำสั่ง `git push -u origin main` แล้วพบข้อความผิดพลาด:

```
remote: Repository not found.
fatal: repository 'https://github.com/ceosofts/CEOsofts_R1.git/' not found
```

ข้อความนี้หมายความว่า repository ที่ระบุไม่มีอยู่บน GitHub หรือคุณไม่มีสิทธิ์เข้าถึง

## วิธีแก้ไข

### 1. สร้าง Repository บน GitHub ก่อน

ก่อนที่จะสามารถ push โค้ดไปยัง GitHub ได้ คุณต้องสร้าง repository บน GitHub ก่อน:

1. ไปที่ https://github.com/
2. ล็อกอินด้วยบัญชี ceosofts
3. คลิกที่ปุ่ม "+" ที่มุมขวาบน และเลือก "New repository"
4. กรอกข้อมูล Repository:
    - Repository name: CEOsofts_R1
    - Description: (ตามต้องการ)
    - เลือก Public หรือ Private ตามต้องการ
    - **ไม่ต้องเลือก** "Initialize this repository with a README"
    - **ไม่ต้องเลือก** Add .gitignore หรือ license (เนื่องจากเรามีอยู่แล้ว)
5. คลิก "Create repository"

### 2. ตรวจสอบ URL ของ remote repository

ตรวจสอบว่า URL ที่ใช้ถูกต้อง:

```bash
git remote -v
```

ถ้า URL ไม่ถูกต้อง สามารถเปลี่ยนได้:

```bash
git remote set-url origin https://github.com/ceosofts/CEOsofts_R1.git
```

### 3. ตรวจสอบสิทธิ์การเข้าถึง GitHub

#### กรณีใช้ HTTPS

ถ้าใช้ HTTPS URL (เริ่มต้นด้วย `https://`):

1. ตรวจสอบว่าได้ล็อกอินด้วย GitHub credential helper:

    ```bash
    git config credential.helper
    ```

2. ถ้าต้องการล้าง credential เพื่อลองใหม่:

    ```bash
    git credential-osxkeychain erase
    host=github.com
    protocol=https
    [Press Ctrl+D]
    ```

3. ลองใช้ GitHub token แทน password:
    - ไปที่ GitHub -> Settings -> Developer settings -> Personal access tokens -> Generate new token
    - เลือกสิทธิ์ `repo` และ `workflow`
    - ใช้ token นี้เป็น password เมื่อ Git ขอ

#### กรณีใช้ SSH

ถ้าต้องการใช้ SSH (แนะนำ):

1. เปลี่ยน remote URL เป็น SSH:

    ```bash
    git remote set-url origin git@github.com:ceosofts/CEOsofts_R1.git
    ```

2. ตรวจสอบว่ามี SSH key และเพิ่มเข้า GitHub:

    ```bash
    # ตรวจสอบว่ามี SSH key หรือไม่
    ls -la ~/.ssh

    # ถ้าไม่มี ให้สร้างใหม่
    ssh-keygen -t ed25519 -C "ceosofts@gmail.com"

    # ดู public key
    cat ~/.ssh/id_ed25519.pub
    ```

3. นำ public key ไปเพิ่มใน GitHub:
    - ไปที่ GitHub -> Settings -> SSH and GPG keys -> New SSH key
    - วางเนื้อหาของ public key และบันทึก

### 4. ทางเลือกอื่น

#### ใช้ GitHub CLI

ติดตั้งและใช้ GitHub CLI เพื่อจัดการ repository:

```bash
# ติดตั้ง GitHub CLI (macOS)
brew install gh

# ล็อกอิน
gh auth login

# สร้าง repository
gh repo create ceosofts/CEOsofts_R1 --private --source=. --remote=origin
```

#### ใช้ repository ชื่ออื่น

ถ้า "CEOsofts_R1" ถูกใช้ไปแล้วหรือมีปัญหา:

```bash
# สร้าง repository ใน GitHub ด้วยชื่ออื่น (เช่น "CEOsofts-Project")

# เปลี่ยน remote URL
git remote set-url origin https://github.com/ceosofts/CEOsofts-Project.git

# หรือ SSH URL
git remote set-url origin git@github.com:ceosofts/CEOsofts-Project.git
```

## การทดสอบการเชื่อมต่อ

เมื่อตั้งค่าเสร็จแล้ว ลองทดสอบการเชื่อมต่อกับ GitHub:

```bash
# ทดสอบการเชื่อมต่อ SSH
ssh -T git@github.com

# หรือ ถ้าใช้ GitHub CLI
gh auth status
```

## ทำการ push อีกครั้ง

หลังจากตั้งค่าเสร็จแล้ว ลองทำการ push อีกครั้ง:

```bash
git push -u origin main
```

## แก้ไขปัญหา "Updates were rejected"

เมื่อรันคำสั่ง `git push -u origin main` แล้วพบข้อความผิดพลาด:

```
To https://github.com/ceosofts/CEOsofts_R1.git
 ! [rejected]        main -> main (fetch first)
error: failed to push some refs to 'https://github.com/ceosofts/CEOsofts_R1.git'
hint: Updates were rejected because the remote contains work that you do
hint: not have locally.
```

ข้อความนี้หมายความว่า: repository บน GitHub มีไฟล์หรือการเปลี่ยนแปลงที่ local repository ของคุณไม่มี ซึ่งอาจเกิดจาก:

-   GitHub repository ถูกสร้างพร้อมกับ README, .gitignore หรือ license
-   มีคนอื่นได้ push โค้ดไปยัง repository นี้แล้ว
-   repository นี้มีไฟล์เริ่มต้นอยู่

### วิธีแก้ไข

มีหลายวิธีในการแก้ปัญหานี้:

#### 1. ดึงข้อมูลจาก remote แล้วรวมกับ local (แนะนำถ้าต้องการเก็บไฟล์จาก remote)

```bash
# ดึงข้อมูลและรวม
git pull --rebase origin main

# แก้ conflict (ถ้ามี)
# หลังจากแก้ conflict แล้ว
git add .
git rebase --continue

# push อีกครั้ง
git push -u origin main
```

#### 2. บังคับ push (ใช้ในกรณีที่ไม่ต้องการเก็บข้อมูลจาก remote)

⚠️ **คำเตือน**: วิธีนี้จะทำให้ข้อมูลบน remote repository หายไป ใช้เมื่อแน่ใจว่าไม่ต้องการเก็บข้อมูลใดๆ บน remote

```bash
git push -u origin main --force
```

#### 3. สร้าง branch ใหม่เพื่อเก็บโค้ด

```bash
# สร้าง branch ใหม่จาก main ปัจจุบัน
git checkout -b local-main

# push branch นี้ขึ้น remote
git push -u origin local-main
```

หลังจากนั้น คุณสามารถไปที่ GitHub แล้วสร้าง Pull Request เพื่อรวม branch นี้เข้ากับ main

#### 4. ลบ remote repository และสร้างใหม่

ถ้ายังอยู่ในช่วงเริ่มต้นและไม่มีข้อมูลสำคัญบน GitHub repository:

1. ไปที่ Settings ของ repository บน GitHub
2. เลือน scroll ลงไปด้านล่างสุด ถึงส่วน "Danger Zone"
3. คลิกที่ "Delete this repository"
4. สร้าง repository ใหม่โดยไม่เลือก "Initialize with README"
5. ทำการ push อีกครั้ง
