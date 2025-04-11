#!/bin/bash

# สี
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# ทำงานในโฟลเดอร์โปรเจกต์
cd "$(dirname "$0")"

echo -e "${BLUE}==============================================================${NC}"
echo -e "${BLUE}     CEOsofts R1 - ระบบจัดระเบียบไฟล์โปรเจกต์อัตโนมัติ     ${NC}"
echo -e "${BLUE}==============================================================${NC}"

# 1. สร้างโฟลเดอร์ที่จำเป็น
echo -e "\n${YELLOW}1. กำลังสร้างโฟลเดอร์สำหรับจัดเก็บไฟล์...${NC}"

folders=(
  "archive" 
  "archive/encryption-fixes" 
  "archive/scripts" 
  "archive/backups" 
  "archive/docs"
  "database/migrations/core" 
  "database/migrations/main" 
  "database/migrations/updates"
)

for folder in "${folders[@]}"; do
  if [ ! -d "$folder" ]; then
    mkdir -p "$folder"
    echo -e "${GREEN}✓ สร้างโฟลเดอร์ $folder เรียบร้อย${NC}"
  else
    echo -e "${GREEN}✓ โฟลเดอร์ $folder มีอยู่แล้ว${NC}"
  fi
done

# 2. ย้ายไฟล์เกี่ยวกับ Encryption ไปที่ archive/encryption-fixes
echo -e "\n${YELLOW}2. กำลังย้ายไฟล์เกี่ยวกับ Encryption...${NC}"

encryption_files=(
  "check-encryption-modules.php"
  "check-encryption-system.php"
  "create-simple-encrypter-l12.php"
  "create-simple-encrypter.php"
  "debug-encrypter-creation.php"
  "debug-encrypter.php"
  "encrypt-decrypt-test.php"
  "encrypt-test.php"
  "extreme-cipher-fix.sh"
  "final-encryption-fix.php"
  "fix-app-key.php"
  "fix-cipher-case.php"
  "fix-encrypter-previouskeys.php"
  "fix-encryption-key.sh"
  "fix-encryption.php"
  "fix-laravel-encryption.php"
  "force-key-cipher.php"
  "full-encryption-db-fix.php"
  "manual-encrypter-test.php"
  "replace-encrypter.php"
  "replace-encryption-provider.php"
  "reset-encryption-settings.php"
  "reset-laravel-encryption.sh"
  "restore-original-encrypter.php"
  "simple-fix-encrypter.php"
  "test-custom-encryption.php"
  "test-encrypter-directly.php"
  "test-encryption.php"
  "test-openssl-directly.php"
  "test-openssl-encrypter.php"
  "test-simple-encrypter.php"
  "update-encrypter-contract.php"
  "update-encrypter-interface.php"
)

moved_count=0
for file in "${encryption_files[@]}"; do
  if [ -f "$file" ]; then
    mv "$file" "archive/encryption-fixes/"
    echo -e "${GREEN}✓ ย้าย $file ไปยัง archive/encryption-fixes/${NC}"
    ((moved_count++))
  fi
done

if [ "$moved_count" -eq 0 ]; then
  echo -e "${GREEN}ไม่พบไฟล์ที่ต้องย้าย${NC}"
fi

# 3. ย้าย Script ต่างๆ ไปยัง archive/scripts
echo -e "\n${YELLOW}3. กำลังย้าย Script ต่างๆ...${NC}"

script_files=(
  "check-basic-structure.php"
  "check-laravel-config.php"
  "check-laravel-version.php"
  "check-override-config.php"
  "check-vendor-encrypter.php"
  "clear-everything.sh"
  "command-list.sh"
  "composer-fixes.json"
  "composer-require-livewire.sh"
  "create-bin-folder.sh"
  "create-new-app-config.php"
  "debug-fix-all.php"
  "debug-runtime-values.php"
  "deep-cipher-fix.php"
  "deep-rebuild-app.sh"
  "dev-start.sh"
  "dev-tools.md"
  "direct-encrypter-fix.php"
  "direct-test-route.php"
  "downgrade-cipher-package.sh"
  "downgrade-laravel.sh"
  "fix-all-issues.sh"
  "fix-app-key.sh"
  "fix-bootstrap-app.php"
  "fix-bootstrap-laravel12.php"
  "fix-core-files-manually.php"
  "fix-helpers-file.php"
  "fix-helpers-syntax.php"
  "fix-key-directly.php"
  "fix-laravel-error.sh"
  "fix-laravel12-core.php"
  "fix-migrations.sh"
  "fix-npm-issues.sh"
  "fix-project-structure.sh"
  "fix-project.sh"
  "fix-routes-issue.sh"
  "fix-tailwind.sh"
  "fix-telescope-migration.php"
  "force-encryption-clean.sh"
  "force-naive-encrypter.php"
  "full-encryption-reset.php"
  "full-laravel-reinstall.sh"
  "minimal-bootstrap.php"
  "minimal-fix.sh"
  "new-env-setup.php"
  "persistent-config-fix.php"
  "recreate-config-local.php"
  "reinstall-core-packages.sh"
  "reinstall-laravel.php"
  "requirements.txt"
  "setup-database.sh"
  "update-project-base.sh"
  "verify-app-functionality.sh"
)

moved_count=0
for file in "${script_files[@]}"; do
  if [ -f "$file" ]; then
    mv "$file" "archive/scripts/"
    echo -e "${GREEN}✓ ย้าย $file ไปยัง archive/scripts/${NC}"
    ((moved_count++))
  fi
done

if [ "$moved_count" -eq 0 ]; then
  echo -e "${GREEN}ไม่พบไฟล์ที่ต้องย้าย${NC}"
fi

# 4. ย้ายเอกสารในโฟลเดอร์ Note/ ไปยัง archive/docs
echo -e "\n${YELLOW}4. กำลังย้ายเอกสารในโฟลเดอร์ Note/...${NC}"

if [ -d "Note" ]; then
  # เก็บรายชื่อไฟล์ก่อนย้าย
  files=$(find "Note" -type f -name "*.md" | sed 's/Note\///')
  
  # ย้ายไฟล์
  mv Note/* archive/docs/ 2>/dev/null
  echo -e "${GREEN}✓ ย้ายไฟล์จาก Note/ ไปยัง archive/docs/${NC}"
  
  # แสดงรายชื่อไฟล์ที่ย้าย
  for file in $files; do
    echo -e "  - ${file}"
  done
  
  # ลบโฟลเดอร์ Note/ ที่ว่างเปล่า
  rmdir Note 2>/dev/null
  echo -e "${GREEN}✓ ลบโฟลเดอร์ Note/ ที่ว่างเปล่า${NC}"
else
  echo -e "${GREEN}ไม่พบโฟลเดอร์ Note/${NC}"
fi

# 5. ย้าย backups ทั้งหมดไปที่ archive/backups
echo -e "\n${YELLOW}5. กำลังย้ายไฟล์ backups...${NC}"

if [ -d "backups" ]; then
  # เก็บรายชื่อไฟล์ก่อนย้าย
  backup_count=$(find "backups" -type f | wc -l)
  
  # ย้ายไฟล์
  mv backups/* archive/backups/ 2>/dev/null
  echo -e "${GREEN}✓ ย้ายไฟล์จาก backups/ ไปยัง archive/backups/ จำนวน $backup_count ไฟล์${NC}"
  
  # ลบโฟลเดอร์ backups/ ที่ว่างเปล่า
  rmdir backups 2>/dev/null
  echo -e "${GREEN}✓ ลบโฟลเดอร์ backups/ ที่ว่างเปล่า${NC}"
else
  echo -e "${GREEN}ไม่พบโฟลเดอร์ backups/${NC}"
fi

# 6. ลบไฟล์ debugbar
echo -e "\n${YELLOW}6. กำลังลบไฟล์ debugbar...${NC}"

if [ -d "storage/debugbar" ]; then
  debug_count=$(find "storage/debugbar" -name "*.json" | wc -l)
  rm -f storage/debugbar/*.json
  echo -e "${GREEN}✓ ลบไฟล์ debugbar จำนวน $debug_count ไฟล์${NC}"
else
  echo -e "${GREEN}ไม่พบโฟลเดอร์ debugbar${NC}"
fi

# 7. ย้ายไฟล์ Migration ไปตามหมวดหมู่
echo -e "\n${YELLOW}7. กำลังจัดระเบียบไฟล์ Migration...${NC}"

# ย้าย Migration พื้นฐาน
core_migrations=(
  "0001_01_01_000000_create_users_table.php"
  "0001_01_01_000001_create_cache_table.php"
  "0001_01_01_000002_create_jobs_table.php"
  "2025_04_11_011336_create_telescope_entries_table.php"
)

for migration in "${core_migrations[@]}"; do
  if [ -f "database/migrations/$migration" ]; then
    cp "database/migrations/$migration" "database/migrations/core/"
    echo -e "${GREEN}✓ คัดลอก $migration ไปยัง database/migrations/core/${NC}"
  fi
done

# ย้าย Migration ที่สร้างตาราง (main)
main_pattern="database/migrations/[0-9]*_create_.*table.php"
for file in $(find database/migrations -maxdepth 1 -name "[0-9]*_create_*table.php"); do
  filename=$(basename "$file")
  
  # ข้าม core migrations
  skip=false
  for core_migration in "${core_migrations[@]}"; do
    if [ "$filename" == "$core_migration" ]; then
      skip=true
      break
    fi
  done
  
  if [ "$skip" == "false" ]; then
    cp "$file" "database/migrations/main/"
    echo -e "${GREEN}✓ คัดลอก $filename ไปยัง database/migrations/main/${NC}"
  fi
done

# ย้าย Migration ที่อัปเดตตาราง (updates)
update_patterns=("database/migrations/[0-9]*_add_*.php" "database/migrations/[0-9]*_update_*.php" "database/migrations/[0-9]*_modify_*.php" "database/migrations/[0-9]*_fix_*.php")

for pattern in "${update_patterns[@]}"; do
  for file in $(find database/migrations -maxdepth 1 -path "$pattern"); do
    filename=$(basename "$file")
    cp "$file" "database/migrations/updates/"
    echo -e "${GREEN}✓ คัดลอก $filename ไปยัง database/migrations/updates/${NC}"
  done
done

echo -e "\n${BLUE}การย้ายและจัดระเบียบไฟล์เสร็จสมบูรณ์!${NC}"
echo -e "${YELLOW}หมายเหตุ: สำหรับไฟล์ migration เราได้ทำการคัดลอกแทนการย้าย เพื่อไม่ให้กระทบการทำงานของระบบ${NC}"
echo -e "${YELLOW}คุณสามารถดูไฟล์ที่จัดหมวดหมู่แล้วในโฟลเดอร์ database/migrations/core, database/migrations/main, และ database/migrations/updates${NC}"
echo -e "\n${BLUE}==============================================================${NC}"

# ตั้งสิทธิ์ให้ executable
chmod +x cleanup.sh

exit 0
