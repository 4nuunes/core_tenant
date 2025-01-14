<p align="center">
    <h2 align="center"> 
        FilamentPHP Tenant Based in Organization/Company
    </h2>
</p>

## About this Project

An example project demonstrating a MultiTenant SingleDatabase system fully built in Laravel and Filament, integrated with Stripe for Subscription management. The system includes the following features:

1 - Creation of Plans, Prices, and Features stored in the database and integrated via the Stripe API.
2 - Admin Panel for Managing Tenants and Subscriptions.
3 - API-based Client Creation when registering the Tenant
4 - Modal for Tenant to choose Plans and Complete the Subscription (Stripe Management)
5 - Feature for Tenant to create tickets for the Tenant's manager.
6 - Profile editing with theme color customization.
7 - Integration of Icons with FontAwesome.

## The plugins used in this project may include:

-   [laravel](https://github.com/laravel/framework)
-   [Filament](https://github.com/filamentphp/filament)
-   [FontAwesome](https://v2.filamentphp.com/tricks/use-font-awesome-or-any-other-icon-set)
-   [Brazilian Form Fields](https://filamentphp.com/plugins/leandrocfe-brazilian-form-fields)
-   [Edit Profile](https://filamentphp.com/plugins/joaopaulolndev-edit-profile)
-   [Spatie Laravel Backup](https://filamentphp.com/plugins/shuvroroy-spatie-laravel-backup)

## Prerequisites

1. Create a Stripe account and enable trial mode - [Stripe](https://stripe.com/)

2. Docker and docker-compose (The Dockerfile for this project already includes all the necessary resources to run the project.)

## Dockerfile includes the following functionalities:

1. Base Image: Uses php:8.3-fpm as the base image.

2. User Setup: Defines a user with a specified UID and creates a home directory.

3. System Dependencies: Installs essential packages such as:

    - git,
    - curl,
    - libpng-dev,
    - libzip-dev,
    - and others required for running the project.

4. Node.js & NPM: Adds the Node.js repository and installs Node.js and the latest version of NPM.

5. PHP Extensions, all necessary PHP extensions:

    - pdo_mysql,
    - mbstring,
    - gd,
    - intl,
    - zip,
    - etc.

6. Composer: Copies the latest Composer binary into the container to manage PHP dependencies.

7. Redis: Installs Redis and enables the extension.

8. Stripe CLI: Installs the Stripe CLI for Stripe-related operations (To Listen Webhook stripe events)

9. PHPStan: Installs PHPStan globally for static analysis.

10. Working Directory: Sets the working directory to /var/www.

11. User Switching: Switches to a non-root user to run Composer and Artisan commands securely.

## Installation

1. Clone the repository

```bash

git https://github.com/wallacemartinss/core_tenant.git
cd core_tenant

```

2. Copy .ENV file

```bash

cp .env.example .env

```

3. Configure your database in `.env`:

```

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=billing_system
DB_USERNAME=your_username
DB_PASSWORD=your_password

```

4. Configure Stripe keys in `.env`

```

STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret

```

5. Run Docker

```bash

docker compose up -d

```

6. Access docker App container

```bash

docker compose exec app bash

```

7. Inside the container, configure environment variables

```bash

php artisan key:generate

```

8. Inside the container, Install PHP dependencies

```bash

composer install

```

9. Inside the container, Run migrations and seeders

```bash

php artisan migrate --seed

```

10. Inside the container, Link storage for file uploads

```bash

php artisan storage:link

```

11. Inside the container run the command below (To Listen Webhook stripe events)

```bash

stripe listen -f http://0.0.0.0/stripe/webhook

```

12. The first time you run it, it will generate your webhook key. copy and paste it into your env file.

```
STRIPE_WEBHOOK_SECRET=your_stripe_webhook_secret

```

## Contributing

1. Fork the repository
2. Create your feature changes in your branch
3. Commit your changes
4. Push to the branch
5. Open a Pull Request

## Security

If you discover any security-related issues, please email wallacemartinss@gmail.com instead of using the issue tracker.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

## Credits

-   [Wallace Martins](https://github.com/wallacemartinss)
-   [All Contributors](../../contributors)

## Support

For support, please email wallacemartinss@gmail.com or create an issue in the GitHub repository.

<br>
    <h4 align="center"> 
        🚧  Project 🚀 under construction...  🚧
    </h4>
<br>
