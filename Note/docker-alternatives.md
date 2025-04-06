# ทางเลือกแทน Docker Desktop สำหรับรัน Laravel Sail

## 1. OrbStack (แนะนำสำหรับ macOS)

OrbStack เป็นทางเลือกที่เบากว่า เร็วกว่า และใช้ทรัพยากรน้อยกว่า Docker Desktop

### วิธีติดตั้ง:

1. ดาวน์โหลดและติดตั้งจาก [orbstack.dev](https://orbstack.dev/)
2. หลังติดตั้ง OrbStack จะให้คุณใช้คำสั่ง `docker` และ `docker-compose` ได้เหมือน Docker Desktop

### วิธีใช้กับ Laravel Sail:

```bash
# หลังจากติดตั้ง OrbStack แล้ว รันคำสั่งเหมือนปกติ
./vendor/bin/sail up -d
```

## 2. Colima

Colima เป็น container runtime สำหรับ macOS ที่ไม่ต้องการ Docker Desktop

### วิธีติดตั้ง:

```bash
# ติดตั้งผ่าน Homebrew
brew install colima

# เริ่มการทำงาน
colima start

# ถ้าต้องการใช้ docker-compose
brew install docker-compose
```

### วิธีใช้กับ Laravel Sail:

```bash
# หลังจากเริ่มการทำงานของ Colima แล้ว
./vendor/bin/sail up -d
```

## 3. Podman (ทางเลือกสำหรับ Linux และ macOS)

Podman เป็นทางเลือกที่ทำงานคล้าย Docker แต่ไม่ต้องการ daemon process

### วิธีติดตั้ง:

```bash
# macOS (ผ่าน Homebrew)
brew install podman
podman machine init
podman machine start

# Ubuntu/Debian
sudo apt-get update
sudo apt-get -y install podman
```

### วิธีใช้กับ Laravel Sail:

การใช้ Podman กับ Laravel Sail ต้องการการตั้งค่าเพิ่มเติม เนื่องจาก Sail ถูกออกแบบมาให้ทำงานกับ Docker โดยตรง คุณอาจจำเป็นต้องสร้าง alias:

```bash
# สร้าง alias เพื่อให้ docker command ทำงานผ่าน podman
alias docker='podman'
```

## 4. Lima (macOS)

Lima เป็นตัวเลือกอีกตัวที่ให้ Linux VM ที่มีประสิทธิภาพสูงบน macOS

### วิธีติดตั้ง:

```bash
brew install lima
limactl start
```

## 5. Rancher Desktop (Cross-platform)

Rancher Desktop ให้ Kubernetes และ container management บน Windows, macOS และ Linux

### วิธีติดตั้ง:

1. ดาวน์โหลดและติดตั้งจาก [rancherdesktop.io](https://rancherdesktop.io/)
2. เลือก "dockerd" เป็น container runtime
3. รอให้เริ่มต้นทำงาน

## ข้อควรระวัง:

-   ทางเลือกเหล่านี้อาจมีความแตกต่างเล็กน้อยในการทำงานเมื่อเทียบกับ Docker Desktop
-   อาจจะมีปัญหาความเข้ากันได้กับบางฟีเจอร์ของ Laravel Sail
-   หากพบปัญหา ให้ตรวจสอบว่าทางเลือกที่คุณเลือกสามารถรัน Docker Compose ได้หรือไม่ เพราะ Laravel Sail ใช้ Docker Compose ในการทำงาน
