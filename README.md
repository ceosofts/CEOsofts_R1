<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# CEOsofts R1

## Overview

This is a web application built with Laravel and styled with Tailwind CSS. It provides a modern, responsive interface with Thai language support through the Prompt and Sarabun font families.

## Setup

### Prerequisites

-   PHP 8.1 or higher
-   Composer
-   Node.js & NPM
-   MySQL or another database system

### Installation

1. Clone the repository

```bash
git clone [repository-url]
cd CEOsofts_R1
```

2. Install PHP dependencies

```bash
composer install
```

3. Install JavaScript dependencies

```bash
npm install
```

4. Set up environment variables

```bash
cp .env.example .env
php artisan key:generate
```

5. Configure your database in the `.env` file

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ceosoft_r1
DB_USERNAME=root
DB_PASSWORD=
```

6. Run migrations

```bash
php artisan migrate
```

7. Build assets

```bash
npm run dev
```

8. Start the server

```bash
php artisan serve
```

## Key Features

-   Responsive design with Tailwind CSS
-   Thai language support with Prompt and Sarabun fonts
-   Custom UI components (buttons, cards, etc.)
-   Dark/light mode support
-   Reusable Blade components

## Component Usage

### Button Component

```blade
<x-button type="primary">Submit</x-button>
<x-button type="secondary" href="/destination">Go to Destination</x-button>
<x-button type="accent" wire:click="save">Save</x-button>
<x-button type="outline">Cancel</x-button>
<x-button type="danger">Delete</x-button>
```

### Card Component

```blade
<x-card>
    <p>This is a simple card with content.</p>
</x-card>

<x-card>
    <x-slot:header>
        <h2 class="font-semibold text-lg">Card Title</h2>
    </x-slot:header>

    <p>Card content here</p>

    <x-slot:footer>
        <div class="flex justify-end">
            <x-button>Action</x-button>
        </div>
    </x-slot:footer>
</x-card>
```

## Tailwind CSS Utilities

This project includes several custom utility classes:

-   `.btn`, `.btn-primary`, `.btn-secondary`, `.btn-accent` - Button styles
-   `.card` - Card container style
-   `.form-input` - Form input styling

## Support

For support, please contact [support@ceosoft.com](mailto:support@ceosoft.com).

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

-   [Simple, fast routing engine](https://laravel.com/docs/routing).
-   [Powerful dependency injection container](https://laravel.com/docs/container).
-   Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
-   Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
-   Database agnostic [schema migrations](https://laravel.com/docs/migrations).
-   [Robust background job processing](https://laravel.com/docs/queues).
-   [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

-   **[Vehikl](https://vehikl.com/)**
-   **[Tighten Co.](https://tighten.co)**
-   **[WebReinvent](https://webreinvent.com/)**
-   **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
-   **[64 Robots](https://64robots.com)**
-   **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
-   **[Cyber-Duck](https://cyber-duck.co.uk)**
-   **[DevSquad](https://devsquad.com/hire-laravel-developers)**
-   **[Jump24](https://jump24.co.uk)**
-   **[Redberry](https://redberry.international/laravel/)**
-   **[Active Logic](https://activelogic.com)**
-   **[byte5](https://byte5.de)**
-   **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
# IDE Settings
โปรเจคนี้ใช้ Visual Studio Code พร้อมกับส่วนขยาย intelephense สำหรับการพัฒนา PHP
คุณสามารถใช้การตั้งค่าที่แนะนำได้โดยเปิดโปรเจคใน VS Code
