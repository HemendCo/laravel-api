# laravel-api
Use shields for your packagist.org repository that shows how many times your project has been downloaded from packagist.org or its latest stable version.

[![Latest Stable Version](http://poser.pugx.org/hemend/laravel-api/v)](https://packagist.org/packages/hemend/laravel-api)
[![Total Downloads](http://poser.pugx.org/hemend/laravel-api/downloads)](https://packagist.org/packages/hemend/laravel-api)
[![Latest Unstable Version](http://poser.pugx.org/hemend/laravel-api/v/unstable)](https://packagist.org/packages/hemend/laravel-api)
[![License](http://poser.pugx.org/hemend/laravel-api/license)](https://packagist.org/packages/hemend/laravel-api)
<a href="#tada-php-support" title="PHP Versions Supported"><img alt="PHP Versions Supported" src="https://img.shields.io/badge/php->=7.4-777bb3.svg?logoColor=white&labelColor=555555"></a>
<!-- [![PHP Version Require](http://poser.pugx.org/hemend/laravel-api/require/php)](https://packagist.org/packages/hemend/laravel-api) -->

## Usage

#### Execute the following commands together to publish:

```shell
php artisan vendor:publish --provider="Hemend\Api\ApiServiceProvider" --tag=api
```

#### Copy the package config to your local config with the publish command:

```shell
php artisan vendor:publish --provider="Hemend\Api\ApiServiceProvider" --tag=config
```

#### Copy the package migrations to your local migrations with the publish command:

```shell
php artisan vendor:publish --provider="Hemend\Api\ApiServiceProvider" --tag=migrations
```

#### Copy the package seeders to your local seeders with the publish command:

```shell
php artisan vendor:publish --provider="Hemend\Api\ApiServiceProvider" --tag=seeders
```

#### Copy the package models to your local models with the publish command:

```shell
php artisan vendor:publish --provider="Hemend\Api\ApiServiceProvider" --tag=models
```

## License

Licensed under the MIT license, see [LICENSE](LICENSE)