# laravel-api
Use shields for your packagist.org repository that shows how many times your project has been downloaded from packagist.org or its latest stable version.

[![Latest Stable Version](http://poser.pugx.org/hemend/laravel-api/v)](https://packagist.org/packages/hemend/laravel-api)
[![Total Downloads](http://poser.pugx.org/hemend/laravel-api/downloads)](https://packagist.org/packages/hemend/laravel-api)
[![Latest Unstable Version](http://poser.pugx.org/hemend/laravel-api/v/unstable)](https://packagist.org/packages/hemend/laravel-api)
[![License](http://poser.pugx.org/hemend/laravel-api/license)](https://packagist.org/packages/hemend/laravel-api)
<a href="#tada-php-support" title="PHP Versions Supported"><img alt="PHP Versions Supported" src="https://img.shields.io/badge/php->=7.4-777bb3.svg?logoColor=white&labelColor=555555"></a>
<!-- [![PHP Version Require](http://poser.pugx.org/hemend/laravel-api/require/php)](https://packagist.org/packages/hemend/laravel-api) -->

## Requirements
### It is mandatory to delete files whose path is listed below:
```
- app/Models/User.php
- database/migrations/2014_10_12_000000_create_users_table.php
- database/migrations/2014_10_12_100000_create_password_resets_table.php
- database/seeders/DatabaseSeeder.php
```

#### Publish commands
In this section, you need to copy the required files from the package to your local path.
If you execute the following command, you do not need to use commands after that:
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

### Changes in project files
1. Edit `config/auth.php`:
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

2. Empty the contents of the `routes/api.php` file and paste the following codes:
```php
function callApiRoute($route_name) {
    Route::any('/{service}/{version}/{method}', 'Api')->where([
        'service' => '[a-zA-Z]+',
        'version' => '(\d+(?:\.\d+){0,2})',
        'method' => '([a-z][a-zA-Z0-9]+(\.?[a-z][a-zA-Z0-9]+)?)'
    ])->name($route_name);
}

Route::group(['namespace' => 'Hemend\Api\Controllers\\'], function ($router) {
    callApiRoute('Api');

    Route::group(['prefix' => 'demo'], function ($router) {
        callApiRoute('DemoApi');
    });
});
```

## Api commands
#### Keyword meanings
|Keywword        |Meaning                        |Example                                            |
|----------------|-------------------------------|---------------------------------------------------|
|[Name]          |Service name                   |`Admins` or `Users` ...                            |
|[Version]       |Version of service             |`1` or `1.0` ...                                   |
|[Method]        |Method from the server version |`AuthSignIn` or `AccountGetTokens` ...             |
|[SrcVersion]    |Source version of service      |`1` or `1.0` ...                                   |
|[DstVersion]    |Destination version of service |`2` or `2.0` ...                                   |
|[Flag]          |Set the endpoint type          |`private` or `public`                              |

#### Create a service with default endpoints:
```php
php artisan make:api-basic [Name] [Version]
```

#### Create a specific endpoint (It is created if there is no service and version)
```php
php artisan make:api-maker [Name] [Version] [Method] --flag=[Flag]
```

#### Create a specific endpoint (You will get an error if there is no service and version)
```php
php artisan make:api-method [Name] [Version] [Method] --flag=[Flag]
```

#### Create a service:
```php
php artisan make:api-service [Name]
```

#### Create a version for service:
```php
php artisan make:api-version [Name] [Version]
```

#### Copy endpoints from an existing version(source) to a new version(destination):
```php
php artisan make:api-version-copy [Name] [SrcVersion] [DstVersion]
```

## Other settings
1. After installing the package and doing the above, you need to migrate to the database:
```shell
php artisan migrate
php artisan passport:install
```

## License
Licensed under the MIT license, see [LICENSE](LICENSE)