#!/bin/bash

# ตั้งค่าสี
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}     Laravel Routes Fix Utility     ${NC}"
echo -e "${BLUE}================================================${NC}"

# ตรวจสอบว่าเป็นโปรเจกต์ Laravel
if [ ! -f "artisan" ]; then
    echo -e "${RED}ไม่พบไฟล์ artisan - ไม่ใช่โปรเจกต์ Laravel${NC}"
    exit 1
fi

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
else
    echo -e "${YELLOW}ไฟล์ routes/web.php มีอยู่แล้ว${NC}"
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
else
    echo -e "${YELLOW}ไฟล์ routes/api.php มีอยู่แล้ว${NC}"
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
else
    echo -e "${YELLOW}ไฟล์ routes/channels.php มีอยู่แล้ว${NC}"
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
else
    echo -e "${YELLOW}ไฟล์ routes/console.php มีอยู่แล้ว${NC}"
fi

# เคลียร์ route cache
echo -e "\n${YELLOW}กำลังเคลียร์ route cache...${NC}"
php artisan route:clear || echo -e "${YELLOW}ไม่สามารถเคลียร์ route cache${NC}"

echo -e "\n${GREEN}การแก้ไขปัญหาเกี่ยวกับ routes เสร็จสมบูรณ์!${NC}"
echo -e "${YELLOW}คุณสามารถรันคำสั่ง 'php artisan route:list' เพื่อตรวจสอบเส้นทาง${NC}"

chmod +x fix-routes-issue.sh
