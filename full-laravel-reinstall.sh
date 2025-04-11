#!/bin/bash

# Màu sắc cho output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}     Laravel Core Package Reinstaller     ${NC}"
echo -e "${BLUE}================================================${NC}"

# 1. Backup composer.json và các file cấu hình quan trọng
echo -e "${YELLOW}\n1. Đang backup các file quan trọng...${NC}"

# Tạo thư mục backup
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_DIR="backups/reinstall_$TIMESTAMP"
mkdir -p $BACKUP_DIR

# Backup các file cấu hình
cp composer.json "$BACKUP_DIR/composer.json" 2>/dev/null || echo -e "${RED}Không tìm thấy composer.json${NC}"
cp .env "$BACKUP_DIR/.env" 2>/dev/null || echo -e "${RED}Không tìm thấy .env${NC}"
cp -r config "$BACKUP_DIR/config" 2>/dev/null || echo -e "${RED}Không tìm thấy thư mục config${NC}"

echo -e "${GREEN}Đã backup các file quan trọng vào thư mục $BACKUP_DIR${NC}"

# 2. Xóa vendor và composer.lock để cài đặt lại từ đầu
echo -e "${YELLOW}\n2. Xóa vendor và composer.lock...${NC}"
rm -rf vendor composer.lock
echo -e "${GREEN}Đã xóa thư mục vendor và file composer.lock${NC}"

# 3. Cài đặt lại các package Laravel core
echo -e "${YELLOW}\n3. Cài đặt lại các package core...${NC}"
composer install --no-interaction

# 4. Cập nhật lại tất cả các package
echo -e "${YELLOW}\n4. Cập nhật tất cả các package...${NC}"
composer update --no-interaction

# 5. Tạo APP_KEY mới
echo -e "${YELLOW}\n5. Tạo APP_KEY mới...${NC}"
php artisan key:generate --ansi

# 6. Xóa các file cache
echo -e "${YELLOW}\n6. Xóa cache...${NC}"

# Xóa các file cache compiled
rm -f bootstrap/cache/*.php
echo -e "${GREEN}Đã xóa cache trong bootstrap/cache${NC}"

# Xóa cache bằng artisan
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan clear-compiled
echo -e "${GREEN}Đã xóa cache bằng artisan${NC}"

# 7. Kiểm tra cài đặt
echo -e "${YELLOW}\n7. Kiểm tra cài đặt...${NC}"
php -v
echo ""
php artisan --version

# Hiển thị APP_KEY
echo -e "${YELLOW}\nAPP_KEY trong .env:${NC}"
grep APP_KEY .env

# Hiển thị cipher
echo -e "${YELLOW}\nCipher trong config/app.php:${NC}"
grep -n "cipher" config/app.php || echo -e "${RED}Không tìm thấy cipher trong config/app.php${NC}"

echo -e "\n${BLUE}================================================${NC}"
echo -e "${GREEN}Cài đặt lại hoàn tất!${NC}"
echo -e "${YELLOW}Vui lòng khởi động lại web server:${NC}"
echo -e "${GREEN}php artisan serve${NC}"
echo -e "${BLUE}================================================${NC}"

chmod +x full-laravel-reinstall.sh
