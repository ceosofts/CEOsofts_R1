# คู่มือการออกแบบ UI/UX สำหรับ CEOsofts R1

## 📋 สารบัญ

1. [หลักการออกแบบ](#หลักการออกแบบ)
2. [โครงสีหลัก](#โครงสีหลัก)
3. [ตัวอักษร](#ตัวอักษร)
4. [องค์ประกอบ UI](#องค์ประกอบ-ui)
5. [การจัดวาง Layout](#การจัดวาง-layout)
6. [การออกแบบที่ตอบสนอง (Responsive Design)](#การออกแบบที่ตอบสนอง-responsive-design)
7. [การเข้าถึง (Accessibility)](#การเข้าถึง-accessibility)
8. [รูปแบบของฟอร์ม](#รูปแบบของฟอร์ม)
9. [การแสดงข้อความและการแจ้งเตือน](#การแสดงข้อความและการแจ้งเตือน)
10. [ไอคอน](#ไอคอน)
11. [โหมดสีธีม (Light/Dark Mode)](#โหมดสีธีม-lightdark-mode) 👈 เพิ่มใหม่
12. [การจัดการแบบฟอร์มขั้นสูง](#การจัดการแบบฟอร์มขั้นสูง) 👈 เพิ่มใหม่
13. [ตัวอย่างหน้าจอที่พัฒนาแล้ว](#ตัวอย่างหน้าจอที่พัฒนาแล้ว) 👈 เพิ่มใหม่

## 🎯 หลักการออกแบบ

หลักการออกแบบหลักของระบบ CEOsofts R1 คือ:

1. **ความเรียบง่าย (Simplicity)**: ออกแบบให้เรียบง่าย เน้นการใช้งานได้จริง ลดความซับซ้อนที่ไม่จำเป็น
2. **ความสม่ำเสมอ (Consistency)**: สร้างประสบการณ์ที่สม่ำเสมอตลอดทั้งแอปพลิเคชัน
3. **ประสิทธิภาพ (Efficiency)**: ออกแบบให้ผู้ใช้สามารถทำงานได้อย่างรวดเร็วและมีประสิทธิภาพ
4. **ความยืดหยุ่น (Flexibility)**: รองรับการทำงานในหลากหลายสถานการณ์และอุปกรณ์
5. **การเข้าถึงได้ (Accessibility)**: ออกแบบให้ทุกคนสามารถเข้าถึงและใช้งานได้

## 🎨 โครงสีหลัก

ระบบ CEOsofts R1 ใช้โครงสีดังต่อไปนี้:

### สีหลัก (Primary Colors)

| ชื่อ        | สี                                                           | รหัสสี    | การใช้งาน             |
| ----------- | ------------------------------------------------------------ | --------- | --------------------- |
| Primary-50  | ![#f0f9ff](https://via.placeholder.com/15/f0f9ff/f0f9ff.png) | `#f0f9ff` | พื้นหลังเน้นเบา       |
| Primary-100 | ![#e0f2fe](https://via.placeholder.com/15/e0f2fe/e0f2fe.png) | `#e0f2fe` | พื้นหลังเน้น          |
| Primary-200 | ![#bae6fd](https://via.placeholder.com/15/bae6fd/bae6fd.png) | `#bae6fd` | พื้นหลังของ Component |
| Primary-300 | ![#7dd3fc](https://via.placeholder.com/15/7dd3fc/7dd3fc.png) | `#7dd3fc` | ขอบของ Component      |
| Primary-400 | ![#38bdf8](https://via.placeholder.com/15/38bdf8/38bdf8.png) | `#38bdf8` | ไอคอนและจุดเน้น       |
| Primary-500 | ![#0ea5e9](https://via.placeholder.com/15/0ea5e9/0ea5e9.png) | `#0ea5e9` | ปุ่มหลักและลิงก์      |
| Primary-600 | ![#0284c7](https://via.placeholder.com/15/0284c7/0284c7.png) | `#0284c7` | ปุ่ม Hover state      |
| Primary-700 | ![#0369a1](https://via.placeholder.com/15/0369a1/0369a1.png) | `#0369a1` | ปุ่ม Active state     |
| Primary-800 | ![#075985](https://via.placeholder.com/15/075985/075985.png) | `#075985` | หัวข้อสำคัญ           |
| Primary-900 | ![#0c4a6e](https://via.placeholder.com/15/0c4a6e/0c4a6e.png) | `#0c4a6e` | หัวข้อหลัก            |

### สีรอง (Secondary Colors)

| ชื่อ          | สี                                                           | รหัสสี    | การใช้งาน       |
| ------------- | ------------------------------------------------------------ | --------- | --------------- |
| Secondary-50  | ![#f8fafc](https://via.placeholder.com/15/f8fafc/f8fafc.png) | `#f8fafc` | พื้นหลังหลัก    |
| Secondary-100 | ![#f1f5f9](https://via.placeholder.com/15/f1f5f9/f1f5f9.png) | `#f1f5f9` | พื้นหลังเนื้อหา |
| Secondary-200 | ![#e2e8f0](https://via.placeholder.com/15/e2e8f0/e2e8f0.png) | `#e2e8f0` | ขอบตาราง        |
| Secondary-300 | ![#cbd5e1](https://via.placeholder.com/15/cbd5e1/cbd5e1.png) | `#cbd5e1` | เส้นแบ่ง        |
| Secondary-400 | ![#94a3b8](https://via.placeholder.com/15/94a3b8/94a3b8.png) | `#94a3b8` | ข้อความรอง      |
| Secondary-500 | ![#64748b](https://via.placeholder.com/15/64748b/64748b.png) | `#64748b` | ข้อความทั่วไป   |
| Secondary-600 | ![#475569](https://via.placeholder.com/15/475569/475569.png) | `#475569` | ข้อความสำคัญ    |
| Secondary-700 | ![#334155](https://via.placeholder.com/15/334155/334155.png) | `#334155` | หัวข้อ          |
| Secondary-800 | ![#1e293b](https://via.placeholder.com/15/1e293b/1e293b.png) | `#1e293b` | หัวข้อสำคัญ     |
| Secondary-900 | ![#0f172a](https://via.placeholder.com/15/0f172a/0f172a.png) | `#0f172a` | หัวข้อหลัก      |

### สีเสริม (Accent Colors)

| ประเภท  | สี                                                           | รหัสสี    | การใช้งาน    |
| ------- | ------------------------------------------------------------ | --------- | ------------ |
| Success | ![#10b981](https://via.placeholder.com/15/10b981/10b981.png) | `#10b981` | สถานะสำเร็จ  |
| Warning | ![#f59e0b](https://via.placeholder.com/15/f59e0b/f59e0b.png) | `#f59e0b` | สถานะเตือน   |
| Danger  | ![#ef4444](https://via.placeholder.com/15/ef4444/ef4444.png) | `#ef4444` | สถานะผิดพลาด |
| Info    | ![#3b82f6](https://via.placeholder.com/15/3b82f6/3b82f6.png) | `#3b82f6` | สถานะข้อมูล  |

## 🔤 ตัวอักษร

### ฟอนต์หลัก

-   **หัวข้อ (Heading)**: "Prompt" สำหรับข้อความภาษาไทยและอังกฤษ
-   **เนื้อหา (Body)**: "Figtree" สำหรับข้อความภาษาอังกฤษ และ "Prompt" สำหรับข้อความภาษาไทย

### ขนาดตัวอักษร

| ชื่อ | ขนาด            | การใช้งาน        |
| ---- | --------------- | ---------------- |
| xs   | 0.75rem (12px)  | ข้อความเล็กพิเศษ |
| sm   | 0.875rem (14px) | ข้อความย่อย      |
| base | 1rem (16px)     | ข้อความทั่วไป    |
| lg   | 1.125rem (18px) | ข้อความเน้น      |
| xl   | 1.25rem (20px)  | หัวข้อย่อย       |
| 2xl  | 1.5rem (24px)   | หัวข้อกลาง       |
| 3xl  | 1.875rem (30px) | หัวข้อใหญ่       |
| 4xl  | 2.25rem (36px)  | หัวข้อใหญ่พิเศษ  |

### น้ำหนักตัวอักษร

-   Light (300): ข้อความที่ไม่เน้น
-   Regular (400): ข้อความทั่วไป
-   Medium (500): ข้อความที่สำคัญ
-   SemiBold (600): หัวข้อย่อย
-   Bold (700): หัวข้อหลัก

## 🧩 องค์ประกอบ UI

### ปุ่ม (Buttons)

ปุ่มมี 4 ขนาด:

1. **เล็ก (sm)**: สำหรับพื้นที่จำกัด

    ```html
    <button
        class="bg-primary-500 text-white px-2 py-1 text-sm rounded-md hover:bg-primary-600"
    >
        ปุ่มเล็ก
    </button>
    ```

2. **ปกติ (base)**: ใช้โดยทั่วไป

    ```html
    <button
        class="bg-primary-500 text-white px-4 py-2 rounded-md hover:bg-primary-600"
    >
        ปุ่มปกติ
    </button>
    ```

3. **ใหญ่ (lg)**: สำหรับปุ่มสำคัญ

    ```html
    <button
        class="bg-primary-500 text-white px-6 py-3 text-lg rounded-md hover:bg-primary-600"
    >
        ปุ่มใหญ่
    </button>
    ```

4. **เต็ม (full)**: กว้างเต็มพื้นที่
    ```html
    <button
        class="bg-primary-500 text-white px-4 py-2 w-full rounded-md hover:bg-primary-600"
    >
        ปุ่มเต็ม
    </button>
    ```

ประเภทของปุ่ม:

1. **ปุ่มหลัก (Primary)**: ใช้สำหรับการกระทำหลักในหน้า

    ```html
    <button
        class="bg-primary-500 text-white px-4 py-2 rounded-md hover:bg-primary-600 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
    >
        บันทึก
    </button>
    ```

2. **ปุ่มรอง (Secondary)**: สำหรับการกระทำรองลงมา

    ```html
    <button
        class="bg-secondary-100 text-secondary-800 border border-secondary-300 px-4 py-2 rounded-md hover:bg-secondary-200 focus:ring-2 focus:ring-secondary-400 focus:ring-offset-2"
    >
        ยกเลิก
    </button>
    ```

3. **ปุ่มอันตราย (Danger)**: สำหรับการกระทำที่อาจเป็นอันตราย

    ```html
    <button
        class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
    >
        ลบ
    </button>
    ```

4. **ปุ่มลิงก์ (Link)**: ดูเหมือนลิงก์แต่ทำงานเหมือนปุ่ม
    ```html
    <button class="text-primary-500 hover:underline">ดูเพิ่มเติม</button>
    ```

### การ์ด (Cards)

```html
<div class="bg-white rounded-lg shadow-md p-4 md:p-6">
    <h3 class="text-xl font-semibold text-secondary-800 mb-2">หัวข้อการ์ด</h3>
    <p class="text-secondary-600">เนื้อหาในการ์ด</p>
    <div class="mt-4 pt-4 border-t border-secondary-200">
        <button class="text-primary-500 hover:text-primary-700">
            ดำเนินการ
        </button>
    </div>
</div>
```

### แถบนำทาง (Navigation)

เพิ่มส่วนการออกแบบเมนูนำทางในระบบ:

### ฟอร์มคอนโทรล (Form Controls)

-   **Input Text**:

    ```html
    <div class="mb-4">
        <label
            for="username"
            class="block text-sm font-medium text-secondary-700 mb-1"
            >ชื่อผู้ใช้</label
        >
        <input
            type="text"
            id="username"
            name="username"
            class="block w-full rounded-md border-secondary-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50"
        />
    </div>
    ```

-   **Select**:

    ```html
    <div class="mb-4">
        <label
            for="country"
            class="block text-sm font-medium text-secondary-700 mb-1"
            >ประเทศ</label
        >
        <select
            id="country"
            name="country"
            class="block w-full rounded-md border-secondary-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50"
        >
            <option value="">เลือกประเทศ</option>
            <option value="th">ไทย</option>
            <option value="us">สหรัฐอเมริกา</option>
        </select>
    </div>
    ```

-   **Checkbox**:
    ```html
    <div class="mb-4">
        <div class="flex items-center">
            <input
                id="remember"
                name="remember"
                type="checkbox"
                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-secondary-300 rounded"
            />
            <label for="remember" class="ml-2 block text-sm text-secondary-700"
                >จดจำฉัน</label
            >
        </div>
    </div>
    ```

### ตาราง (Tables)

```html
<table class="min-w-full divide-y divide-secondary-200">
    <thead class="bg-secondary-50">
        <tr>
            <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider"
            >
                ชื่อ
            </th>
            <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider"
            >
                ตำแหน่ง
            </th>
            <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider"
            >
                สถานะ
            </th>
            <th
                scope="col"
                class="px-6 py-3 text-right text-xs font-medium text-secondary-500 uppercase tracking-wider"
            >
                จัดการ
            </th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-secondary-200">
        <tr>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-secondary-900">
                    สมชาย ใจดี
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-secondary-500">ผู้จัดการ</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"
                    >ทำงาน</span
                >
            </td>
            <td
                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
            >
                <a href="#" class="text-primary-600 hover:text-primary-800 mr-4"
                    >แก้ไข</a
                >
                <a href="#" class="text-red-600 hover:text-red-800">ลบ</a>
            </td>
        </tr>
    </tbody>
</table>
```

## 📏 การจัดวาง Layout

### หลักการจัดวางเลย์เอาต์

1. **โครงสร้างหลัก**:

    - Sidebar Navigation ด้านซ้าย
    - Header ด้านบน
    - เนื้อหาหลักตรงกลาง
    - Footer ด้านล่าง (ถ้ามี)

2. **Container**:
    - ใช้ความกว้างสูงสุด 1280px สำหรับเนื้อหา
    - ให้มี padding ด้านข้างเพื่อความสวยงาม

```html
<div class="min-h-screen bg-secondary-50 flex">
    <!-- Sidebar -->
    <div class="w-64 bg-secondary-800 text-white">
        <!-- Sidebar content -->
    </div>

    <!-- Main Content Area -->
    <div class="flex-1">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <!-- Header content -->
            </div>
        </header>

        <!-- Page Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Content goes here -->
        </main>
    </div>
</div>
```

### Grid System

ใช้ Grid System ของ Tailwind CSS สำหรับการจัดวาง:

```html
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="bg-white p-4 rounded-lg shadow-md">Item 1</div>
    <div class="bg-white p-4 rounded-lg shadow-md">Item 2</div>
    <div class="bg-white p-4 rounded-lg shadow-md">Item 3</div>
</div>
```

### Spacing

ใช้ระบบ spacing ของ Tailwind CSS:

-   `p-{size}`: padding รอบด้าน
-   `px-{size}`: padding แนวนอน
-   `py-{size}`: padding แนวตั้ง
-   `m-{size}`: margin รอบด้าน
-   `mx-{size}`: margin แนวนอน
-   `my-{size}`: margin แนวตั้ง

ค่า size: 0, 0.5, 1, 1.5, 2, 2.5, 3, 3.5, 4, 5, 6, 7, 8, 9, 10, 11, 12, 14, 16, 20, 24, 28, 32, 36, 40, 44, 48, 52, 56, 60, 64, 72, 80, 96

## 📱 การออกแบบที่ตอบสนอง (Responsive Design)

### Breakpoints

-   **sm**: 640px และมากกว่า
-   **md**: 768px และมากกว่า
-   **lg**: 1024px และมากกว่า
-   **xl**: 1280px และมากกว่า
-   **2xl**: 1536px และมากกว่า

### หลักการออกแบบ Responsive

1. **Mobile First**: ออกแบบสำหรับอุปกรณ์มือถือก่อนเสมอ
2. **Stack on mobile, side-by-side on desktop**: วางองค์ประกอบในแนวตั้งบนมือถือ แนวนอนบนเดสก์ท็อป
3. **Hide/Show Elements**: ซ่อนหรือแสดงองค์ประกอบตามขนาดหน้าจอ
4. **Adapt Font Sizes**: ปรับขนาดตัวอักษรตามความเหมาะสม

ตัวอย่าง:

```html
<div class="flex flex-col md:flex-row">
    <div class="w-full md:w-1/3 mb-4 md:mb-0 md:mr-4">
        <!-- Sidebar content -->
    </div>
    <div class="w-full md:w-2/3">
        <!-- Main content -->
    </div>
</div>
```

## ♿ การเข้าถึง (Accessibility)

### หลักการ Accessibility

1. **Semantic HTML**: ใช้ HTML ที่มีความหมายเหมาะสม
2. **ARIA Attributes**: เพิ่ม ARIA เมื่อจำเป็น
3. **Keyboard Navigation**: รองรับการใช้งานผ่าน keyboard
4. **Color Contrast**: ใช้สีที่มี contrast ratio ที่เพียงพอ
5. **Text Alternatives**: เพิ่ม alt text สำหรับรูปภาพ

ตัวอย่าง:

```html
<!-- Using semantic HTML -->
<nav>
    <ul>
        <li>
            <a href="#" class="text-secondary-600 hover:text-secondary-900"
                >หน้าหลัก</a
            >
        </li>
    </ul>
</nav>

<!-- Using ARIA -->
<button
    aria-label="เปิดเมนู"
    aria-expanded="false"
    class="text-secondary-600 hover:text-secondary-900"
>
    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M4 6h16M4 12h16M4 18h16"
        />
    </svg>
</button>
```

## 📝 รูปแบบของฟอร์ม

### หลักการออกแบบฟอร์ม

1. **ชัดเจน**: ฟอร์มควรเข้าใจง่ายและไม่สับสน
2. **การจัดกลุ่ม**: จัดกลุ่มฟิลด์ที่เกี่ยวข้องไว้ด้วยกัน
3. **การตรวจสอบ**: แสดงข้อผิดพลาดอย่างชัดเจน
4. **การช่วยเหลือ**: มีข้อความช่วยเหลือสำหรับฟิลด์ที่ซับซ้อน

ตัวอย่าง:

```html
<form class="space-y-6">
    <div>
        <h3 class="text-lg font-medium text-secondary-900">ข้อมูลส่วนตัว</h3>
        <div class="mt-4 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
            <div class="sm:col-span-3">
                <label
                    for="first_name"
                    class="block text-sm font-medium text-secondary-700"
                    >ชื่อ</label
                >
                <div class="mt-1">
                    <input
                        type="text"
                        name="first_name"
                        id="first_name"
                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-secondary-300 rounded-md"
                    />
                </div>
            </div>

            <div class="sm:col-span-3">
                <label
                    for="last_name"
                    class="block text-sm font-medium text-secondary-700"
                    >นามสกุล</label
                >
                <div class="mt-1">
                    <input
                        type="text"
                        name="last_name"
                        id="last_name"
                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-secondary-300 rounded-md"
                    />
                </div>
            </div>
        </div>
    </div>

    <div>
        <h3 class="text-lg font-medium text-secondary-900">ข้อมูลติดต่อ</h3>
        <div class="mt-4 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
            <div class="sm:col-span-4">
                <label
                    for="email"
                    class="block text-sm font-medium text-secondary-700"
                    >อีเมล</label
                >
                <div class="mt-1">
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-secondary-300 rounded-md"
                    />
                </div>
                <p class="mt-2 text-sm text-secondary-500">
                    เราจะไม่แชร์อีเมลของคุณกับบุคคลอื่น
                </p>
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        <button
            type="button"
            class="bg-white py-2 px-4 border border-secondary-300 rounded-md shadow-sm text-sm font-medium text-secondary-700 hover:bg-secondary-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
        >
            ยกเลิก
        </button>
        <button
            type="submit"
            class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
        >
            บันทึก
        </button>
    </div>
</form>
```

## 💬 การแสดงข้อความและการแจ้งเตือน

### ประเภทของการแจ้งเตือน

1. **ข้อความสำเร็จ (Success)**:

    ```html
    <div class="rounded-md bg-green-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <!-- Icon -->
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">
                    บันทึกข้อมูลสำเร็จแล้ว
                </p>
            </div>
        </div>
    </div>
    ```

2. **ข้อความเตือน (Warning)**:

    ```html
    <div class="rounded-md bg-yellow-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <!-- Icon -->
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-yellow-800">
                    กรุณาตรวจสอบข้อมูลก่อนดำเนินการต่อ
                </p>
            </div>
        </div>
    </div>
    ```

3. **ข้อความผิดพลาด (Error)**:

    ```html
    <div class="rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <!-- Icon -->
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">
                    เกิดข้อผิดพลาดในการบันทึกข้อมูล
                </p>
            </div>
        </div>
    </div>
    ```

4. **ข้อความข้อมูล (Info)**:
    ```html
    <div class="rounded-md bg-blue-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <!-- Icon -->
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-blue-800">
                    ระบบอยู่ระหว่างการปรับปรุง
                </p>
            </div>
        </div>
    </div>
    ```

## 🔣 ไอคอน

### การใช้ไอคอน

-   ใช้ [Heroicons](https://heroicons.com/) เป็นชุดไอคอนหลัก
-   ขนาดไอคอนมาตรฐาน: 20x20 pixels (1.25rem)

ตัวอย่างการใช้:

```html
<!-- Solid Icon -->
<svg
    class="h-5 w-5"
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 20 20"
    fill="currentColor"
>
    <path
        fill-rule="evenodd"
        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
        clip-rule="evenodd"
    />
</svg>

<!-- Outline Icon -->
<svg
    class="h-5 w-5"
    xmlns="http://www.w3.org/2000/svg"
    fill="none"
    viewBox="0 0 24 24"
    stroke="currentColor"
>
    <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M5 13l4 4L19 7"
    />
</svg>
```

## 📚 แหล่งข้อมูลอ้างอิง

1. [Tailwind CSS Documentation](https://tailwindcss.com/docs)
2. [Heroicons](https://heroicons.com/)
3. [Web Content Accessibility Guidelines (WCAG)](https://www.w3.org/WAI/standards-guidelines/wcag/)

---

คู่มือนี้จะช่วยให้ทีมพัฒนาสามารถสร้างส่วนติดต่อผู้ใช้ที่สวยงาม ใช้งานง่าย และมีความสม่ำเสมอตลอดทั้งแอปพลิเคชัน CEOsofts R1
