# โครงสร้างไฟล์สำหรับ CEOsofts_R1 (DDD Architecture)

## แนวคิดหลักในการออกแบบโครงสร้าง

CEOsofts_R1 ได้รับการออกแบบตามหลักการของ Domain-Driven Design (DDD) ซึ่งแบ่งแยกโค้ดออกเป็นชั้น (layers) ตามความรับผิดชอบที่แตกต่างกัน:

1. **Domain Layer** - เป็นหัวใจของระบบ ประกอบด้วย business logic และ business rules
2. **Application Layer** - ทำหน้าที่ประสานงานระหว่าง Domain และชั้นอื่นๆ
3. **Infrastructure Layer** - จัดการรายละเอียดทางเทคนิค เช่น การเชื่อมต่อกับฐานข้อมูล
4. **UI Layer** - ส่วนติดต่อกับผู้ใช้งาน

การแบ่งโค้ดในลักษณะนี้ช่วยให้ระบบ:

-   มีความชัดเจนในความรับผิดชอบของแต่ละส่วน
-   ทำการทดสอบได้ง่าย
-   มีความยืดหยุ่นต่อการเปลี่ยนแปลงในอนาคต
-   ลดการพึ่งพาระหว่างองค์ประกอบต่างๆ

## โครงสร้างโฟลเดอร์หลัก

```
CEOsofts_R1/                      # โปรเจคหลัก
├── app/                          # โค้ดหลักของแอพพลิเคชั่น
│   ├── Domain/                   # Domain Layer - ประกอบด้วย business logic และ business rules
│   │   ├── Organization/         # Organization Domain (Companies, Departments, etc.)
│   │   ├── HumanResources/       # HR Domain (Employees, Workshift, Leaves, etc.)
│   │   ├── Sales/                # Sales Domain (Customers, Orders, Invoices, etc.)
│   │   ├── Finance/              # Finance Domain (Payments, Accounts, etc.)
│   │   ├── Inventory/            # Inventory Domain (Products, Stock, etc.)
│   │   ├── Settings/             # Settings Domain (Users, Roles, Permissions, etc.)
│   │   ├── DocumentGeneration/   # Document Generation Domain (PDF Documents, Templates, etc.)
│   │   └── Shared/               # Shared elements ระหว่าง domains
│   │
│   ├── Application/              # Application Layer - ประสานงานระหว่าง Domain และชั้นอื่นๆ
│   │   ├── Commands/             # Command handlers
│   │   ├── Queries/              # Query handlers
│   │   ├── DTOs/                 # Data Transfer Objects
│   │   ├── Events/               # Application events
│   │   ├── Interfaces/           # Application interfaces
│   │   └── Services/             # Application services
│   │
│   ├── Infrastructure/           # Infrastructure Layer - รายละเอียดทางเทคนิค
│   │   ├── Repositories/         # Repository implementations
│   │   ├── Services/             # Service implementations
│   │   ├── Persistence/          # Database-specific code
│   │   ├── External/             # External service integration
│   │   └── Support/              # Support utilities
│   │
│   ├── UI/                       # User Interface Layer
│   │   ├── Web/                  # Web interface (Controllers, Livewire/Inertia)
│   │   ├── API/                  # API interface
│   │   └── Console/              # CLI interface
│   │
│   ├── Http/                     # Laravel HTTP components
│   │   ├── Controllers/          # Controllers (legacy, แนะนำให้ใช้ app/UI/Web/Controllers แทน)
│   │   ├── Middleware/           # HTTP middleware
│   │   └── Requests/             # Form requests
│   │
│   └── Providers/                # Service providers
│       ├── AppServiceProvider.php
│       ├── DomainServiceProvider.php
│       └── ...
│
├── bootstrap/                    # Laravel bootstrap files
├── config/                       # Configuration files
├── database/                     # Database-related files
│   ├── migrations/               # Database migrations
│   ├── factories/                # Model factories
│   └── seeders/                  # Database seeders
│
├── docker/                       # Docker-related files
│   └── mysql/                    # MySQL configuration and init scripts
│       └── create-database.sql   # Database initialization script
│
├── public/                       # Public assets
├── resources/                    # Frontend resources
│   ├── js/                       # JavaScript files
│   │   ├── Components/           # Vue/React components organized by domain
│   │   └── Pages/                # Page components organized by domain
│   │
│   ├── views/                    # Blade templates organized by domain
│   │   ├── organizations/        # Organization-related views
│   │   ├── human-resources/      # HR-related views
│   │   ├── sales/                # Sales-related views
│   │   └── ...
│   │
│   ├── css/                      # CSS/SCSS files
│   └── lang/                     # Language files
│       ├── th/                   # Thai language (default)
│       └── en/                   # English language
│
├── routes/                       # Route definitions
│   ├── web.php                   # Web routes
│   ├── api.php                   # API routes
│   └── domains/                  # Domain-specific routes
│       ├── organization.php      # Organization domain routes
│       ├── human-resources.php   # HR domain routes
│       └── ...
│
├── storage/                      # Laravel storage
├── tests/                        # Test files
│   ├── Feature/                  # Feature tests organized by domain
│   │   ├── Organization/         # Organization domain tests
│   │   ├── HumanResources/       # HR domain tests
│   │   └── ...
│   │
│   ├── Unit/                     # Unit tests organized by domain
│   │   ├── Domain/               # Domain layer tests
│   │   ├── Application/          # Application layer tests
│   │   └── ...
│   │
│   └── Integrations/             # Integration tests
│
├── vendor/                       # Composer dependencies
├── node_modules/                 # NPM dependencies
├── Project_Info/                 # Project documentation and information
│   ├── database-design.md        # Database schema and design documentation
│   ├── development-workflow.md   # Development workflow guidelines
│   └── ...
│
├── .env                          # Environment variables for Docker
├── .env.local                    # Environment variables for local development
├── docker-compose.yml           # Docker Compose configuration
├── composer.json                # PHP dependencies
└── package.json                 # JavaScript dependencies
```

## โครงสร้างแต่ละ Domain

แต่ละโฟลเดอร์ใน `app/Domain/` จะมีโครงสร้างเหมือนกัน เพื่อความสม่ำเสมอและง่ายต่อการเข้าใจ:

```
Domain/
├── Organization/
│   ├── Models/                    # Eloquent models
│   ├── ValueObjects/              # Value Objects สำหรับข้อมูลที่มีความหมายในตัวเอง
│   ├── Events/                    # Domain Events
│   ├── Exceptions/                # Domain-specific Exceptions
│   ├── Repositories/              # Repository Interfaces (contracts)
│   │   └── Contracts/             # Repository Interfaces
│   ├── Services/                  # Domain Services
│   │   ├── Contracts/             # Service Interfaces
│   │   └── Implementations/       # Service Implementations
│   ├── DTOs/                      # Data Transfer Objects
│   ├── Factories/                 # Domain object factories
│   ├── Policies/                  # Authorization policies
│   └── Enums/                     # Enumerations
```

## โครงสร้าง Application Layer

Application Layer ทำหน้าที่เป็นตัวกลางระหว่าง Domain Layer และ UI Layer:

```
Application/
├── Commands/                      # Command handlers
│   ├── Organization/              # Organization domain commands
│   │   ├── CreateCompanyCommand.php
│   │   ├── UpdateCompanyCommand.php
│   │   └── ...
│   └── ...
│
├── Queries/                       # Query handlers
│   ├── Organization/              # Organization domain queries
│   │   ├── GetCompanyByIdQuery.php
│   │   ├── ListCompaniesQuery.php
│   │   └── ...
│   └── ...
│
├── DTOs/                          # Data Transfer Objects
│   ├── Organization/
│   │   ├── CompanyDTO.php
│   │   └── ...
│   └── ...
│
├── Events/                        # Application Events
│   ├── UserRegisteredEvent.php
│   └── ...
│
└── Services/                      # Application Services
    ├── OrganizationService.php
    ├── HumanResourcesService.php
    └── ...
```

## โครงสร้าง Infrastructure Layer

Infrastructure Layer จัดการกับรายละเอียดทางเทคนิคและการเชื่อมต่อกับระบบภายนอก:

```
Infrastructure/
├── Repositories/                  # Repository Implementations
│   ├── Organization/
│   │   ├── EloquentCompanyRepository.php
│   │   └── ...
│   └── ...
│
├── Services/                      # Service Implementations
│   ├── Pdf/
│   │   ├── DomPdfAdapter.php
│   │   └── ...
│   ├── Email/
│   │   ├── MailServiceAdapter.php
│   │   └── ...
│   └── ...
│
├── Persistence/                   # Database-specific code
│   ├── Eloquent/
│   │   ├── EloquentBaseRepository.php
│   │   └── ...
│   └── ...
│
├── External/                      # External service integration
│   ├── Payment/
│   │   ├── PaymentGatewayAdapter.php
│   │   └── ...
│   ├── Sms/
│   │   └── ...
│   └── ...
│
└── Support/                       # Support utilities
    ├── Traits/
    │   ├── HasCompanyScope.php     # Trait สำหรับ multi-tenancy
    │   └── ...
    └── Helpers/
        ├── DateHelper.php
        └── ...
```

## โครงสร้าง UI Layer

UI Layer แบ่งออกเป็น 3 ส่วนหลัก:

### 1. Web UI (สำหรับ web browser)

```
UI/
├── Web/
│   ├── Controllers/               # Web controllers
│   │   ├── Organization/
│   │   │   ├── CompanyController.php
│   │   │   └── ...
│   │   └── ...
│   │
│   ├── Livewire/                  # Livewire components
│   │   ├── Organization/
│   │   │   ├── CompanyForm.php
│   │   │   ├── CompanyList.php
│   │   │   └── ...
│   │   └── ...
│   │
│   ├── ViewModels/                # View models for Blade templates
│   │   ├── Organization/
│   │   │   ├── CompanyViewModel.php
│   │   │   └── ...
│   │   └── ...
│   │
│   └── Middleware/                # Web-specific middleware
│       ├── SetupCompanyContext.php
│       └── ...
```

### 2. API

```
UI/
├── API/
│   ├── Controllers/               # API controllers
│   │   ├── V1/                    # API version 1
│   │   │   ├── Organization/
│   │   │   │   ├── CompanyController.php
│   │   │   │   └── ...
│   │   │   └── ...
│   │   └── ...
│   │
│   ├── Resources/                 # API resources
│   │   ├── Organization/
│   │   │   ├── CompanyResource.php
│   │   │   └── ...
│   │   └── ...
│   │
│   ├── Requests/                  # API request validators
│   │   ├── Organization/
│   │   │   ├── CreateCompanyRequest.php
│   │   │   └── ...
│   │   └── ...
│   │
│   └── Middleware/                # API-specific middleware
│       ├── ApiAuthentication.php
│       └── ...
```

### 3. Console

```
UI/
└── Console/
    └── Commands/                  # Console commands
        ├── Organization/
        │   ├── ImportCompanyDataCommand.php
        │   └── ...
        └── ...
```

## การนำไปใช้

ในการพัฒนาระบบ CEOsofts_R1 ตามโครงสร้างนี้ ให้ดำเนินการตามลำดับดังนี้:

1. **กำหนดขอบเขตของแต่ละ Domain** - เริ่มต้นด้วยการแบ่งระบบออกเป็นโดเมนต่างๆ ตามความรับผิดชอบทางธุรกิจ

2. **สร้าง Domain Model** - ออกแบบและสร้าง entities, value objects, repositories, และ domain services

3. **สร้าง Application Services** - สร้าง application services, commands และ queries ที่ใช้ domain models

4. **พัฒนา Infrastructure Layer** - สร้าง repository implementations และ service adapters

5. **พัฒนา UI Layer** - สร้าง controllers, views, และ API endpoints ที่ใช้ application services

## คำแนะนำเพิ่มเติม

-   **Domain Layer ควรเป็นอิสระ** - Domain Layer ไม่ควรพึ่งพาชั้นอื่นๆ
-   **Application Layer เป็นตัวกลาง** - Application Layer เป็นตัวกลางระหว่าง Domain และ Infrastructure/UI

-   **การจัดการ Dependencies** - ใช้ Dependency Injection เพื่อลดการเชื่อมต่อโดยตรงระหว่างชั้นต่างๆ

-   **Service Providers** - ลงทะเบียน interfaces และ implementations ใน service providers

-   **Global Scopes สำหรับ Multi-tenancy** - ใช้ global scopes ใน Eloquent models เพื่อแยกข้อมูลตาม company_id

-   **Authorization** - ใช้ Policies สำหรับการตรวจสอบสิทธิ์ในการเข้าถึงข้อมูล

-   **Validations** - ใช้ Form Requests สำหรับการตรวจสอบข้อมูลที่ถูกส่งมา

## ตัวอย่างการทำงานของระบบ

### 1. การสร้าง Company ใหม่

```
UI/Web/Controllers/Organization/CompanyController
  ↓ (calls)
Application/Commands/Organization/CreateCompanyCommand
  ↓ (calls)
Domain/Organization/Services/CompanyService
  ↓ (calls)
Domain/Organization/Repositories/CompanyRepository
  ↓ (implemented by)
Infrastructure/Repositories/Organization/EloquentCompanyRepository
  ↓ (uses)
Domain/Organization/Models/Company
```

### 2. การดึงข้อมูล Companies

```
UI/API/Controllers/V1/Organization/CompanyController
  ↓ (calls)
Application/Queries/Organization/ListCompaniesQuery
  ↓ (calls)
Domain/Organization/Repositories/CompanyRepository
  ↓ (implemented by)
Infrastructure/Repositories/Organization/EloquentCompanyRepository
  ↓ (uses)
Domain/Organization/Models/Company
  ↓ (returns to)
UI/API/Resources/Organization/CompanyResource
```
