# Technical Specifications สำหรับ CEOsofts

## สถาปัตยกรรมระบบ (System Architecture)

### Domain-Driven Design (DDD)

CEOsofts ใช้แนวคิด Domain-Driven Design (DDD) ซึ่งเป็นวิธีการออกแบบซอฟต์แวร์ที่มุ่งเน้นที่โดเมนธุรกิจหลัก โดยแบ่ง Domain ดังนี้:

1. **Organization Domain** - จัดการโครงสร้างองค์กร (บริษัท แผนก ตำแหน่ง สาขา)
2. **HumanResources Domain** - จัดการทรัพยากรบุคคล (พนักงาน การลางาน การเข้างาน เงินเดือน)
3. **Sales Domain** - จัดการการขาย (ลูกค้า ใบเสนอราคา คำสั่งซื้อ ใบแจ้งหนี้ ใบเสร็จรับเงิน)
4. **Inventory Domain** - จัดการสินค้า (สินค้า หมวดหมู่ คลัง การเคลื่อนไหวสินค้า)
5. **Finance Domain** - จัดการการเงิน (การรับชำระเงิน ค่าใช้จ่าย ภาษี)
6. **Settings Domain** - จัดการการตั้งค่าระบบ (ผู้ใช้ บทบาท สิทธิ์ การตั้งค่า)
7. **DocumentGeneration Domain** - จัดการเอกสาร (เทมเพลต PDF การสร้างเอกสาร การส่งเอกสาร)

### Layered Architecture

ระบบได้รับการออกแบบด้วยสถาปัตยกรรมแบบชั้น (Layered Architecture) ดังนี้:

1. **Domain Layer** - เป็นหัวใจของระบบ ประกอบด้วย:

    - **Entities** - โมเดลที่มีเอกลักษณ์และต่อเนื่อง (เช่น Company, Employee)
    - **Value Objects** - โมเดลที่ไม่มีเอกลักษณ์ แต่มีความหมายในตัวเอง (เช่น Address, Money)
    - **Repositories (Interfaces)** - สัญญาสำหรับการเข้าถึงข้อมูล
    - **Domain Services** - บริการที่จัดการกับหลาย Entity/Value Object
    - **Domain Events** - เหตุการณ์ที่เกิดขึ้นและอาจส่งผลต่อส่วนอื่นของระบบ

2. **Application Layer** - ทำหน้าที่ประสานงานระหว่างชั้นต่างๆ:

    - **Application Services** - การใช้ Domain Services เพื่อทำภารกิจ
    - **Command Handlers** - จัดการคำสั่งที่เข้ามาในระบบ
    - **Query Handlers** - จัดการการดึงข้อมูล
    - **DTOs** (Data Transfer Objects) - โครงสร้างข้อมูลสำหรับการส่งข้อมูลระหว่างชั้น

3. **Infrastructure Layer** - จัดการกับรายละเอียดทางเทคนิคต่างๆ:

    - **Repository Implementations** - การนำ Repository Interfaces ไปปฏิบัติ
    - **Database Access** - การเข้าถึงฐานข้อมูล
    - **External Services** - บริการภายนอก (เช่น การชำระเงิน, SMS)
    - **Persistence** - การจัดเก็บข้อมูล

4. **UI Layer** - ส่วนติดต่อกับผู้ใช้:
    - **Web UIs** - ส่วนติดต่อผู้ใช้บนเว็บ (Controllers, Views)
    - **API Endpoints** - จุดเชื่อมต่อ API
    - **CLI Commands** - คำสั่ง Command Line

## รายละเอียดเทคโนโลยี (Technology Stack)

### Backend Framework

-   **Laravel 11+**
    -   PHP 8.4+
    -   Laravel Sanctum สำหรับ Authentication ของ API และ SPA
    -   Laravel Telescope สำหรับ Debugging ในสภาพแวดล้อมการพัฒนา
    -   Laravel Horizon สำหรับ Queue Management และการติดตาม
    -   Laravel Jetstream สำหรับ Authentication UI (ถ้าใช้ Livewire)

### Frontend Technologies

#### Option A: Livewire + Alpine.js + Blade (แนะนำ)

-   **Livewire 3** - สำหรับ Server-Side Rendering และ Reactivity โดยไม่ต้องเขียน JavaScript มากนัก
-   **Alpine.js** - สำหรับ Client-Side Interactivity ที่ไม่ซับซ้อนมาก
-   **Blade Components** - สำหรับสร้าง Reusable UI Components
-   **Blade แบบ SPA** - ผสาน Livewire และ Alpine.js เพื่อสร้างประสบการณ์ SPA

#### Option B: Inertia.js + Vue 3

-   **Inertia.js** - เชื่อมระหว่าง Laravel และ Vue โดยไม่ต้องสร้าง API
-   **Vue 3** - Composition API สำหรับสร้าง Components ที่ซับซ้อน
-   **Pinia** - State Management สำหรับ Vue 3
-   **Vue Router 4.x** - สำหรับการจัดการ Routing ใน Vue

### Database

-   **MySQL 8.0+** หรือ **PostgreSQL 15+**
    -   MySQL สำหรับความง่ายในการติดตั้งและใช้งาน
    -   PostgreSQL สำหรับคุณสมบัติขั้นสูง (JSON, Array, Partitioning)
-   **Redis** - สำหรับ Caching, Session Storage, และ Queue
-   **Laravel Migrations** - สำหรับจัดการโครงสร้างฐานข้อมูล
-   **Laravel Eloquent ORM** - สำหรับการโต้ตอบกับฐานข้อมูล

### CSS Framework & UI Tools

-   **TailwindCSS 3.x**
    -   JIT (Just-In-Time) Compiler ทำให้ไฟล์ CSS มีขนาดเล็ก
    -   TailwindCSS Forms Plugin สำหรับจัดการ Form Elements
    -   TailwindCSS Typography Plugin สำหรับจัดการเนื้อหา
-   **Heroicons** - ไอคอนที่ออกแบบให้ทำงานร่วมกับ TailwindCSS
-   **AplineUI** หรือ **Tailwind UI** - UI Component Library

### Build Tools

-   **Vite** - สำหรับ Dev Server และ Asset Bundling
-   **Laravel Vite Plugin** - สำหรับใช้ Vite กับ Laravel
-   **PostCSS** - สำหรับแปลงและปรับแต่ง CSS
-   **Node.js 18+** - สำหรับ Development Environment

### Testing Framework

-   **Pest** - Modern Testing Framework สำหรับ PHP ที่สร้างบน PHPUnit
    -   PestPHP/Expectations สำหรับ Assertions ที่อ่านง่าย
    -   PestPHP/Laravel สำหรับ Laravel-specific Testing
-   **Laravel Dusk** - สำหรับ Browser Testing
-   **Cypress** - สำหรับ End-to-End Testing บน Frontend
-   **Mock Service Worker (MSW)** - สำหรับ Mock API Responses ใน Test

### DevOps & Deployment

-   **Docker** สำหรับการพัฒนาและการ Deploy
    -   Laravel Sail สำหรับ Local Development Environment
    -   Docker Compose สำหรับการจัดการ Services
-   **GitHub Actions** สำหรับ CI/CD
    -   Automated Testing
    -   Code Quality Checks
    -   Automated Deployment
-   **Laravel Forge** หรือ **Envoyer** สำหรับ Server Management และ Deployment
-   **AWS** หรือ **Digital Ocean** สำหรับ Hosting และ Infrastructure

### APIs

-   **RESTful API** - เป็นหลักสำหรับการเชื่อมต่อ
    -   Laravel API Resources สำหรับการแปลงข้อมูล
    -   Consistent JSON Response Structure
-   **JSON:API** standard (optional) - สำหรับ API ที่เป็นมาตรฐาน
-   **API Documentation**
    -   OpenAPI/Swagger Documentation
    -   Postman Collection
    -   API Blueprint

## Detailed Implementation Strategies

### Multi-tenancy Implementation

เราจะใช้ "Company ID based Multi-tenancy" ซึ่งหมายถึง:

1. **Database Level**

    - โมเดลทั้งหมดที่เกี่ยวข้องกับธุรกิจจะมีคอลัมน์ `company_id` เพื่อแบ่งแยกข้อมูล
    - ใช้ Foreign Key Constraints `company_id → companies.id` ทุกตาราง
    - สร้าง Composite Indexes ที่รวม company_id ในทุกตารางที่ค้นหาบ่อย

2. **Application Level**

    - ใช้ Global Scope ที่ใช้งานผ่าน Trait `HasCompanyScope`

    ```php
    // ตัวอย่าง Trait HasCompanyScope
    trait HasCompanyScope {
        public static function bootHasCompanyScope() {
            static::addGlobalScope('company', function($query) {
                if (auth()->check()) {
                    $query->where('company_id', current_company_id());
                }
            });
        }
    }
    ```

3. **Security Level**

    - ใช้ Middleware ตรวจสอบการเข้าถึงเฉพาะข้อมูลที่เป็นของ Company นั้น
    - นโยบายการอนุญาต (Policies) ตรวจสอบ company_id
    - Validation Rules สำหรับตรวจสอบ company_id ในทุก Request

4. **Scoping Relations**
    - ความสัมพันธ์ระหว่าง Models จะถูก scoped ตาม Company
    - ใช้ constrained() ใน Laravel Relationships

### Authentication Implementation

1. **Multi-system Authentication**

    - ใช้ Laravel Sanctum สำหรับ API Authentication และ SPA Authentication
    - ใช้ Laravel Web Authentication (Session) สำหรับ Web Interface

2. **Authentication Features**

    - Login with Email/Password
    - Two-Factor Authentication (2FA)
    - Remember Me
    - Password Reset
    - Email Verification
    - Login History & Tracking
    - Account Locking after Failed Attempts

3. **Social Authentication (Optional)**
    - Google OAuth2
    - Facebook OAuth2
    - Line Sign-In (สำหรับตลาดเอเชีย)

### Authorization & RBAC System

1. **Role-Based Access Control**

    - Roles: Pre-defined sets of permissions (e.g., Admin, Manager, Staff)
    - Permissions: Fine-grained access controls (e.g., view-users, edit-users)
    - การใช้ Spatie Laravel-Permission

2. **Implementation**

    ```php
    // ตัวอย่างการใช้ในโค้ด
    // ตรวจสอบสิทธิ์ใน Controller
    public function update(Request $request, Invoice $invoice) {
        $this->authorize('update', $invoice);
        // ...
    }

    // ตรวจสอบใน Template
    @can('update', $invoice)
        <button>Edit</button>
    @endcan
    ```

3. **ความสัมพันธ์กับ Multi-tenancy**
    - บทบาทและสิทธิ์จะถูกแบ่งตาม Company
    - ผู้ใช้สามารถมีบทบาทที่แตกต่างกันในแต่ละ Company
    - Super Admin จะมีสิทธิ์เข้าถึงทุก Company

### PDF Document Generation System

1. **เทคโนโลยี**

    - **DomPDF** - เป็นหลัก เนื่องจากง่ายต่อการใช้งานและการติดตั้ง
    - **Snappy PDF** (wkhtmltopdf) - ทางเลือกสำหรับเอกสารที่ซับซ้อน
    - **Laravel-DomPDF** และ **Laravel-Snappy** - Wrapper สำหรับใช้งานใน Laravel

2. **การจัดการเทมเพลต**

    - เทมเพลตแบบ Blade สำหรับ PDF
    - จัดเก็บเทมเพลตใน database พร้อมกับการปรับแต่งได้
    - CSS เฉพาะสำหรับการพิมพ์

3. **ฟีเจอร์ PDF**
    - Dynamic เทมเพลตแยกตามประเภทเอกสาร
    - Header และ Footer ที่กำหนดเองได้
    - ลายเซ็นแบบดิจิทัล
    - การส่งทางอีเมล
    - การจัดเก็บประวัติ

### Security Implementation

1. **การป้องกันการโจมตีทั่วไป**

    - CSRF Protection ด้วย Laravel's built-in CSRF Protection
    - XSS Protection ด้วย Laravel's built-in HTML Purifier และ Blade Escaping
    - SQL Injection Protection ด้วย Laravel's Query Builder และ Prepared Statements
    - Content Security Policy (CSP) Headers

2. **Data Security**

    - Encryption at Rest ด้วย Laravel's built-in encryption
    - HTTPS สำหรับการสื่อสารทั้งหมด
    - Encrypted Sessions และ Cookies

3. **Validation & Sanitization**

    - Form Request Validation
    - Input Sanitization
    - Output Escaping

4. **Authentication Security**

    - Password Hashing ด้วย bcrypt/Argon2
    - IP และ User-Agent Tracking
    - Login Throttling
    - Logout จากทุกอุปกรณ์

5. **Audit Logging**

    - เก็บประวัติการเข้าถึงและการเปลี่ยนแปลงข้อมูล
    - จัดเก็บ IP Address และ User-Agent
    - บันทึกการเปลี่ยนแปลงข้อมูลสำคัญ (เช่น ข้อมูลทางการเงิน)

6. **Security Headers**
    - X-Content-Type-Options: nosniff
    - X-XSS-Protection: 1; mode=block
    - X-Frame-Options: DENY/SAMEORIGIN
    - Referrer-Policy: strict-origin-when-cross-origin

## Performance Optimization Strategies

### 1. Caching Strategy

การใช้ Cache เพื่อเพิ่มประสิทธิภาพของระบบ:

-   **Redis** สำหรับ Cache Store

    ```php
    // ตัวอย่างการใช้ Cache
    $products = Cache::tags(['products', 'company:'.$companyId])
        ->remember('products:all:'.$companyId, 3600, function () use ($companyId) {
            return Product::whereCompanyId($companyId)->get();
        });
    ```

-   **Cache Tags** สำหรับการจัดการ Cache ตามประเภทข้อมูล

    ```php
    // Invalidate cache เมื่อมีการอัพเดทข้อมูล
    Cache::tags(['products', 'company:'.$product->company_id])->flush();
    ```

-   **Cache Time-to-Live (TTL)** กำหนดตามความถี่ในการเปลี่ยนแปลงข้อมูล
    -   Master Data: 24 ชั่วโมง
    -   Transaction Data: 1 ชั่วโมง
    -   Real-time Data: ไม่เก็บ Cache หรือ Cache แค่ 1-5 นาที

### 2. Query Optimization

-   **Eager Loading** เพื่อแก้ปัญหา N+1 Queries

    ```php
    // แทนที่จะเป็น
    $orders = Order::all();
    foreach ($orders as $order) {
        echo $order->customer->name; // เกิด N+1 queries
    }

    // ใช้ eager loading
    $orders = Order::with('customer')->get();
    foreach ($orders as $order) {
        echo $order->customer->name; // ไม่มี query เพิ่มเติม
    }
    ```

-   **Database Indexing** ตามรูปแบบการค้นหา

    -   คอลัมน์ที่ใช้ใน WHERE, JOIN, ORDER BY
    -   Composite Index สำหรับการค้นหาหลายเงื่อนไขพร้อมกัน

-   **Query Optimizations**

    -   เลือกเฉพาะคอลัมน์ที่จำเป็น `select('id', 'name', 'price')`
    -   Chunking สำหรับข้อมูลขนาดใหญ่
    -   Pagination สำหรับการแสดงผลข้อมูลจำนวนมาก

-   **Database Monitoring**
    -   Laravel Telescope สำหรับ Development
    -   Database Query Logger สำหรับ Production

### 3. Asset Optimization

-   **Vite** สำหรับการ Bundle JavaScript และ CSS

    -   Code Splitting
    -   Tree Shaking
    -   Minification

-   **Lazy Loading**

    -   Lazy Load Components
    -   Lazy Load Images
    -   Route-based Code Splitting

-   **CDN** สำหรับ Static Assets
    -   Images
    -   CSS, JavaScript ที่ Compiled แล้ว
    -   Fonts

## Localization Strategy

ระบบรองรับการทำงานแบบหลายภาษา:

### 1. ภาษาที่รองรับ

-   **ไทย (th)** - เป็นภาษาเริ่มต้น
-   **อังกฤษ (en)** - เป็นภาษาสำรอง

### 2. การจัดการแปลภาษา

-   **Laravel Localization** - ใช้ไฟล์ JSON และ PHP Arrays

    ```php
    // ตัวอย่างการใช้
    {{ __('messages.welcome') }}
    // หรือใน Blade
    @lang('messages.welcome')
    ```

-   **Dynamic Translations** - เก็บการแปลในฐานข้อมูล

    ```php
    // Model Translation
    public function getNameAttribute($value)
    {
        return $this->translate('name', $value);
    }

    protected function translate($field, $default)
    {
        $locale = app()->getLocale();
        $translation = $this->translations()
            ->where('field', $field)
            ->where('locale', $locale)
            ->first();

        return $translation ? $translation->value : $default;
    }
    ```

### 3. Date, Time, และ Number Formatting

-   **Carbon** สำหรับจัดการวันที่ตามภาษา

    ```php
    // ตัวอย่างการใช้
    Carbon::now()->locale('th')->isoFormat('LL');
    ```

-   **NumberFormatter** สำหรับจัดการรูปแบบตัวเลขตามภาษา
    ```php
    // ตัวอย่างการใช้
    $fmt = new NumberFormatter('th_TH', NumberFormatter::CURRENCY);
    echo $fmt->formatCurrency(1234.56, 'THB');
    ```

## Testing Strategy

### 1. Unit Testing

ใช้ Pest สำหรับการทำ Unit Test:

```php
// ตัวอย่าง Unit Test ด้วย Pest
it('calculates correct total with tax', function () {
    $calculator = new InvoiceCalculator();
    $items = [
        ['price' => 100, 'quantity' => 2],
        ['price' => 200, 'quantity' => 1]
    ];

    $result = $calculator->calculateWithTax($items, 7);

    expect($result)->toEqual([
        'subtotal' => 400,
        'tax' => 28,
        'total' => 428
    ]);
});
```

### 2. Feature Testing

```php
// ตัวอย่าง Feature Test
it('creates an invoice', function () {
    // Arrange
    $user = User::factory()->create();
    $customer = Customer::factory()->create(['company_id' => $user->company_id]);
    $invoiceData = [
        'customer_id' => $customer->id,
        'issue_date' => '2023-05-01',
        'due_date' => '2023-06-01',
        'items' => [
            ['product_id' => 1, 'quantity' => 2, 'price' => 100]
        ]
    ];

    // Act
    $response = $this->actingAs($user)
                     ->postJson('/api/invoices', $invoiceData);

    // Assert
    $response->assertStatus(201)
             ->assertJsonPath('data.total', 200);

    $this->assertDatabaseHas('invoices', [
        'customer_id' => $customer->id,
        'issue_date' => '2023-05-01',
        'company_id' => $user->company_id
    ]);
});
```

### 3. Browser Testing

```php
// ตัวอย่าง Browser Test ด้วย Laravel Dusk
it('submits invoice form', function () {
    $this->browse(function ($browser) {
        $browser->loginAs(User::find(1))
                ->visit('/invoices/create')
                ->select('customer_id', '1')
                ->type('issue_date', '2023-05-01')
                ->type('due_date', '2023-06-01')
                ->click('.add-item-button')
                ->select('.item-row:first-child .product-select', '1')
                ->type('.item-row:first-child .quantity-input', '2')
                ->press('Save Invoice')
                ->assertPathIs('/invoices')
                ->assertSee('Invoice created successfully');
    });
});
```

## Development & Deployment Workflow

### 1. Development Workflow

1. ใช้ Git Flow Workflow

    - `main` branch สำหรับ production code
    - `develop` branch สำหรับ development
    - Feature branches จาก `develop`
    - Hotfix branches จาก `main`

2. Pull Request Process
    - Code Review โดย team members อย่างน้อย 1 คน
    - Automated tests ต้องผ่านทั้งหมด
    - Static Analysis ต้องไม่มี errors

### 2. CI/CD Pipeline

1. **Continuous Integration**

    - GitHub Actions หรือ GitLab CI/CD
    - Run tests และ static analysis
    - Build assets
    - Generate API documentation

2. **Continuous Deployment**
    - Automated deployment ไปยัง staging environment
    - Manual approval สำหรับ production deployment
    - Zero-downtime deployment

### 3. Environments

1. **Development** - สำหรับการพัฒนา

    - Local environment ด้วย Docker (Laravel Sail)
    - Development database

2. **Staging** - สำหรับการทดสอบ

    - Shared environment ที่เหมือนกับ Production
    - Copy ของ production data (anonymized)

3. **Production** - ระบบจริง
    - High-availability configuration
    - Database backups
    - Monitoring และ alerting

## Technical Requirements เพิ่มเติม

### 1. Browser Support

-   **Chrome** (2 latest versions)
-   **Firefox** (2 latest versions)
-   **Safari** (2 latest versions)
-   **Edge** (2 latest versions)
-   **Mobile Browsers** บน iOS และ Android ล่าสุด

### 2. Responsive Design

-   **Breakpoints**:
    -   Mobile: < 640px
    -   Tablet: 640px - 768px
    -   Laptop: 768px - 1024px
    -   Desktop: 1024px - 1280px
    -   Large Desktop: > 1280px

### 3. Accessibility

-   WCAG 2.1 AA compliance
-   Screen reader compatibility
-   Keyboard navigation
-   Sufficient color contrast

### 4. Performance Benchmarks

-   Page load time < 2 seconds
-   Time to interactive < 3 seconds
-   First contentful paint < 1 second
-   Core Web Vitals:
    -   Largest Contentful Paint (LCP) < 2.5s
    -   First Input Delay (FID) < 100ms
    -   Cumulative Layout Shift (CLS) < 0.1

### 5. API Throttling & Rate Limiting

-   Public API endpoints: 60 requests/minute
-   Authenticated endpoints: 300 requests/minute
-   Admin endpoints: 600 requests/minute

### 6. File Upload Limitations

-   Maximum file size: 10MB
-   Supported formats: PDF, JPG, PNG, XLS, XLSX, DOC, DOCX
-   Virus scanning for all uploads

## Conclusion

Technical Specifications นี้ให้ภาพรวมของการออกแบบและเทคโนโลยีที่ใช้ในระบบ CEOsofts ซึ่งออกแบบให้มีความยืดหยุ่น ปลอดภัย และมีประสิทธิภาพสูง การใช้ Domain-Driven Design ร่วมกับ Laravel Framework จะช่วยให้การพัฒนาและบำรุงรักษาระบบเป็นไปอย่างมีประสิทธิภาพ สามารถรองรับความต้องการทางธุรกิจได้ดี และมีความยืดหยุ่นสำหรับการขยายระบบในอนาคต
