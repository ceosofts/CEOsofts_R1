# Contributing to CEOsofts R1

This document outlines the process and standards for contributing to the CEOsofts R1 project. Please follow these guidelines to ensure a smooth collaboration.

## Development Workflow

1. **Clone the Repository**

    ```bash
    git clone <repository-url>
    cd CEOsofts_R1
    ```

2. **Install Dependencies**

    ```bash
    composer install
    npm install
    cp .env.example .env
    php artisan key:generate
    ```

3. **Set Up Local Database**

    ```bash
    php artisan db:create ceosoft_dev
    php artisan migrate
    php artisan db:seed
    ```

4. **Branch Naming Convention**

    - Feature branches: `feature/short-description`
    - Bug fixes: `fix/short-description`
    - Refactoring: `refactor/short-description`
    - Documentation: `docs/short-description`

5. **Pull Request Process**
    - Create a new branch from `develop`
    - Make your changes
    - Write or update tests as needed
    - Submit a PR to `develop`
    - Ensure CI passes
    - Request review from team members

## Coding Standards

### General Guidelines

1. Follow PSR-12 coding standards
2. Use Laravel conventions and best practices
3. Add comments for complex logic
4. Write self-documenting code with clear naming
5. Implement proper error handling

### Domain-Driven Design Implementation

1. **Models**: Place all Eloquent models in `app/Models`
2. **Domain Logic**: Place in appropriate domain folder under `app/Domains`
3. **Controllers**: Keep thin, use actions for business logic
4. **Services**: Use for complex operations spanning multiple models
5. **Actions**: Single-purpose classes handling one business action

### Database & Migrations

1. **Migration Best Practices**:

    - One table per migration
    - Use `if (!Schema::hasTable())` checks
    - Add foreign key constraints appropriately
    - Use the migration commands to verify syntax:
        ```bash
        php artisan migrate:check-syntax {file}
        ```

2. **Multi-Tenancy**:
    - Remember to add `company_id` to tenant-specific tables
    - Use the `HasCompanyScope` trait for tenant-specific models
    - Test filtering always works with company scope

## Testing Standards

1. **Test Coverage Requirements**:

    - Models: 90%+
    - Actions: 90%+
    - Controllers: 80%+
    - Services: 90%+

2. **Types of Tests**:

    - Unit Tests: For isolated components
    - Feature Tests: For HTTP endpoints
    - Integration Tests: For component interactions
    - Browser Tests: For critical UI flows

3. **Running Tests**:
    ```bash
    php artisan test
    # or
    ./vendor/bin/pest
    ```

## UI/UX Development

1. **Component-Based Approach**:

    - Create reusable Blade components
    - Use Livewire for interactive components
    - Utilize Alpine.js for client-side interactions

2. **CSS Guidelines**:
    - Use Tailwind CSS utility classes
    - Create custom components for repetitive patterns
    - Maintain responsive design principles

## Documentation

1. Document all major components with PHPDoc comments
2. Update the relevant documentation when adding features
3. Include docblocks for methods with complex logic

## Code Review Guidelines

1. **What We Look For**:

    - Code quality and adherence to standards
    - Proper error handling
    - Test coverage
    - Performance implications
    - Security considerations

2. **Review Process**:
    - Address all comments and suggestions
    - Explain complex implementation decisions
    - Request re-review after making changes

## Helpful Commands

Here are some custom commands developed for this project:

```bash
# Database
php artisan db:create {name}             # Create a new database
php artisan db:fix-schema {table?}       # Fix schema issues

# Migrations
php artisan migrate:check-syntax {file?} # Check migration syntax
php artisan migrate:fix-files {file?}    # Fix migration files
php artisan migrate:skip {migration}     # Skip a troublesome migration
```

## Questions or Issues?

If you have any questions or encounter issues, please contact the project maintainers or create an issue in the repository.

Thank you for contributing to CEOsofts R1!
