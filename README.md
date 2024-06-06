# laravel-api
Use shields for your packagist.org repository that shows how many times your project has been downloaded from packagist.org or its latest stable version.

[![Latest Stable Version](http://poser.pugx.org/hemend/laravel-api/v)](https://packagist.org/packages/hemend/laravel-api)
[![Total Downloads](http://poser.pugx.org/hemend/laravel-api/downloads)](https://packagist.org/packages/hemend/laravel-api)
[![Latest Unstable Version](http://poser.pugx.org/hemend/laravel-api/v/unstable)](https://packagist.org/packages/hemend/laravel-api)
[![License](http://poser.pugx.org/hemend/laravel-api/license)](https://packagist.org/packages/hemend/laravel-api)
[![PHP Version Require](http://poser.pugx.org/hemend/laravel-api/require/php)](https://packagist.org/packages/hemend/laravel-api)

[comment]: <> (<a href="#tada-php-support" title="PHP Versions Supported"><img alt="PHP Versions Supported" src="https://img.shields.io/badge/php->=7.4-777bb3.svg?logoColor=white&labelColor=555555"></a>)

## Requirements
### It is mandatory to delete files whose path is listed below:
```
- app/Models/User.php
- database/migrations/2014_10_12_000000_create_users_table.php
- database/migrations/2014_10_12_100000_create_password_reset_tokens_table.php
```

#### Publish commands
In this section, you need to copy the required files from the package to your local path.
If you execute the following command, you do not need to use commands after that:
```php
php artisan vendor:publish --provider="Hemend\Api\ApiServiceProvider" --tag=api
php artisan vendor:publish --provider="Hemend\Library\Laravel\Providers\LibraryServiceProvider" --tag=config
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
1. Edit `public/index.php`:
```php
$app = require_once __DIR__.'/../bootstrap/app.php';

// set the public path to this directory
$app->bind('path.public', function() {
    return __DIR__;
});

$kernel = $app->make(Kernel::class);
```

2. Edit `config/auth.php`:
```php
    ...
    'defaults' => [
        'guard' => 'api',
        'passwords' => 'users',
    ],
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

3. Empty the contents of the `routes/api.php` file and paste the following codes:
```php
function callApiRoute($route_name) {
    Route::any('/{service}/{version}/{package}/{endpoint}', 'Api')->where([
        'service' => '[a-z][a-zA-Z0-9]+',
        'version' => '[1-9][0-9]{0,1}',
        'package' => '[a-z][a-zA-Z0-9]+',
        'endpoint' => '[a-z][a-zA-Z0-9]+'
    ])->name($route_name);
}

Route::group(['namespace' => 'Hemend\Api\Controllers\\'], function ($router) {
    callApiRoute('Api');

//    Route::group(['prefix' => 'demo'], function ($router) {
//        callApiRoute('DemoApi');
//    });
});
```

## Api commands
#### Keyword meanings
|Keyword        |Meaning                        |Example                                            |
|----------------|-------------------------------|---------------------------------------------------|
|[Service]       |Service name                                |`Admin` or `Client` ...                            |
|[Version]       |Version of service                          |`1` or `2` or ... or `99`                                   |
|[Package]       |Package from the server version             |`Auth` or `Account` ...                          |
|[Endpoint]      |Endpoint from the server version            |`SignIn` or `TokensGet` ...                     |
|[Mode]?         |Set the endpoint mode default(`client`)     |`admin` or `client`                              |
|[Guard]?        |Set the service guard default(`api`)        |`admin` or `client`                              |
|[Flag]?         |Set the permission flag default(`private`)  |`private` or `public` or `private_only` or `public_only`                              |

#### Create a service with default endpoints:
```php
php artisan make:api-basic [Service] [Version] --mode=[Mode] --guard=[Guard]
```

#### Create a specific endpoint (It is created if there is no service and version)
```php
php artisan make:api-maker [Service] [Version] [Package] [Endpoint] --flag=[Flag]
```

#### Create a specific endpoint (You will get an error if there is no service and version)
```php
php artisan make:api-endpoint [Service] [Version] [Package] [Endpoint] --flag=[Flag]
```

#### Create a service:
```php
php artisan make:api-service [Service]
```

#### Create a version for service:
```php
php artisan make:api-version [Service] [Version]
```

## Other settings
1. After installing the package and doing the above, you need to publish and migrate to the database:
```shell
php artisan migrate
php artisan passport:install
php artisan db:seed --class=UsersSeeder
```
2. Trackable Job Example(path: app/Jobs/TrackableTest.php):
```shell
<?php

namespace App\Jobs;

use Hemend\Api\Implements\TrackableJob;
use Hemend\Api\Traits\TrackableQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TrackableTest implements ShouldQueue, TrackableJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, TrackableQueue;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->prepareTracker();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $max = mt_rand(5, 30);
        $this->setProgressMax($max);

        for ($i = 0; $i <= $max; $i += 1) {
            sleep(1); // Some Long Operations
            $this->setProgressNow($i);
        }

        $this->setOutput(['total' => $max, 'other' => 'parameter']);
    }
}
```
usage:
```shell
<?php

use App\Jobs\TrackableTest;

TrackableTest::dispatch();
```

## License
Licensed under the MIT license, see [LICENSE](LICENSE)