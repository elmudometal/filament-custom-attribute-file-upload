# Component to add a custom name to image attributes for captions

[![Latest Version on Packagist](https://img.shields.io/packagist/v/elmudo-dev/filament-custom-attribute-file-upload.svg?style=flat-square)](https://packagist.org/packages/elmudo-dev/filament-custom-attribute-file-upload)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/elmudo-dev/filament-custom-attribute-file-upload/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/elmudo-dev/filament-custom-attribute-file-upload/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/elmudo-dev/filament-custom-attribute-file-upload/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/elmudo-dev/filament-custom-attribute-file-upload/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/elmudo-dev/filament-custom-attribute-file-upload.svg?style=flat-square)](https://packagist.org/packages/elmudo-dev/filament-custom-attribute-file-upload)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require elmudo-dev/filament-custom-attribute-file-upload
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-custom-attribute-file-upload-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-custom-attribute-file-upload-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-custom-attribute-file-upload-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$filamentCustomAttributeFileUpload = new ElmudoDev\FilamentCustomAttributeFileUpload();
echo $filamentCustomAttributeFileUpload->echoPhrase('Hello, ElmudoDev!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Hernan Soto](https://github.com/elmudometal)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
