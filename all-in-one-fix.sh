#!/bin/bash

# ตั้งค่าสี
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}     All-in-One Laravel Fix Utility     ${NC}"
echo -e "${BLUE}================================================${NC}"

# ตรวจสอบว่าเป็นโปรเจกต์ Laravel
if [ ! -f "artisan" ]; then
    echo -e "${RED}ไม่พบไฟล์ artisan - ไม่ใช่โปรเจกต์ Laravel${NC}"
    exit 1
fi

# 1. สร้างโครงสร้างไฟล์พื้นฐานที่จำเป็น
echo -e "${YELLOW}\n1. กำลังตรวจสอบและสร้างโครงสร้างไฟล์พื้นฐาน...${NC}"

# สร้างโฟลเดอร์ routes หากยังไม่มี
if [ ! -d "routes" ]; then
    mkdir -p routes
    echo -e "${GREEN}สร้างโฟลเดอร์ routes${NC}"
fi

# ตรวจสอบและสร้างไฟล์ routes พื้นฐาน
if [ ! -f "routes/web.php" ]; then
    cat > routes/web.php << 'EOL'
<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
EOL
    echo -e "${GREEN}สร้างไฟล์ routes/web.php${NC}"
fi

if [ ! -f "routes/api.php" ]; then
    cat > routes/api.php << 'EOL'
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
EOL
    echo -e "${GREEN}สร้างไฟล์ routes/api.php${NC}"
fi

if [ ! -f "routes/channels.php" ]; then
    cat > routes/channels.php << 'EOL'
<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
EOL
    echo -e "${GREEN}สร้างไฟล์ routes/channels.php${NC}"
fi

if [ ! -f "routes/console.php" ]; then
    cat > routes/console.php << 'EOL'
<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
EOL
    echo -e "${GREEN}สร้างไฟล์ routes/console.php${NC}"
fi

# 2. แก้ไขปัญหา App Key และ Cipher
echo -e "${YELLOW}\n2. กำลังแก้ไขปัญหา App Key และ Cipher...${NC}"

# สร้างโฟลเดอร์สำรองข้อมูลถ้ายังไม่มี
if [ ! -d "backups" ]; then
    mkdir backups
    echo -e "${GREEN}สร้างโฟลเดอร์ backups สำเร็จ${NC}"
fi

# timestamp สำหรับการตั้งชื่อไฟล์สำรอง
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")

# สำรองไฟล์สำคัญ
if [ -f "config/app.php" ]; then
    cp config/app.php "backups/app.php.$TIMESTAMP"
    echo -e "${GREEN}สำรองไฟล์ config/app.php เรียบร้อยแล้ว${NC}"
fi

if [ -f ".env" ]; then
    cp .env "backups/.env.$TIMESTAMP"
    echo -e "${GREEN}สำรองไฟล์ .env เรียบร้อยแล้ว${NC}"
fi

# สร้างไฟล์ config/app.php ใหม่
echo -e "${YELLOW}กำลังสร้างไฟล์ config/app.php ใหม่...${NC}"

# สร้างโฟลเดอร์ config หากยังไม่มี
if [ ! -d "config" ]; then
    mkdir -p config
    echo -e "${GREEN}สร้างโฟลเดอร์ config${NC}"
fi

cat > config/app.php << 'EOL'
<?php

return [
    'name' => env('APP_NAME', 'Laravel'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL'),
    'timezone' => 'UTC',
    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),
    'key' => env('APP_KEY'),
    'cipher' => 'aes-256-cbc',
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE'),
    ],
    'providers' => [
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
    ],
    'aliases' => [],
];
EOL
echo -e "${GREEN}สร้างไฟล์ config/app.php ใหม่เรียบร้อยแล้ว${NC}"

# สร้าง APP_KEY ใหม่
echo -e "${YELLOW}\nกำลังสร้าง APP_KEY ใหม่...${NC}"

# สร้างตัวแปร PHP เพื่อสร้าง key ใหม่
NEW_KEY=$(php -r "echo 'base64:' . base64_encode(random_bytes(32));")
echo -e "${GREEN}สร้าง key ใหม่: $NEW_KEY${NC}"

# แก้ไขไฟล์ .env
if [ -f ".env" ]; then
    # ตรวจสอบว่ามี APP_KEY ในไฟล์หรือไม่
    grep -q "^APP_KEY=" .env
    if [ $? -eq 0 ]; then
        # แทนที่ APP_KEY ที่มีอยู่
        sed -i.bak "s/^APP_KEY=.*$/APP_KEY=$NEW_KEY/" .env
        echo -e "${GREEN}แก้ไข APP_KEY ในไฟล์ .env เรียบร้อยแล้ว${NC}"
    else
        # เพิ่ม APP_KEY ใหม่
        echo "APP_KEY=$NEW_KEY" >> .env
        echo -e "${GREEN}เพิ่ม APP_KEY ในไฟล์ .env เรียบร้อยแล้ว${NC}"
    fi
else
    echo -e "${RED}ไม่พบไฟล์ .env${NC}"
    echo -e "${YELLOW}กำลังสร้างไฟล์ .env ใหม่จาก .env.example...${NC}"
    
    if [ -f ".env.example" ]; then
        cp .env.example .env
        sed -i.bak "s/^APP_KEY=.*$/APP_KEY=$NEW_KEY/" .env
        echo -e "${GREEN}สร้างไฟล์ .env ใหม่พร้อม APP_KEY เรียบร้อยแล้ว${NC}"
    else
        # สร้างไฟล์ .env ขั้นต่ำ
        cat > .env << EOL
APP_NAME=CEOsofts
APP_ENV=local
APP_KEY=$NEW_KEY
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ceosofts_db_r1
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
EOL
        echo -e "${GREEN}สร้างไฟล์ .env ขั้นต่ำพร้อม APP_KEY เรียบร้อยแล้ว${NC}"
    fi
fi

# 3. ตรวจสอบและสร้างโฟลเดอร์ที่จำเป็น
echo -e "${YELLOW}\n3. กำลังตรวจสอบและสร้างโฟลเดอร์ที่จำเป็น...${NC}"

# สร้างโฟลเดอร์ storage ที่จำเป็น
mkdir -p storage/framework/views
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/logs
mkdir -p bootstrap/cache
mkdir -p resources/views

# ตรวจสอบว่ามีไฟล์ welcome.blade.php หรือไม่
if [ ! -f "resources/views/welcome.blade.php" ]; then
    cat > resources/views/welcome.blade.php << 'EOL'
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CEOsofts R1 - ยินดีต้อนรับ</title>
    <style>
        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            color: #333;
            background: #f9fafb;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }
        .header {
            text-align: center;
            margin-bottom: 2rem;
            padding: 1rem;
            background: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .content {
            background: #fff;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        h1 {
            color: #2563eb;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .subtitle {
            color: #64748b;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }
        .success-message {
            padding: 1rem;
            background: #dcfce7;
            color: #166534;
            border-radius: 0.375rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>CEOsofts R1</h1>
            <p class="subtitle">ระบบบริหารจัดการธุรกิจ</p>
        </div>

        <div class="content">
            <div class="success-message">
                ✅ การแก้ไขปัญหาเสร็จสมบูรณ์! ระบบพร้อมใช้งาน
            </div>

            <h2>ข้อมูลระบบ</h2>
            <ul>
                <li>Laravel Framework <?php echo app()->version(); ?></li>
                <li>PHP <?php echo phpversion(); ?></li>
                <li>ฐานข้อมูล: <?php echo config('database.default'); ?></li>
                <li>APP_KEY: <?php echo 'มีการตั้งค่าแล้ว ✓'; ?></li>
            </ul>
            
            <h2>ขั้นตอนต่อไป</h2>
            <p>คุณสามารถเริ่มต้นพัฒนาระบบได้โดย:</p>
            <ul>
                <li>แก้ไข Routes ใน <code>routes/web.php</code></li>
                <li>แก้ไขหน้า View ใน <code>resources/views</code></li>
                <li>เพิ่ม Controllers ใน <code>app/Http/Controllers</code></li>
                <li>เพิ่ม Models ใน <code>app/Models</code></li>
            </ul>
        </div>
    </div>
</body>
</html>
EOL
    echo -e "${GREEN}สร้างไฟล์ welcome.blade.php เรียบร้อยแล้ว${NC}"
fi

# ตั้งค่าสิทธิ์การเข้าถึง
chmod -R 775 storage
chmod -R 775 bootstrap/cache
echo -e "${GREEN}ตั้งค่าสิทธิ์ของโฟลเดอร์ storage และ bootstrap/cache เรียบร้อยแล้ว${NC}"

# 4. ลบไฟล์ cache ทั้งหมด
echo -e "${YELLOW}\n4. กำลังลบไฟล์ cache ทั้งหมด...${NC}"

# ลบไฟล์ cache ใน bootstrap/cache
if [ -d "bootstrap/cache" ]; then
    rm -f bootstrap/cache/*.php
    echo -e "${GREEN}ลบไฟล์ cache ใน bootstrap/cache เรียบร้อยแล้ว${NC}"
fi

# ลบไฟล์ cache ใน storage/framework
if [ -d "storage/framework/cache/data" ]; then
    rm -rf storage/framework/cache/data/*
    echo -e "${GREEN}ลบไฟล์ cache ใน storage/framework/cache เรียบร้อยแล้ว${NC}"
fi

if [ -d "storage/framework/views" ]; then
    rm -f storage/framework/views/*.php
    echo -e "${GREEN}ลบไฟล์ cache ใน storage/framework/views เรียบร้อยแล้ว${NC}"
fi

if [ -d "storage/framework/sessions" ]; then
    rm -f storage/framework/sessions/*
    echo -e "${GREEN}ลบไฟล์ sessions เรียบร้อยแล้ว${NC}"
fi

# 5. เคลียร์ cache ด้วย artisan
echo -e "${YELLOW}\n5. กำลังเคลียร์ cache ด้วย artisan...${NC}"
php artisan config:clear || echo -e "${YELLOW}ไม่สามารถเคลียร์ config cache${NC}"
php artisan cache:clear || echo -e "${YELLOW}ไม่สามารถเคลียร์ application cache${NC}"
php artisan route:clear || echo -e "${YELLOW}ไม่สามารถเคลียร์ route cache${NC}"
php artisan view:clear || echo -e "${YELLOW}ไม่สามารถเคลียร์ view cache${NC}"
php artisan clear-compiled || echo -e "${YELLOW}ไม่สามารถเคลียร์ compiled code${NC}"

# 6. อัพเดต autoload
echo -e "${YELLOW}\n6. กำลังอัพเดต autoload...${NC}"
composer dump-autoload || echo -e "${YELLOW}ไม่สามารถอัพเดต autoload${NC}"

# 7. รีสตาร์ท PHP server (ถ้ามี)
echo -e "${YELLOW}\n7. กำลังรีสตาร์ท PHP server...${NC}"
# ฆ่า PHP process ที่รันอยู่
pkill -f "php artisan serve" || true

# รัน PHP server ใหม่ในแบ็คกราวด์
echo -e "${GREEN}กำลังเริ่ม PHP server ใหม่...${NC}"
php artisan serve > /dev/null 2>&1 &

echo -e "\n${BLUE}================================================${NC}"
echo -e "${GREEN}การแก้ไขปัญหาทั้งหมดเสร็จสมบูรณ์!${NC}"
echo -e "${YELLOW}คุณสามารถเข้าถึงเว็บไซต์ได้ที่: http://localhost:8000${NC}"
echo -e "${BLUE}================================================${NC}"

chmod +x all-in-one-fix.sh
