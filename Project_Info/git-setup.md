# การตั้งค่า Git Repository

## คำอธิบายข้อผิดพลาด

เมื่อเราเรียกใช้คำสั่ง `git status` และได้รับข้อความ:

```
fatal: not a git repository (or any of the parent directories): .git
```

หมายความว่า: ไดเรกทอรีปัจจุบัน (`/Users/iwasbornforthis/MyProject/CEOsofts_R1`) **ยังไม่ได้ถูกตั้งค่าให้เป็น Git repository**

ข้อความนี้แสดงว่า Git ไม่สามารถหาโฟลเดอร์ `.git` ซึ่งเป็นที่เก็บข้อมูลการควบคุมเวอร์ชันของ Git repository ได้

## การตั้งค่า Git Repository

เพื่อเริ่มต้นใช้งาน Git ในโปรเจคนี้ ให้ทำตามขั้นตอนต่อไปนี้:

### 1. สร้าง Git Repository ใหม่

```bash
# เข้าไปยังโฟลเดอร์โปรเจค
cd /Users/iwasbornforthis/MyProject/CEOsofts_R1

# สร้าง Git Repository ใหม่
git init
```

### 2. ตั้งค่า Git User (ถ้ายังไม่ได้ตั้งค่า)

```bash
git config --local user.name "ceosofts"
git config --local user.email "ceosofts@gmail.com"
```

### 3. สร้าง .gitignore file

```bash
# สร้างไฟล์ .gitignore สำหรับ Laravel project
cat > .gitignore << 'EOL'
/node_modules
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.phpunit.result.cache
docker-compose.override.yml
Homestead.json
Homestead.yaml
npm-debug.log
yarn-error.log
/.idea
/.vscode
EOL
```

### 4. เพิ่มไฟล์เข้าสู่ Staging Area

```bash
# เพิ่มไฟล์ทั้งหมดที่ต้องการ track (ยกเว้นไฟล์ที่อยู่ใน .gitignore)
git add .
```

### 5. Commit ครั้งแรก

```bash
# สร้าง commit แรก
git commit -m "Initial commit"
```

### 6. เชื่อมต่อกับ Remote Repository (ถ้ามี)

```bash
# เชื่อมต่อกับ remote repository บน GitHub/GitLab/Bitbucket
git remote add origin https://github.com/ceosofts/CEOsofts_R1.git

# Push โค้ดไปยัง remote repository
git push -u origin main
```

## การตั้งค่า Branch Strategy

ตามที่ได้วางแผนไว้ใน Next_Steps.md เราจะใช้ branching strategy ดังนี้:

```bash
# สร้าง develop branch
git checkout -b develop

# สร้าง feature branch (ตัวอย่าง)
git checkout -b feature/auth develop
```

## Git Workflow ที่แนะนำ

1. ทำงานใน feature branch
2. Commit และ push การเปลี่ยนแปลงไปยัง feature branch
3. สร้าง Pull Request เพื่อ merge เข้า develop branch
4. เมื่อพัฒนาเสร็จและทดสอบแล้ว จึง merge develop เข้า main

## Git Hooks

เราสามารถตั้งค่า Git hooks เพื่อทำ code linting และ testing อัตโนมัติก่อน commit ได้:

```bash
# ตัวอย่าง pre-commit hook สำหรับ PHP CS Fixer
mkdir -p .git/hooks
cat > .git/hooks/pre-commit << 'EOL'
#!/bin/sh
./vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php --allow-risky=yes
EOF
chmod +x .git/hooks/pre-commit
```

## ติดตั้ง Git LFS (ถ้าต้องการ)

หากต้องการเก็บไฟล์ขนาดใหญ่ (เช่น รูปภาพ, วิดีโอ) ใน repository:

```bash
# ติดตั้ง Git LFS
brew install git-lfs

# ตั้งค่า Git LFS
git lfs install

# ตั้งค่าให้ track ไฟล์ประเภทต่างๆ
git lfs track "*.psd"
git lfs track "*.ai"
git lfs track "*.mp4"

# เพิ่มไฟล์ .gitattributes
git add .gitattributes
git commit -m "Set up Git LFS"
```
