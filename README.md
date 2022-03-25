# laravel-api
Use shields for your packagist.org repository that shows how many times your project has been downloaded from packagist.org or its latest stable version.

[![Latest Stable Version](http://poser.pugx.org/hemend/laravel-api/v)](https://packagist.org/packages/hemend/laravel-api)
[![Total Downloads](http://poser.pugx.org/hemend/laravel-api/downloads)](https://packagist.org/packages/hemend/laravel-api)
[![Latest Unstable Version](http://poser.pugx.org/hemend/laravel-api/v/unstable)](https://packagist.org/packages/hemend/laravel-api)
[![License](http://poser.pugx.org/hemend/laravel-api/license)](https://packagist.org/packages/hemend/laravel-api)
<a href="#tada-php-support" title="PHP Versions Supported"><img alt="PHP Versions Supported" src="https://img.shields.io/badge/php->=7.4-777bb3.svg?logoColor=white&labelColor=555555"></a>
<!-- [![PHP Version Require](http://poser.pugx.org/hemend/laravel-api/require/php)](https://packagist.org/packages/hemend/laravel-api) -->

## Requirements

### - It is mandatory to delete files whose path is listed below:
```
- app/Models/User.php
- database/migrations/2014_10_12_000000_create_users_table.php
- database/migrations/2014_10_12_100000_create_password_resets_table.php
- database/seeders/DatabaseSeeder.php
```

#### - Publish commands
In this section, you need to copy the required files from the package to your local path.

**If you execute the following command, you do not need to use commands after that:**
```php
php artisan vendor:publish --provider="Hemend\Api\ApiServiceProvider" --tag=api
```
<details><summary>Copy config</summary>

> php artisan vendor:publish --provider="Hemend\Api\ApiServiceProvider" --tag=config
</details>

<details><summary>Copy migrations</summary>

> php artisan vendor:publish --provider="Hemend\Api\ApiServiceProvider" --tag=migrations
</details>

<details><summary>Copy seeders</summary>

> php artisan vendor:publish --provider="Hemend\Api\ApiServiceProvider" --tag=seeders
</details>

<details><summary>Copy models</summary>

> php artisan vendor:publish --provider="Hemend\Api\ApiServiceProvider" --tag=models
</details>

## Config

Edit `config/auth.php`:
```php
...
    'guards' => [
    ...
        'api' => [
            'driver' => 'passport',
            'provider' => 'users',
        ],
    ...
    ],
    ...
    'providers' => [
    ...
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\Users::class,
        ],
    ...
    ],
```

## Api commands
#### Edit config/auth.php:

## License

Licensed under the MIT license, see [LICENSE](LICENSE)