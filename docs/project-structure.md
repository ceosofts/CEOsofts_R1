# CEOsofts R1 Project Structure

This document outlines the Domain-Driven Design (DDD) structure used in the CEOsofts R1 project.

## Overview

The codebase is organized following DDD principles with Laravel-specific adaptations:

```
app/
├── Console/               # Artisan commands
├── Domains/               # Domain-specific code
│   ├── Shared/            # Shared domain components
│   ├── Company/           # Company domain
│   ├── Customer/          # Customer domain
│   ├── Employee/          # Employee domain
│   ├── Product/           # Product domain
│   ├── Sales/             # Sales domain
│   └── Finance/           # Finance domain
├── Http/                  # HTTP layer
│   ├── Controllers/       # API and web controllers
│   ├── Livewire/          # Livewire components
│   └── Middleware/        # HTTP middleware
├── Infrastructure/        # Infrastructure concerns
│   ├── MultiTenancy/      # Multi-tenancy implementation
│   ├── Support/           # Support classes
│   └── Auth/              # Authentication services
├── Providers/             # Service providers
└── Models/                # Eloquent models
```

## Domain Structure

Each domain follows the same internal structure:

```
Domain/
├── Actions/         # Business logic encapsulated in single-purpose classes
├── DTOs/            # Data Transfer Objects
├── Events/          # Domain events
├── Exceptions/      # Domain-specific exceptions
├── Listeners/       # Event listeners
├── Policies/        # Authorization policies
├── Repositories/    # Data access abstraction
├── Rules/           # Validation rules
└── Services/        # Domain services
```

## Livewire Components Structure

Livewire components are organized by feature:

```
Http/Livewire/
├── Company/
│   ├── CompanyForm.php
│   ├── CompanyList.php
│   └── CompanySelector.php
├── Customer/
│   ├── CustomerForm.php
│   └── CustomerList.php
...
```

## Views Structure

View files are organized to match Livewire components:

```
resources/views/
├── components/      # Blade components
├── layouts/         # Layout templates
├── livewire/        # Livewire component views
│   ├── company/
│   ├── customer/
│   ├── employee/
│   └── ...
└── pages/           # Page templates
```

## Development Guidelines

1. **Domain Integrity**: Keep domain logic within the appropriate domain folder.
2. **Single Responsibility**: Each class should have a single responsibility.
3. **Model Usage**: Models should be thin and primarily for database interaction.
4. **Business Logic**: Encapsulate business logic in Action classes.
5. **Controllers**: Keep controllers thin, delegate to actions and services.
6. **Multi-tenancy**: Always use HasCompanyScope for tenant-specific models.

## Service Layer

Services handle complex operations that might span multiple models or domains:

1. **Application Services**: Coordinate tasks across domains
2. **Domain Services**: Complex business logic within a domain
3. **Infrastructure Services**: Handle technical concerns (e.g., file storage)
