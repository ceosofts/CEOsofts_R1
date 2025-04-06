# Development Workflow และ Coding Standards

## เครื่องมือและสภาพแวดล้อมการพัฒนา

### Local Development Environment

เราจะใช้ Docker สำหรับสภาพแวดล้อมการพัฒนาเพื่อให้ทุกคนทำงานบนสภาพแวดล้อมเดียวกัน:

1. **Docker Setup**:

    - Laravel Sail (Docker Compose setup สำหรับ Laravel)
    - Services: PHP 8.4, MySQL 8.0, Redis, Mailpit, Minio (S3)
    - Setup สำหรับเชื่อมต่อฐานข้อมูล `ceosofts_db_R1`

2. **IDE ที่แนะนำ**:

    - PhpStorm หรือ VS Code
    - Extensions:
        - Laravel Extension Pack
        - PHP Intelephense
        - Tailwind CSS IntelliSense
        - Alpine.js IntelliSense
        - EditorConfig
        - PHP CS Fixer
        - Better DocBlock (สำหรับ PHPDoc)

3. **Configuration Management**:
    - `.env.example` ที่อัพเดตเสมอ
    - `.env.local` สำหรับพัฒนาบนเครื่อง local โดยตรง
    - `.env` สำหรับรันผ่าน Docker/Sail
    - Secrets จะไม่ถูกเก็บใน Git และจัดการผ่าน environment variables

## โครงสร้างโปรเจค

เราจะใช้แนวทาง DDD (Domain-Driven Design) สำหรับโครงสร้างโปรเจค:

```
app/
├── Domain/             # Domain Layer - business logic และ business rules
│   ├── Organization/   # โครงสร้างองค์กร (บริษัท แผนก ตำแหน่ง)
│   ├── HumanResources/ # ระบบงาน HR (พนักงาน การลางาน เงินเดือน)
│   ├── Sales/          # ระบบขาย (ลูกค้า คำสั่งซื้อ ใบแจ้งหนี้)
│   ├── Finance/        # ระบบการเงิน (รับ-จ่าย บัญชี)
│   ├── Inventory/      # ระบบสินค้า (สินค้า คลัง)
│   └── Shared/         # Shared code ระหว่าง domains
│
├── Application/        # Application Layer - orchestration
│   ├── Commands/       # Command handlers
│   ├── Queries/        # Query handlers
│   └── DTOs/           # Data Transfer Objects
│
├── Infrastructure/     # Infrastructure Layer
│   ├── Repositories/   # Repository implementations
│   └── Services/       # Service implementations
│
└── UI/                 # UI Layer
    ├── Web/            # Web interface
    └── API/            # API interface
```

## Git Workflow

เราจะใช้ Git Flow แบบปรับแต่งสำหรับ Version Control:

### Branch Structure

-   `main` - Production code
-   `develop` - Development code
-   `feature/[feature-name]` - สำหรับฟีเจอร์ใหม่
-   `bugfix/[bug-name]` - สำหรับแก้ไข bug
-   `hotfix/[hotfix-name]` - สำหรับแก้ไขด่วนบน production
-   `release/[version]` - สำหรับเตรียม release

### Commit Standards

-   ใช้ Conventional Commits:
    -   `feat:` สำหรับฟีเจอร์ใหม่
    -   `fix:` สำหรับแก้ไข bug
    -   `refactor:` สำหรับ refactoring code
    -   `style:` สำหรับการแก้ไขเกี่ยวกับ formatting
    -   `docs:` สำหรับการแก้ไข documentation
    -   `test:` สำหรับการเพิ่ม/แก้ไข tests
    -   `chore:` สำหรับการแก้ไขทั่วไป ไม่เกี่ยวกับ code

### Pull Request Process

1. สร้าง branch จาก `develop` (หรือ `main` สำหรับ hotfix)
2. พัฒนาและ push code
3. สร้าง Pull Request (PR) ไปยัง branch ต้นทาง
4. Code Review โดยอย่างน้อย 1 คน
5. CI/CD จะทดสอบโค้ด
6. Merge เมื่อผ่านการ review และ tests ทั้งหมด

## Coding Standards

### PHP Coding Standards

-   ปฏิบัติตาม PSR-12
-   ใช้ PHP CS Fixer สำหรับ auto-formatting
-   ใช้ Type hints สำหรับ parameters และ return types
-   หลีกเลี่ยงการใช้ comments ที่ไม่จำเป็น ให้เขียน self-documenting code
-   ใช้ Laravel conventions:
    -   Controllers ใช้ 7 RESTful actions (index, create, store, show, edit, update, destroy)
    -   Models ควรมี accessors, mutators, relationships, scopes
    -   Use Value Objects สำหรับ complex values

### JavaScript Coding Standards

-   ใช้ ESLint สำหรับ linting
-   ปฏิบัติตาม Airbnb JavaScript Style Guide
-   ใช้ Modern JavaScript (ES6+)
-   สำหรับ Vue components:
    -   1 component per file
    -   ใช้ SFC (Single File Component)
    -   ชื่อ component เป็น PascalCase
    -   Props ต้องมี type และ default value

### CSS/SCSS Coding Standards

-   ใช้ TailwindCSS utility-first approach เป็นหลัก
-   สำหรับ custom CSS:
    -   ปฏิบัติตาม BEM methodology
    -   หลีกเลี่ยง deeply-nested selectors
    -   ใช้ variables สำหรับสี, spacing, fonts

### Database Conventions

-   ชื่อตารางเป็น snake_case และใช้พหูพจน์ (เช่น `order_items`)
-   ชื่อคอลัมน์เป็น snake_case (เช่น `first_name`, `product_id`)
-   Primary keys ใช้ `id`
-   Foreign keys ใช้รูปแบบ `table_name_singular_id` (เช่น `company_id`, `product_id`)
-   ตารางที่เป็น Many-to-Many ใช้ชื่อทั้งสองฝั่งเรียงตามตัวอักษร (เช่น `product_tag`)
-   Index และ Constraints ต้องระบุชื่อชัดเจน

### Multi-tenancy Standards

-   ใช้ `company_id` เป็น foreign key สำหรับตารางที่ต้องแยกข้อมูลตาม tenant
-   ใส่ Global scope ในทุกโมเดลที่ต้องการ multi-tenancy
-   สร้าง composite unique indexes ที่รวม `company_id` เข้าไปด้วย
-   ใช้ middleware เพื่อตรวจสอบ tenant context

### Naming Conventions

-   **PHP Classes**: PascalCase (e.g., `UserController`, `OrderRepository`)
-   **PHP Methods/Functions**: camelCase (e.g., `getUserById()`, `calculateTotal()`)
-   **PHP Variables**: camelCase (e.g., `$orderItems`, `$totalAmount`)
-   **Database Tables**: snake_case, plural (e.g., `users`, `order_items`)
-   **Database Columns**: snake_case (e.g., `first_name`, `created_at`)
-   **CSS Classes**: kebab-case (e.g., `header-container`, `user-profile`)
-   **JavaScript Variables/Functions**: camelCase (e.g., `getUserData()`, `isUserActive`)
-   **JavaScript Constants**: SCREAMING_SNAKE_CASE (e.g., `API_KEY`, `MAX_ATTEMPTS`)
-   **Domain Events**: Past tense PascalCase (e.g., `OrderCreated`, `UserRegistered`)
-   **Commands**: Imperative PascalCase (e.g., `CreateOrder`, `RegisterUser`)
-   **Queries**: สร้างชื่อตามข้อมูลที่ต้องการ (e.g., `GetOrderById`, `ListActiveUsers`)

## Testing Strategy

### Unit Testing

-   ทุก Service class ควรมี unit tests
-   ใช้ Pest หรือ PHPUnit
-   มี convention ในการตั้งชื่อ test: `it_should_[expected_behavior]` หรือ `test_[method_name]_should_[expected_behavior]`
-   ใช้ Mocking อย่างเหมาะสม โดยเฉพาะสำหรับ external services
-   แยก fixtures และ factories ให้ชัดเจน

### Feature Testing

-   ทุก API endpoint และ critical path ควรมี feature tests
-   Testing เฉพาะ public interface ของ modules
-   ใช้ Laravel's HTTP testing helpers

### End-to-End Testing

-   Critical user journeys ควรมี E2E tests ด้วย Cypress
-   มีการ testing อย่างสม่ำเสมอบน staging environment
-   Automate UI testing สำหรับหน้าสำคัญ (login, dashboard, critical forms)

### Testing Coverage

-   ตั้งเป้า code coverage อย่างน้อย 80% สำหรับ business logic
-   Coverage reports จะถูกสร้างและแสดงใน CI pipeline
-   Priority tests coverage:
    1. Domain Services
    2. Application Services
    3. Repositories
    4. Controllers
    5. Helpers/Utilities

### Test Data Management

-   ใช้ Factories สำหรับสร้าง test data
-   Anonymize real production data ในกรณีที่จำเป็นต้องใช้ใน testing
-   เตรียม seeders สำหรับแต่ละสภาพแวดล้อม (development, testing, demo)

## CI/CD Pipeline

### Continuous Integration

GitHub Actions จะทำงานทุกครั้งที่มีการ push หรือ open PR:

1. Install dependencies
2. Run linting
3. Run static analysis (PHPStan)
4. Run unit tests
5. Run feature tests
6. Generate code coverage report

```yaml
# .github/workflows/ci.yml
name: CI

on:
    push:
        branches: [develop, main]
    pull_request:
        branches: [develop, main]

jobs:
    tests:
        runs-on: ubuntu-latest

        services:
            mysql:
                image: mysql:8.0
                env:
                    MYSQL_DATABASE: ceosofts_db_R1_test
                    MYSQL_ROOT_PASSWORD: password
                ports:
                    - 3306:3306
                options: --health-cmd="mysqladmin ping" --health-interval=10s --health-timeout=5s --health-retries=3

        steps:
            - uses: actions/checkout@v3

            - name: Setup PHP
              uses: shivammathur/setup-php@v2
              with:
                  php-version: "8.4"
                  extensions: mbstring, dom, fileinfo, mysql, redis
                  coverage: xdebug

            - name: Copy .env
              run: php -r "file_exists('.env') || copy('.env.example', '.env');"

            - name: Install Composer dependencies
              run: composer install --prefer-dist --no-interaction --no-progress

            - name: Generate key
              run: php artisan key:generate

            - name: Run PHP CS Fixer
              run: ./vendor/bin/php-cs-fixer fix --dry-run --diff

            - name: Run PHPStan
              run: ./vendor/bin/phpstan analyse

            - name: Run tests with Pest
              run: ./vendor/bin/pest --coverage --coverage-clover=coverage.xml

            - name: Upload coverage to Codecov
              uses: codecov/codecov-action@v3
              with:
                  file: ./coverage.xml
```

### Continuous Deployment

การ deploy จะเป็นอัตโนมัติไปยัง environments ต่างๆ:

1. `develop` branch -> Development server
2. `release/[version]` branch -> Staging server
3. `main` branch -> Production server (ต้องผ่านการอนุมัติก่อน)

## Documentation Standards

### Code Documentation

-   ใช้ PHPDoc blocks สำหรับ classes, methods, properties
-   Document complex algorithms และ business rules
-   สร้าง README files สำหรับทุก major component
-   ทำ inline documentation สำหรับ complex logic

#### ตัวอย่าง PHPDoc

```php
/**
 * Calculate the invoice total with applied discounts and taxes.
 *
 * This method applies the following steps:
 * 1. Sum all invoice items
 * 2. Apply invoice-level discount
 * 3. Calculate taxes on the discounted amount
 * 4. Return the final total
 *
 * @param Collection<InvoiceItem> $items The invoice items to calculate
 * @param float $discountPercentage The discount percentage (0-100)
 * @param bool $includeTax Whether to include tax in the calculation
 *
 * @throws InvalidArgumentException When discount percentage is outside valid range
 *
 * @return float The calculated total
 */
public function calculateTotal(Collection $items, float $discountPercentage = 0, bool $includeTax = true): float
{
    // Implementation...
}
```

### API Documentation

-   ใช้ OpenAPI/Swagger สำหรับ API documentation
-   Documentation ควร auto-generated จาก annotations
-   มีตัวอย่างการใช้งานสำหรับทุก endpoint
-   มี Postman collection สำหรับ testing

### Project Documentation

-   อยู่ใน `/Project_Info/` directory
-   Architecture docs (High-level overview)
-   Setup instructions
-   Deployment process
-   Decision logs (สำหรับ architectural decisions)
-   Database schema และ entity relationships

## Code Review Guidelines

### What to Look For

1. **Functionality**: โค้ดทำงานตามที่ออกแบบไว้หรือไม่
2. **Security**: มี security vulnerabilities หรือไม่
3. **Performance**: มีปัญหาด้าน performance หรือไม่
4. **Maintainability**: โค้ดอ่านง่ายและบำรุงรักษาง่ายหรือไม่
5. **Test Coverage**: มี tests เพียงพอหรือไม่
6. **Standards Compliance**: เป็นไปตาม coding standards หรือไม่
7. **Domain Integrity**: แบ่งความรับผิดชอบระหว่าง layers และ domains ถูกต้องหรือไม่

### Code Review Checklist

-   [ ] โค้ดทำงานได้ตาม requirements
-   [ ] มี tests ที่ครอบคลุม edge cases
-   [ ] เป็นไปตาม coding standards
-   [ ] ไม่มี security vulnerabilities
-   [ ] Naming conventions ชัดเจนและเหมาะสม
-   [ ] ไม่มีการทำงานซ้ำซ้อน (DRY principle)
-   [ ] Documentation เพียงพอและทันสมัย
-   [ ] ประสิทธิภาพของ database queries เหมาะสม

### Code Review Etiquette

1. ให้ feedback ที่สร้างสรรค์
2. อธิบายเหตุผลเสมอ
3. แยกความคิดเห็นส่วนตัวออกจากปัญหาจริง
4. ใช้คำถามมากกว่าคำสั่ง
5. รวม feedback เล็กๆ น้อยๆ เป็นหนึ่งความคิดเห็น

## Definition of Done

ทุก feature จะถือว่า "เสร็จสมบูรณ์" เมื่อ:

1. โค้ดพร้อมใช้งานตาม requirements
2. มี unit tests และ feature tests ครบถ้วน
3. ผ่าน code review จากอย่างน้อย 1 คน
4. มี documentation ที่เหมาะสม
5. ทุก CI checks ผ่าน
6. UX/UI ได้รับการตรวจสอบจาก designer
7. ทดสอบบน development environment และทำงานได้ถูกต้อง
8. ไม่มี technical debt เพิ่มขึ้นโดยไม่ได้วางแผน
9. กรณี API - มี API documentation ที่ถูกต้อง
10. กรณีเปลี่ยนแปลงฐานข้อมูล - มี migration scripts และ rollback strategy

## Sprint Workflow

เราจะใช้ Agile methodology ด้วย 2-week sprints:

1. **Sprint Planning**:

    - การวางแผนงานสำหรับ sprint
    - การประมาณเวลาและความซับซ้อน
    - การกำหนด sprint goals

2. **Daily Stand-up**:

    - การประชุมสั้นๆ ทุกวัน (15 นาที)
    - แต่ละคนรายงาน:
        - อะไรที่ทำเสร็จเมื่อวานนี้
        - จะทำอะไรวันนี้
        - มีอุปสรรคอะไรหรือไม่

3. **Sprint Review**:

    - นำเสนอสิ่งที่ทำเสร็จใน sprint
    - รับ feedback จากทีมและ stakeholders
    - ปรับแผนงานสำหรับ product backlog

4. **Sprint Retrospective**:

    - ทบทวนกระบวนการทำงาน
    - หาสิ่งที่ดีและควรทำต่อ
    - หาสิ่งที่ควรปรับปรุง

5. **Backlog Refinement**:
    - ปรับปรุง backlog สำหรับ sprint ถัดไป
    - เพิ่มรายละเอียดให้กับ user stories
    - จัดลำดับความสำคัญของงาน

## การจัดการ Dependencies

-   ใช้ Composer สำหรับ PHP dependencies
-   ใช้ NPM สำหรับ JavaScript dependencies
-   การอัพเดต dependencies จะเป็นส่วนหนึ่งของ regular sprint
-   สร้าง dependabot alerts สำหรับการตรวจจับ vulnerabilities
-   ระบุ version constraints อย่างเหมาะสม (ไม่ใช้ `*`)

## Multi-language Support

-   ใช้ Laravel's Localization system
-   เก็บ translations ในไฟล์ JSON สำหรับแต่ละภาษา
-   Dynamic translations จากฐานข้อมูลสำหรับข้อมูลที่มีการเปลี่ยนแปลงบ่อย
-   ภาษาเริ่มต้น: Thai (th)
-   ภาษาสำรอง: English (en)

## Incident Response Process

1. **รายงาน**: บันทึกปัญหาใน issue tracking system
2. **วิเคราะห์**: ตรวจสอบสาเหตุและผลกระทบ
3. **แก้ไข**: พัฒนาการแก้ไขและ deploy
4. **บันทึก**: เก็บบันทึกเหตุการณ์และวิธีการแก้ไข
5. **ป้องกัน**: กำหนดมาตรการป้องกันไม่ให้เกิดซ้ำ

## Performance Optimization Guidelines

1. **Database Queries**:

    - ใช้ eager loading (`with()`) เพื่อหลีกเลี่ยง N+1 queries
    - สร้าง indexes สำหรับคอลัมน์ที่ใช้ในการค้นหา
    - ใช้ query builder แทน raw SQL เมื่อเป็นไปได้
    - คัดเลือกเฉพาะคอลัมน์ที่จำเป็น (`select()`)

2. **Cache Strategy**:

    - ใช้ Redis สำหรับ cache
    - Cache ข้อมูลที่เปลี่ยนแปลงน้อย
    - ใช้ cache tags เพื่อจัดการ invalidation
    - กำหนด TTL (Time To Live) ที่เหมาะสม

3. **Asset Optimization**:

    - ใช้ Laravel Mix/Vite สำหรับ asset compilation
    - Minify และ bundle JS/CSS files
    - Image optimization
    - Lazy loading สำหรับ images และ heavy components

4. **Monitoring**:
    - ใช้ Laravel Telescope ในสภาพแวดล้อม development
    - เก็บ logs และ metrics สำหรับวิเคราะห์ performance
    - ตั้ง alerts สำหรับ slow queries และ high response times
