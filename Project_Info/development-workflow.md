# Development Workflow และ Coding Standards

## เครื่องมือและสภาพแวดล้อมการพัฒนา

### Local Development Environment

เราจะใช้ Docker สำหรับสภาพแวดล้อมการพัฒนาเพื่อให้ทุกคนทำงานบนสภาพแวดล้อมเดียวกัน:

1. **Docker Setup**:

   - Laravel Sail (Docker Compose setup สำหรับ Laravel)
   - Services: PHP 8.4, MySQL/PostgreSQL, Redis, Mailhog, Minio (S3)

2. **IDE ที่แนะนำ**:

   - PhpStorm หรือ VS Code
   - Extensions:
     - Laravel Extension Pack
     - PHP Intelephense
     - Tailwind CSS IntelliSense
     - Alpine.js IntelliSense
     - EditorConfig
     - PHP CS Fixer

3. **Configuration Management**:
   - `.env.example` ที่อัพเดตเสมอ
   - Secrets จะไม่ถูกเก็บใน Git
   - Configuration สำหรับแต่ละสภาพแวดล้อม (dev, staging, production)

## Git Workflow

เราจะใช้ Git Flow แบบปรับแต่งสำหรับ Version Control:

### Branch Structure

- `main` - Production code
- `develop` - Development code
- `feature/[feature-name]` - สำหรับฟีเจอร์ใหม่
- `bugfix/[bug-name]` - สำหรับแก้ไข bug
- `hotfix/[hotfix-name]` - สำหรับแก้ไขด่วนบน production
- `release/[version]` - สำหรับเตรียม release

### Commit Standards

- ใช้ Conventional Commits:
  - `feat:` สำหรับฟีเจอร์ใหม่
  - `fix:` สำหรับแก้ไข bug
  - `refactor:` สำหรับ refactoring code
  - `style:` สำหรับการแก้ไขเกี่ยวกับ formatting
  - `docs:` สำหรับการแก้ไข documentation
  - `test:` สำหรับการเพิ่ม/แก้ไข tests
  - `chore:` สำหรับการแก้ไขทั่วไป ไม่เกี่ยวกับ code

### Pull Request Process

1. สร้าง branch จาก `develop` (หรือ `main` สำหรับ hotfix)
2. พัฒนาและ push code
3. สร้าง Pull Request (PR) ไปยัง branch ต้นทาง
4. Code Review โดยอย่างน้อย 1 คน
5. CI/CD จะทดสอบโค้ด
6. Merge เมื่อผ่านการ review และ tests ทั้งหมด

## Coding Standards

### PHP Coding Standards

- ปฏิบัติตาม PSR-12
- ใช้ PHP CS Fixer สำหรับ auto-formatting
- ใช้ Type hints สำหรับ parameters และ return types
- หลีกเลี่ยงการใช้ comments ที่ไม่จำเป็น ให้เขียน self-documenting code
- ใช้ Laravel conventions:
  - Controllers ใช้ 7 RESTful actions (index, create, store, show, edit, update, destroy)
  - Models ควรมี accessors, mutators, relationships, scopes
  - Use Value Objects สำหรับ complex values

### JavaScript Coding Standards

- ใช้ ESLint สำหรับ linting
- ปฏิบัติตาม Airbnb JavaScript Style Guide
- ใช้ Modern JavaScript (ES6+)
- สำหรับ Vue components:
  - 1 component per file
  - ใช้ SFC (Single File Component)
  - ชื่อ component เป็น PascalCase
  - Props ต้องมี type และ default value

### CSS/SCSS Coding Standards

- ใช้ TailwindCSS utility-first approach เป็นหลัก
- สำหรับ custom CSS:
  - ปฏิบัติตาม BEM methodology
  - หลีกเลี่ยง deeply-nested selectors
  - ใช้ variables สำหรับสี, spacing, fonts

### Naming Conventions

- **PHP Classes**: PascalCase (e.g., `UserController`, `OrderRepository`)
- **PHP Methods/Functions**: camelCase (e.g., `getUserById()`, `calculateTotal()`)
- **PHP Variables**: camelCase (e.g., `$orderItems`, `$totalAmount`)
- **Database Tables**: snake_case, plural (e.g., `users`, `order_items`)
- **Database Columns**: snake_case (e.g., `first_name`, `created_at`)
- **CSS Classes**: kebab-case (e.g., `header-container`, `user-profile`)
- **JavaScript Variables/Functions**: camelCase (e.g., `getUserData()`, `isUserActive`)
- **JavaScript Constants**: SCREAMING_SNAKE_CASE (e.g., `API_KEY`, `MAX_ATTEMPTS`)

## Testing Strategy

### Unit Testing

- ทุก Service class ควรมี unit tests
- ใช้ Pest หรือ PHPUnit
- มี convention ในการตั้งชื่อ test: `it_should_[expected_behavior]`

### Feature Testing

- ทุก API endpoint และ critical path ควรมี feature tests
- Testing เฉพาะ public interface ของ modules

### End-to-End Testing

- Critical user journeys ควรมี E2E tests ด้วย Cypress
- มีการ testing อย่างสม่ำเสมอบน staging environment

### Testing Coverage

- ตั้งเป้า code coverage อย่างน้อย 80% สำหรับ business logic
- Coverage reports จะถูกสร้างและแสดงใน CI pipeline

## CI/CD Pipeline

### Continuous Integration

GitHub Actions จะทำงานทุกครั้งที่มีการ push หรือ open PR:

1. Install dependencies
2. Run linting
3. Run unit tests
4. Run feature tests
5. Generate code coverage report

### Continuous Deployment

การ deploy จะเป็นอัตโนมัติไปยัง environments ต่างๆ:

1. `develop` branch -> Development server
2. `release/[version]` branch -> Staging server
3. `main` branch -> Production server

## Documentation Standards

### Code Documentation

- ใช้ PHPDoc blocks สำหรับ classes, methods, properties
- Document complex algorithms และ business rules
- สร้าง README files สำหรับทุก major component
- ทำ inline documentation สำหรับ complex logic

### API Documentation

- ใช้ OpenAPI/Swagger สำหรับ API documentation
- Documentation ควร auto-generated จาก annotations
- มีตัวอย่างการใช้งานสำหรับทุก endpoint
- มี Postman collection สำหรับ testing

### Project Documentation

- Architecture docs (High-level overview)
- Setup instructions
- Deployment process
- Troubleshooting guide
- Decision logs (สำหรับ architectural decisions)

## Code Review Guidelines

### What to Look For

1. **Functionality**: โค้ดทำงานตามที่ออกแบบไว้หรือไม่
2. **Security**: มี security vulnerabilities หรือไม่
3. **Performance**: มีปัญหาด้าน performance หรือไม่
4. **Maintainability**: โค้ดอ่านง่ายและบำรุงรักษาง่ายหรือไม่
5. **Test Coverage**: มี tests เพียงพอหรือไม่
6. **Standards Compliance**: เป็นไปตาม coding standards หรือไม่

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

## Sprint Workflow

เราจะใช้ Agile methodology ด้วย 2-week sprints:

1. **Sprint Planning**: การวางแผนงานสำหรับ sprint
2. **Daily Stand-up**: การประชุมสั้นๆ ทุกวัน
3. **Sprint Review**: ทบทวนงานที่ทำเสร็จใน sprint
4. **Sprint Retrospective**: ทบทวนกระบวนการทำงานและหาทางปรับปรุง
5. **Backlog Refinement**: ปรับปรุง backlog สำหรับ sprint ถัดไป

## การจัดการ Dependencies

- ใช้ Composer สำหรับ PHP dependencies
- ใช้ NPM สำหรับ JavaScript dependencies
- การอัพเดต dependencies จะเป็นส่วนหนึ่งของ regular sprint
- สร้าง dependabot alerts สำหรับการตรวจจับ vulnerabilities

## Incident Response Process

1. **รายงาน**: บันทึกปัญหาใน issue tracking system
2. **วิเคราะห์**: ตรวจสอบสาเหตุและผลกระทบ
3. **แก้ไข**: พัฒนาการแก้ไขและ deploy
4. **บันทึก**: เก็บบันทึกเหตุการณ์และวิธีการแก้ไข
5. **ป้องกัน**: กำหนดมาตรการป้องกันไม่ให้เกิดซ้ำ
