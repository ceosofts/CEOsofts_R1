# คำสั่งตรวจสอบสภาพแวดล้อมการพัฒนา

# Development Environment Check Commands

## ตรวจสอบเวอร์ชันของเครื่องมือต่างๆ

## Check software versions

### PHP

```bash
# ตรวจสอบเวอร์ชัน PHP
# Check PHP version
php -v

# ตรวจสอบการตั้งค่า PHP
# Check PHP configuration
php -i

# ตรวจสอบ PHP extensions ที่ติดตั้ง
# Check installed PHP extensions
php -m
```

### Composer

```bash
# ตรวจสอบเวอร์ชัน Composer
# Check Composer version
composer -V

# ตรวจสอบแพ็กเกจที่ติดตั้งแล้วใน global
# Check globally installed packages
composer global show

# ตรวจสอบการตั้งค่า Composer
# Check Composer configuration
composer config -l
```

### Node.js & NPM

```bash
# ตรวจสอบเวอร์ชัน Node.js
# Check Node.js version
node -v

# ตรวจสอบเวอร์ชัน NPM
# Check NPM version
npm -v

# ตรวจสอบ global NPM packages
# Check global NPM packages
npm list -g --depth=0
```

### Git

```bash
# ตรวจสอบเวอร์ชัน Git
# Check Git version
git --version

# ตรวจสอบการตั้งค่า Git
# Check Git configuration
git config --list

# ตรวจสอบสถานะ repository
# Check repository status
git status
```

### Docker

```bash
# ตรวจสอบเวอร์ชัน Docker
# Check Docker version
docker --version

# ตรวจสอบเวอร์ชัน Docker Compose
# Check Docker Compose version
docker-compose --version

# ตรวจสอบ containers ที่กำลังทำงาน
# Check running containers
docker ps

# ตรวจสอบ images ทั้งหมด
# Check all images
docker images

# ตรวจสอบข้อมูลระบบ Docker
# Check Docker system info
docker system info
```

### Database

```bash
# MySQL: ตรวจสอบเวอร์ชัน
# MySQL: Check version
mysql --version

# PostgreSQL: ตรวจสอบเวอร์ชัน
# PostgreSQL: Check version
psql --version

# MySQL: ทดสอบการเชื่อมต่อ (ต้องใส่รหัสผ่าน)
# MySQL: Test connection (requires password)
mysql -u root -p -e "SHOW DATABASES;"

# PostgreSQL: ทดสอบการเชื่อมต่อ
# PostgreSQL: Test connection
psql -U postgres -c "\l"
```

## ตรวจสอบโปรเจค Laravel

## Check Laravel Project

```bash
# ตรวจสอบเวอร์ชัน Laravel
# Check Laravel version
php artisan --version

# ตรวจสอบสถานะระบบ
# Check system status
php artisan about

# ตรวจสอบเส้นทาง (routes)
# Check routes
php artisan route:list

# ตรวจสอบสถานะการเชื่อมต่อฐานข้อมูล
# Check database connection
php artisan db:show

# ตรวจสอบสถานะ migrations
# Check migrations status
php artisan migrate:status

# ตรวจสอบระบบแคช
# Check cache status
php artisan cache:status
```

## Laravel Sail (Docker)

```bash
# เริ่มต้นใช้งาน Sail
# Start Sail
./vendor/bin/sail up -d

# หยุดการทำงาน Sail
# Stop Sail
./vendor/bin/sail down

# รันคำสั่ง Artisan ผ่าน Sail
# Run Artisan commands via Sail
./vendor/bin/sail artisan about

# รันคำสั่ง Composer ผ่าน Sail
# Run Composer commands via Sail
./vendor/bin/sail composer install

# รันคำสั่ง NPM ผ่าน Sail
# Run NPM commands via Sail
./vendor/bin/sail npm install
```

## ตรวจสอบเครือข่าย

## Network Checks

```bash
# ตรวจสอบ ports ที่ใช้งานอยู่
# Check used ports
# macOS & Linux:
netstat -tulpn
# Windows:
netstat -ano

# ตรวจสอบการเชื่อมต่อเซิร์ฟเวอร์
# Check server connection
ping github.com

# ตรวจสอบ DNS
# Check DNS
nslookup github.com
```

## ตรวจสอบระบบปฏิบัติการ

## Operating System Checks

```bash
# macOS & Linux: ตรวจสอบการใช้งาน disk
# macOS & Linux: Check disk usage
df -h

# macOS & Linux: ตรวจสอบการใช้งาน memory
# macOS & Linux: Check memory usage
free -m

# macOS & Linux: ตรวจสอบเวอร์ชันระบบปฏิบัติการ
# macOS & Linux: Check OS version
uname -a
# macOS specific:
sw_vers
# Linux specific:
cat /etc/os-release

# Windows: ตรวจสอบเวอร์ชันระบบปฏิบัติการ
# Windows: Check OS version
ver
```

## ทดสอบประสิทธิภาพ

## Performance Tests

```bash
# ทดสอบความเร็ว disk
# Test disk speed
# macOS & Linux:
dd if=/dev/zero of=testfile bs=1G count=1 oflag=dsync

# ทดสอบ network bandwidth
# Test network bandwidth
wget -O /dev/null http://speedtest.wdc01.softlayer.com/downloads/test10.zip
```

## การแก้ปัญหาเบื้องต้น

## Basic Troubleshooting

### PHP

-   ตรวจสอบว่า PHP extensions ที่จำเป็นสำหรับ Laravel มีติดตั้งหรือไม่
-   Check if the required PHP extensions for Laravel are installed
    -   BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML

### Docker

-   หากพบปัญหาเรื่อง permissions: `sudo chmod 666 /var/run/docker.sock`
-   If there are permission issues: `sudo chmod 666 /var/run/docker.sock`

### Laravel

-   ลบแคช: `php artisan cache:clear && php artisan config:clear && php artisan view:clear && php artisan route:clear`
-   Clear caches: `php artisan cache:clear && php artisan config:clear && php artisan view:clear && php artisan route:clear`

### Database

-   ตรวจสอบการเชื่อมต่อในไฟล์ `.env`
-   Check connection settings in the `.env` file

```

```
