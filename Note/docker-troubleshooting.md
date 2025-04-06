# วิธีแก้ไขปัญหา Docker ไม่ทำงาน

## 1. ตรวจสอบว่า Docker ถูกติดตั้งหรือยัง

```bash
# ตรวจสอบเวอร์ชันของ Docker
docker --version
```

## 2. ในกรณีที่ใช้ Docker Desktop

หากคุณใช้ Docker Desktop (สำหรับ macOS หรือ Windows):

1. เปิด Docker Desktop application
2. รอจนกว่า Docker Desktop จะเริ่มต้นทำงาน (สังเกตไอคอน whale ที่ taskbar/menu bar)
3. ตรวจสอบว่า Docker พร้อมใช้งานแล้วโดยรันคำสั่ง:
    ```bash
    docker info
    ```
4. หลังจากนั้น ลองรันคำสั่ง Sail อีกครั้ง:
    ```bash
    ./vendor/bin/sail up -d
    ```

## 3. ในกรณีที่ใช้ Docker Engine (Linux)

หากคุณใช้ Docker Engine โดยตรงบน Linux:

```bash
# ตรวจสอบสถานะ Docker service
sudo systemctl status docker

# เริ่มการทำงานของ Docker service (ถ้ายังไม่ทำงาน)
sudo systemctl start docker

# ตั้งค่าให้ Docker เริ่มทำงานอัตโนมัติเมื่อบูท
sudo systemctl enable docker
```

## 4. ตรวจสอบการติดตั้ง Docker

หากยังไม่ได้ติดตั้ง Docker:

### สำหรับ macOS:

1. ดาวน์โหลดและติดตั้ง Docker Desktop จาก [https://www.docker.com/products/docker-desktop](https://www.docker.com/products/docker-desktop)
2. เปิด Docker Desktop และรอจนกว่าจะ "Running"

### สำหรับ Windows:

1. ดาวน์โหลดและติดตั้ง Docker Desktop จาก [https://www.docker.com/products/docker-desktop](https://www.docker.com/products/docker-desktop)
2. ตรวจสอบว่า WSL2 และ Hyper-V เปิดใช้งาน (ตามคำแนะนำในตัวติดตั้ง)
3. เปิด Docker Desktop และรอจนกว่าจะ "Running"

### สำหรับ Linux:

```bash
# ติดตั้ง Docker บน Ubuntu
sudo apt-get update
sudo apt-get install docker-ce docker-ce-cli containerd.io

# ติดตั้ง Docker บน CentOS/RHEL
sudo yum install docker-ce docker-ce-cli containerd.io

# ตรวจสอบว่าติดตั้งสำเร็จ
docker --version
```

## 5. การแก้ไขปัญหาอื่นๆ

1. **ถ้ามีปัญหาเรื่องสิทธิ์**:

    ```bash
    # เพิ่มผู้ใช้ปัจจุบันเข้ากลุ่ม docker
    sudo usermod -aG docker $USER

    # จากนั้นออกจากระบบและเข้าสู่ระบบใหม่
    ```

2. **ถ้ามีปัญหา port conflict**:

    - ตรวจสอบว่ามีบริการอื่นใช้พอร์ตที่ซ้ำกับที่กำหนดใน docker-compose.yml
    - แก้ไขพอร์ตใน docker-compose.yml ถ้าจำเป็น

3. **ถ้า Docker มีปัญหา**:
    ```bash
    # Restart Docker service
    # สำหรับ Docker Desktop: Restart ผ่าน UI
    # สำหรับ Linux:
    sudo systemctl restart docker
    ```

## 6. หลังจาก Docker ทำงานแล้ว

เมื่อ Docker ทำงานปกติแล้ว คุณสามารถเริ่ม Laravel Sail:

```bash
# เริ่ม containers
./vendor/bin/sail up -d

# ตรวจสอบสถานะ
./vendor/bin/sail ps

# รัน migrations
./vendor/bin/sail artisan migrate
```

## 7. การติดตั้งและตั้งค่า Docker ฉบับเต็ม

สำหรับรายละเอียดเพิ่มเติมเกี่ยวกับการติดตั้งและการตั้งค่า Docker โปรดดูเอกสาร Docker อย่างเป็นทางการ:

-   [Docker Desktop สำหรับ Mac](https://docs.docker.com/docker-for-mac/install/)
-   [Docker Desktop สำหรับ Windows](https://docs.docker.com/docker-for-windows/install/)
-   [Docker Engine สำหรับ Linux](https://docs.docker.com/engine/install/)
