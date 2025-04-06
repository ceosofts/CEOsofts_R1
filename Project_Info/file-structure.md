# โครงสร้างไฟล์สำหรับระบบ CEOsofts แบบใหม่

## โครงสร้างหลัก

```
/Users/iwasbornforthis/MyProject/CEOsofts/
├── app/
│   ├── Domains/                    # โฟลเดอร์หลักสำหรับ Domain-Driven Design
│   │   ├── Organization/           # Domain: โครงสร้างองค์กร
│   │   ├── HumanResources/         # Domain: ทรัพยากรบุคคล
│   │   ├── Sales/                  # Domain: การขายและลูกค้า
│   │   ├── Inventory/              # Domain: สินค้าและคลัง
│   │   ├── Finance/                # Domain: การเงินและบัญชี
│   │   └── Settings/               # Domain: การตั้งค่าระบบ
│   ├── Shared/                     # Shared components ระหว่าง Domains
│   │   ├── Services/               # Shared services
│   │   ├── Traits/                 # Shared traits
│   │   ├── Exceptions/             # Shared exceptions
│   │   └── Enums/                  # Shared enums (PHP 8.1+)
│   ├── Http/                       # Legacy & App-level Controllers
│   │   ├── Controllers/            # Base controllers
│   │   ├── Middleware/             # Middleware
│   │   └── Requests/               # Form requests
│   └── Support/                    # Support utilities
│       ├── Helpers/                # Helper functions
│       └── Facades/                # Custom facades
├── config/                         # Configuration files
├── database/                       # Database migrations and seeds
├── resources/
│   ├── js/                         # JavaScript and frontend files
│   ├── css/                        # CSS and styling files
│   └── views/                      # Blade templates
└── tests/
    ├── Unit/                       # Unit tests (by domain)
    ├── Feature/                    # Feature tests
    └── Browser/                    # Browser tests (Laravel Dusk)
```

## โครงสร้างแต่ละ Domain

แต่ละ Domain จะมีโครงสร้างย่อยเหมือนกัน:

```
app/Domains/Organization/               # ตัวอย่างโดเมน Organization
├── Models/                             # โมเดลเกี่ยวกับโครงสร้างองค์กร
│   ├── Company.php                     # บริษัท
│   ├── Department.php                  # แผนก
│   └── Position.php                    # ตำแหน่งงาน
├── Repositories/                       # Repository pattern สำหรับ data access
│   ├── CompanyRepository.php
│   ├── DepartmentRepository.php
│   └── PositionRepository.php
├── Services/                           # Business logic
│   ├── CompanyService.php
│   ├── DepartmentService.php
│   └── PositionService.php
├── Http/                               # HTTP-related code
│   ├── Controllers/                    # Controllers
│   │   ├── CompanyController.php
│   │   ├── DepartmentController.php
│   │   └── PositionController.php
│   ├── Requests/                       # Form requests
│   │   ├── Company/                    # Grouped by entity
│   │   │   ├── StoreCompanyRequest.php
│   │   │   └── UpdateCompanyRequest.php
│   │   └── Department/
│   │       ├── StoreDepartmentRequest.php
│   │       └── UpdateDepartmentRequest.php
│   └── Resources/                      # API resources
│       ├── CompanyResource.php
│       ├── DepartmentResource.php
│       └── PositionResource.php
├── Policies/                           # Authorization policies
│   ├── CompanyPolicy.php
│   ├── DepartmentPolicy.php
│   └── PositionPolicy.php
├── Events/                             # Domain events
│   ├── CompanyCreated.php
│   └── DepartmentDeleted.php
├── Listeners/                          # Event listeners
│   └── NotifyEmployeesOfDepartmentChange.php
├── Jobs/                               # Queue jobs
│   └── GenerateOrganizationReport.php
└── Exceptions/                         # Domain-specific exceptions
    └── DepartmentHasEmployeesException.php
```

## ตัวอย่างโดเมนอื่นๆ

### โดเมน HumanResources

```
app/Domains/HumanResources/
├── Models/
│   ├── Employee.php
│   ├── Attendance.php
│   ├── WorkShift.php
│   ├── Leave.php
│   └── Payroll.php
├── Repositories/
├── Services/
│   ├── EmployeeService.php
│   ├── AttendanceService.php
│   ├── LeaveService.php
│   └── PayrollService.php
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Resources/
└── ...
```

### โดเมน Sales

```
app/Domains/Sales/
├── Models/
│   ├── Customer.php
│   ├── Order.php
│   ├── Quotation.php
│   └── SalesLead.php
├── Repositories/
├── Services/
│   ├── CustomerService.php
│   ├── OrderService.php
│   └── QuotationService.php
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Resources/
└── ...
```

### โดเมน Finance

```
app/Domains/Finance/
├── Models/
│   ├── Invoice.php
│   ├── Payment.php
│   ├── Tax.php
│   └── Expense.php
├── Repositories/
├── Services/
│   ├── InvoiceService.php
│   ├── PaymentService.php
│   └── ExpenseService.php
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Resources/
└── ...
```

## Shared Components

```
app/Shared/
├── Services/
│   ├── NotificationService.php
│   ├── FileStorageService.php
│   └── PDFGenerationService.php
├── Traits/
│   ├── HasCompanyScope.php     # Multi-tenancy
│   ├── HasTimestamps.php
│   └── HasStatus.php
├── Enums/
│   ├── StatusType.php
│   └── PaymentMethod.php
└── Interfaces/
    ├── RepositoryInterface.php
    └── FileStorageInterface.php
```

## Resources Views

```
resources/views/
├── layouts/                         # Layouts
│   ├── app.blade.php
│   ├── admin.blade.php
│   └── auth.blade.php
├── components/                      # Reusable components
│   ├── action-icons.blade.php
│   ├── data-table.blade.php
│   └── form-group.blade.php
├── organization/                    # Views by domain
│   ├── companies/
│   └── departments/
├── human-resources/
│   ├── employees/
│   └── payrolls/
├── sales/
│   ├── customers/
│   └── orders/
└── finance/
    ├── invoices/
    └── payments/
```

## Tests Structure

```
tests/
├── Unit/
│   ├── Organization/
│   │   ├── Services/
│   │   │   └── DepartmentServiceTest.php
│   │   └── Models/
│   │       └── DepartmentTest.php
│   ├── HumanResources/
│   └── Sales/
├── Feature/
│   ├── Organization/
│   │   └── Http/
│   │       └── Controllers/
│   │           └── DepartmentControllerTest.php
│   └── ...
└── Browser/
    └── ...
```

## ไฟล์ Service Provider

การลงทะเบียน Domain Services:

```
app/Providers/
├── AppServiceProvider.php                 # Laravel default
├── AuthServiceProvider.php                # Laravel default
├── RouteServiceProvider.php               # Laravel default
├── OrganizationServiceProvider.php        # Organization domain services
├── HumanResourcesServiceProvider.php      # HR domain services
├── SalesServiceProvider.php               # Sales domain services
└── FinanceServiceProvider.php             # Finance domain services
```

## การตั้งค่าเส้นทาง (Routes)

```
routes/
├── web.php                          # Main web routes
├── api.php                          # API routes
├── domains/                         # Routes organized by domain
│   ├── organization.php
│   ├── human-resources.php
│   ├── sales.php
│   └── finance.php
└── admin.php                        # Admin-specific routes
```
