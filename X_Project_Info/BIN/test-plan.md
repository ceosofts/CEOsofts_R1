# Test Plan สำหรับ CEOsofts

## หลักการและเป้าหมาย

เป้าหมายของ test plan นี้คือเพื่อให้มั่นใจว่า:

1. ระบบทำงานได้ตรงตามความต้องการทางธุรกิจ
2. ระบบมีความเสถียรและน่าเชื่อถือ
3. ระบบมีความปลอดภัยและป้องกันการใช้งานที่ไม่ถูกต้อง
4. ระบบสามารถรองรับปริมาณการใช้งานตามที่คาดการณ์ไว้
5. ระบบทำงานได้ถูกต้องในสภาพแวดล้อมที่มีหลายบริษัท (Multi-tenancy)

## กลยุทธ์การทดสอบโดยรวม

ใช้แนวทาง **Testing Pyramid** โดยเรียงลำดับความสำคัญจากล่างขึ้นบน:

1. **Unit Testing** - ทดสอบส่วนประกอบย่อยที่สุดของระบบ (80% ของการทดสอบทั้งหมด)
2. **Integration Testing** - ทดสอบการทำงานร่วมกันของส่วนประกอบต่างๆ (15%)
3. **End-to-End Testing** - ทดสอบระบบทั้งหมดจาก UI (5%)

![Testing Pyramid](https://static.packt-cdn.com/products/9781789133646/graphics/B13646_04_01.jpg)

## ประเภทการทดสอบ

### 1. Unit Testing

**เครื่องมือ**: Pest, PHPUnit
**Coverage เป้าหมาย**: 80% ของ business logic

#### ส่วนที่ต้องทดสอบ:

-   Domain Services ทั้งหมด
-   Application Services ทั้งหมด
-   Repository Implementations
-   Domain Entities และ Value Objects
-   Command และ Query Handlers
-   Helper Functions
-   Form Requests

#### วิธีการ:

-   ใช้ Pest เป็นหลัก ด้วยวิธีการแบบ BDD
    ```php
    it('calculates invoice total correctly', function () {
        // arrange
        $calculator = new InvoiceCalculator();
        $items = [ /* test data */ ];

        // act
        $result = $calculator->calculate($items);

        // assert
        expect($result)->toBe(1000.00);
    });
    ```
-   Mocking external services และ dependencies
-   Data Providers สำหรับการทดสอบหลาย edge cases
-   Test Isolation โดยใช้ `$this->refreshDatabase()` และ `fakeables`
-   Assertions ที่เฉพาะเจาะจงและชัดเจน

### 2. Feature Testing

**เครื่องมือ**: Laravel HTTP Tests, Pest
**Coverage เป้าหมาย**: 100% ของ endpoints, 90% ของ user flows

#### ส่วนที่ต้องทดสอบ:

-   API endpoints ทั้งหมด
-   Web Controllers และ routes ทั้งหมด
-   Form submissions และ data validation
-   Authentication และ Authorization scenarios
-   Multi-tenancy scoping
-   Error handling และ response codes

#### วิธีการ:

-   HTTP Request Simulations ด้วย Laravel Testing Helpers
    ```php
    test('user can create a company', function () {
        $user = User::factory()->create();
        $companyData = [ /* test data */ ];

        $response = $this->actingAs($user)
                         ->postJson('/api/companies', $companyData);

        $response->assertStatus(201)
                 ->assertJsonStructure(['data' => ['id', 'name']]);

        $this->assertDatabaseHas('companies', [
            'name' => $companyData['name']
        ]);
    });
    ```
-   Test Database Transactions เพื่อให้แต่ละ test เป็นอิสระต่อกัน
-   Simulating authenticated users ด้วย roles และ permissions ต่างๆ
-   Asserting database state, cache state, และ response content
-   Testing validation errors และ error responses

### 3. Integration Testing

**เครื่องมือ**: PHPUnit, Pest
**Coverage เป้าหมาย**: ทดสอบ integrations ที่สำคัญทั้งหมด

#### ส่วนที่ต้องทดสอบ:

-   การทำงานร่วมกันระหว่าง Domains ต่างๆ
-   Database queries และ transactions ที่ซับซ้อน
-   Cache interactions
-   Queue jobs และ events
-   Third-party services (email, storage, payment gateways)

#### วิธีการ:

-   ใช้ real database connections
-   Mock external services เมื่อจำเป็น
-   Testing event dispatching และ listeners
-   Testing queue jobs และการประมวลผลในแต่ละขั้นตอน

### 4. Browser Testing

**เครื่องมือ**: Laravel Dusk, Cypress
**Coverage เป้าหมาย**: Critical user journeys

#### ส่วนที่ต้องทดสอบ:

-   Critical User Flows:
    -   Login/Registration/Password Reset
    -   Company setup และการสลับระหว่างบริษัท
    -   การสร้างและจัดการลูกค้า
    -   การสร้างและจัดการสินค้า
    -   การสร้างและติดตามคำสั่งซื้อ
    -   การสร้างและจัดการใบแจ้งหนี้
    -   การรับชำระเงิน
    -   การสร้างและจัดการเอกสาร PDF

#### วิธีการ:

-   Headless browser automation
-   Page Object Pattern สำหรับ Browser Tests
-   Screenshots และ video recordings ของการทดสอบที่ล้มเหลว
-   Cross-browser compatibility tests
-   Mobile responsiveness testing

### 5. Performance Testing

**เครื่องมือ**: k6, Apache JMeter, Laravel Telescope
**Benchmarks เป้าหมาย**:

-   Response time < 200ms (median), < 500ms (95th percentile)
-   Throughput >= 100 req/s
-   Load time < 2s

#### ส่วนที่ต้องทดสอบ:

-   API endpoints โดยเน้นที่:
    -   Endpoints ที่มีการเข้าถึงบ่อย (เช่น การดึงข้อมูลสินค้า)
    -   Endpoints ที่มีการประมวลผลซับซ้อน (เช่น การสร้างใบแจ้งหนี้)
-   Database queries ที่ซับซ้อน และการใช้ indexes
-   PDF Generation
-   Report Generation
-   Bulk operations (การนำเข้าข้อมูล, การสร้างเอกสารจำนวนมาก)

#### วิธีการ:

-   Load testing - ทดสอบระบบภายใต้โหลด expected และ peak
-   Stress testing - ทดสอบภายใต้โหลดที่สูงเกินความคาดหมาย
-   Endurance testing - ทดสอบการทำงานต่อเนื่องเป็นเวลานาน
-   Database profiling โดยใช้ EXPLAIN queries
-   Application profiling โดยใช้ Laravel Debugbar และ Telescope

### 6. Security Testing

**เครื่องมือ**: OWASP ZAP, Laravel Security Checker, Burp Suite
**Coverage เป้าหมาย**: ครอบคลุม OWASP Top 10 vulnerabilities

#### ส่วนที่ต้องทดสอบ:

-   Authentication mechanisms
-   Authorization controls โดยเฉพาะในระบบ multi-tenancy
-   CSRF protection
-   XSS vulnerabilities
-   SQL injection
-   File upload vulnerabilities
-   CORS configuration
-   Security headers
-   Session management
-   API rate limiting

#### วิธีการ:

-   Automated vulnerability scanning ด้วย OWASP ZAP
-   Manual penetration testing
-   Dependency scanning เพื่อตรวจหา vulnerabilities
-   Code reviews มุ่งเน้นที่ security
-   Validating multi-tenancy data isolation

## Test Environments

### Development Environment

-   **Purpose**: Unit testing, feature testing สำหรับ developers
-   **Data**: Test fixtures และ Factories, in-memory SQLite
-   **Access**: Development team only

### Test Environment

-   **Purpose**: Integration testing, browser testing, manual QA
-   **Data**: Anonymized copy ของข้อมูลจริงขนาดเล็ก, test data
-   **Access**: Development และ QA teams

### Staging Environment

-   **Purpose**: Performance testing, acceptance testing, security testing
-   **Data**: Anonymized copy ของข้อมูลจริง + test data
-   **Access**: Development, QA, และ stakeholders
-   **Configuration**: ใกล้เคียงกับ Production มากที่สุด

### Production Environment

-   **Purpose**: Smoke tests หลัง deployment
-   **Data**: Live data
-   **Access**: Limited access สำหรับ monitoring และ smoke testing

## Test Data Management

### Test Data Generation

การจัดการข้อมูลสำหรับการทดสอบ:

-   **Laravel Factories** สำหรับการสร้างข้อมูลทดสอบ
    ```php
    // Company Factory ตัวอย่าง
    class CompanyFactory extends Factory {
        public function definition() {
            return [
                'name' => $this->faker->company,
                'address' => $this->faker->address,
                'phone' => $this->faker->phoneNumber,
                'email' => $this->faker->companyEmail,
                'tax_id' => $this->faker->numerify('###########'),
                'is_active' => true,
                // etc.
            ];
        }
    }
    ```
-   **Database Seeders** สำหรับข้อมูลพื้นฐานที่จำเป็น
-   **Custom Data Generators** สำหรับข้อมูลซับซ้อน (เช่น invoice chains)
-   **Faker Library** โดยใช้ `th_TH` locale สำหรับข้อมูลภาษาไทย

### Data Relationships

-   Test data ต้องมีความสัมพันธ์ที่สมบูรณ์
    ```php
    // การสร้าง Invoice พร้อมรายการสินค้า
    public function test_invoice_creation() {
        $company = Company::factory()->create();
        $customer = Customer::factory()
            ->for($company)
            ->create();
        $products = Product::factory()
            ->for($company)
            ->count(3)
            ->create();

        $invoice = Invoice::factory()
            ->for($company)
            ->for($customer)
            ->has(
                InvoiceItem::factory()
                    ->count(3)
                    ->sequence(fn ($sequence) => [
                        'product_id' => $products[$sequence->index]->id,
                        'quantity' => rand(1, 5),
                        'price' => $products[$sequence->index]->price
                    ])
            )
            ->create();

        // assertions...
    }
    ```

### Production Data

-   **Anonymization** ของข้อมูลส่วนบุคคลจากข้อมูลจริง
-   **Subset Creation** ของข้อมูลจริงเพื่อใช้ในการทดสอบ
-   **Data Cleaning** เพื่อลบข้อมูลที่ไม่จำเป็นหรือเป็นความลับ

## Test Automation Strategy

### CI/CD Integration

-   **GitHub Actions** ทำงานเมื่อมี:
    -   Push to any branch
    -   Pull Request to main/develop branches
    -   Scheduled runs (เช่น nightly tests)

```yaml
# CI Workflow ตัวอย่าง
name: Test Suite

on:
    push:
        branches: [main, develop, feature/**, bugfix/**]
    pull_request:
        branches: [main, develop]

jobs:
    tests:
        runs-on: ubuntu-latest

        services:
            mysql:
                image: mysql:8.0
                env:
                    MYSQL_DATABASE: testing
                    MYSQL_ROOT_PASSWORD: password
                ports:
                    - 3306:3306

        steps:
            - uses: actions/checkout@v3

            - name: Setup PHP
              uses: shivammathur/setup-php@v2
              with:
                  php-version: "8.4"
                  extensions: mbstring, dom, fileinfo, mysql
                  coverage: xdebug

            - name: Install Dependencies
              run: composer install --no-interaction --prefer-dist

            - name: Run Static Analysis
              run: ./vendor/bin/phpstan analyse

            - name: Run Unit Tests
              run: ./vendor/bin/pest --testsuite=Unit

            - name: Run Feature Tests
              run: ./vendor/bin/pest --testsuite=Feature

            - name: Upload Coverage
              uses: codecov/codecov-action@v3
              with:
                  token: ${{ secrets.CODECOV_TOKEN }}
```

### Test Reporting

-   **Coverage Reports** ใน CI
-   **Test Results** แสดงใน PR comments
-   **Test Failures** แจ้งเตือนไปยัง Slack/Discord
-   **Dashboard** สำหรับ project-wide test metrics และ trends

## Testing Schedule

### Daily Testing

-   **Automated Tests** สำหรับ Unit และ Feature tests ทุกครั้งที่มีการ commit
-   **Smoke Tests** บน development environment

### Weekly Testing

-   **Full Regression Test Suite** รวมถึง End-to-End tests
-   **Browser Compatibility Testing**
-   **Security Scans**
-   **Performance Benchmarks**

### Pre-Release Testing

-   **User Acceptance Testing** (UAT)
-   **Full End-to-End Testing**
-   **Load Testing**
-   **Data Migration Testing** (เมื่อมีการ migrate จากระบบเก่า)
-   **Deployment Testing** บน staging environment

## Bug Tracking and Resolution

### Bug Priority Levels

1. **Critical**: ระบบหยุดทำงาน หรือ data loss

    - ต้องแก้ไขทันที แม้จะเป็นนอกเวลาทำงาน
    - Hotfix แบบด่วนไปยัง production

2. **High**: ส่งผลกระทบต่อการใช้งานหลักของระบบ

    - ต้องแก้ใน sprint ปัจจุบัน
    - อาจจำเป็นต้อง hotfix ถ้าอยู่ใน critical path

3. **Medium**: ส่งผลกระทบต่อการใช้งาน แต่มี workaround

    - จัดลำดับความสำคัญใน sprint ถัดไป
    - อาจถูกเลื่อนหากมีปัญหาที่สำคัญกว่า

4. **Low**: ปัญหาเล็กน้อย เช่น UI glitches, typos
    - จัดการเมื่อมีเวลา
    - อาจรวมกับการปรับปรุงอื่นๆ

### Bug Report Template

```
## Bug Report

**หัวเรื่อง**: [อธิบายปัญหาสั้นๆ]

**ความรุนแรง**: [Critical/High/Medium/Low]

**ขั้นตอนการทำซ้ำ**:
1. ลงชื่อเข้าใช้ด้วยบัญชี [username]
2. ไปที่หน้า [page]
3. [ขั้นตอนเพิ่มเติม]

**ผลที่คาดหวัง**:
[อธิบายว่าควรเกิดอะไรขึ้น]

**ผลที่เกิดขึ้นจริง**:
[อธิบายสิ่งที่เกิดขึ้นจริง]

**สภาพแวดล้อม**:
- Browser: [browser + version]
- OS: [operating system]
- Screen resolution: [if relevant]

**Evidence**:
[screenshots, console logs, etc.]
```

### Bug Resolution Workflow

1. **Report** - ได้รับรายงานข้อบกพร่อง
2. **Triage** - ยืนยันและกำหนดความรุนแรง
3. **Assign** - มอบหมายให้นักพัฒนา
4. **Fix** - นักพัฒนาแก้ไขปัญหา
5. **Review** - Code review การแก้ไข
6. **Test** - QA ทดสอบการแก้ไข
7. **Close** - ปิดรายงานข้อบกพร่องหลังการแก้ไข
8. **Document** - บันทึกการแก้ไข และความรู้ที่ได้รับ

## Test Roles and Responsibilities

### Developers

-   เขียนและรันเฟิร์ม Unit Tests สำหรับทุก feature ที่พัฒนา
-   เขียน Feature Tests สำหรับ endpoints/controllers
-   แก้ไขปัญหาที่พบใน Code Reviews และ Automated Tests
-   Peer review code และ tests ของทีม

### QA Engineers

-   ออกแบบและจัดการ Test Cases/Test Plans
-   จัดการ End-to-End Tests
-   ทำ Manual Testing รวมถึง Exploratory Testing
-   ตรวจสอบ bug fixes

### DevOps Engineers

-   จัดการและบำรุงรักษาสภาพแวดล้อมการทดสอบ
-   ตั้งค่าและดูแล CI/CD pipelines
-   Monitor test metrics และ reporting
-   จัดการ test data และ environments

### Product Owner

-   กำหนด Acceptance Criteria สำหรับ features
-   ทำ User Acceptance Testing
-   จัดลำดับความสำคัญของข้อบกพร่อง
-   Sign off บน releases

## Risks and Mitigation

### Identified Testing Risks

1. **Multi-tenancy complexity**:

    - **Risk**: ข้อมูลรั่วไหลระหว่าง tenants
    - **Mitigation**: ทดสอบ data isolation อย่างละเอียด, ใช้ global scopes, automate testing ของ permissions

2. **Performance degradation with scale**:

    - **Risk**: ระบบช้าลงเมื่อมีผู้ใช้หรือข้อมูลมาก
    - **Mitigation**: load testing with production-scale data, performance profiling, regular benchmarking

3. **Browser compatibility issues**:

    - **Risk**: UI ทำงานไม่ถูกต้องในบางเบราว์เซอร์
    - **Mitigation**: cross-browser testing, responsive testing, UI component library

4. **PDF generation complications**:
    - **Risk**: เอกสารแสดงผลไม่ถูกต้องหรือช้า
    - **Mitigation**: comprehensive PDF testing across templates, performance optimization, fallback rendering options

## Acceptance Criteria for Testing

Project จะถือว่าพร้อมสำหรับ release เมื่อ:

1. ผ่านเกณฑ์ Unit test coverage (80% สำหรับ business logic)
2. Feature tests สำหรับ endpoints ทั้งหมดผ่าน 100%
3. End-to-End tests สำหรับ critical paths ทั้งหมดผ่าน
4. ไม่มี Critical หรือ High bugs ที่ยังไม่ได้แก้ไข
5. Security scan ไม่พบ vulnerabilities ระดับ high
6. Performance metrics เป็นไปตามเป้าหมาย
7. UAT ผ่านการตรวจสอบจาก stakeholders
8. Cross-browser compatibility ได้รับการยืนยัน

## Continuous Improvement

-   จัดทำ Retrospectives หลังจาก major releases
-   วิเคราะห์ bugs ที่ไปถึง production เพื่อปรับปรุงกระบวนการ
-   ติดตาม test coverage และ metrics อย่างต่อเนื่อง
-   ปรับปรุง test suites ตาม feature ใหม่และการเปลี่ยนแปลง
-   training ทีมเกี่ยวกับเทคนิคการทดสอบใหม่ๆ

## ภาคผนวก

### Test Case ตัวอย่าง

#### Unit Test ตัวอย่างสำหรับ Invoice Calculation:

```php
it('calculates invoice total with tax and discount', function () {
    // Arrange
    $calculator = new InvoiceCalculator();
    $items = [
        ['price' => 1000, 'quantity' => 1, 'discount' => 0, 'tax_rate' => 7],
        ['price' => 500, 'quantity' => 2, 'discount' => 10, 'tax_rate' => 7],
    ];
    $invoiceDiscount = 5; // 5%

    // Act
    $result = $calculator->calculateInvoice($items, $invoiceDiscount);

    // Assert
    expect($result->subtotal)->toBe(1900.00);
    expect($result->discount)->toBe(95.00); // 5% of 1900
    expect($result->taxableAmount)->toBe(1805.00);
    expect($result->taxAmount)->toBe(126.35); // 7% of 1805
    expect($result->total)->toBe(1931.35);
});
```

#### Feature Test ตัวอย่างสำหรับ API Endpoint:

```php
test('it returns correct products for company', function () {
    // Arrange
    $company1 = Company::factory()->create();
    $company2 = Company::factory()->create();

    $user = User::factory()->create();
    $user->companies()->attach($company1->id);

    $productsCompany1 = Product::factory()
        ->count(3)
        ->for($company1)
        ->create();

    $productsCompany2 = Product::factory()
        ->count(2)
        ->for($company2)
        ->create();

    // Act
    $response = $this->actingAs($user)
                     ->withHeaders(['X-Company-Id' => $company1->id])
                     ->getJson('/api/products');

    // Assert
    $response->assertOk()
             ->assertJsonCount(3, 'data')
             ->assertJsonPath('data.0.id', $productsCompany1[0]->id);

    // Ensure company2's products are not returned
    $response->assertJsonMissing(['id' => $productsCompany2[0]->id]);
});
```

### Test Suite Organization

```
tests/
├── Unit/
│   ├── Domain/
│   │   ├── Organization/
│   │   ├── HumanResources/
│   │   ├── Sales/
│   │   └── ...
│   └── Application/
│       ├── Commands/
│       ├── Queries/
│       └── ...
├── Feature/
│   ├── API/
│   │   ├── Organization/
│   │   ├── HumanResources/
│   │   ├── Sales/
│   │   └── ...
│   └── Web/
│       ├── Organization/
│       ├── HumanResources/
│       └── ...
├── Browser/
│   ├── UserFlows/
│   ├── Components/
│   └── Pages/
└── Integration/
    ├── Database/
    ├── Cache/
    └── ThirdParty/
```

### Test Double Patterns

-   **Stubs** - เพื่อให้ค่าตอบกลับที่กำหนด
-   **Mocks** - เพื่อตรวจสอบว่ามีการเรียกเมธอดที่คาดหวัง
-   **Fakes** - สำหรับสิ่งที่ต้องการพฤติกรรมเหมือนจริงแต่ไม่ต้องการใช้ของจริง (เช่น in-memory database)
-   **Spies** - เพื่อตรวจสอบว่ามีการเรียกเมธอดหรือไม่
