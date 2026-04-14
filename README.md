# Component to add a custom name to image attributes for captions

[![Latest Version on Packagist](https://img.shields.io/packagist/v/elmudo-dev/filament-custom-attribute-file-upload.svg?style=flat-square)](https://packagist.org/packages/elmudo-dev/filament-custom-attribute-file-upload)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/elmudo-dev/filament-custom-attribute-file-upload/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/elmudo-dev/filament-custom-attribute-file-upload/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/elmudo-dev/filament-custom-attribute-file-upload/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/elmudo-dev/filament-custom-attribute-file-upload/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/elmudo-dev/filament-custom-attribute-file-upload.svg?style=flat-square)](https://packagist.org/packages/elmudo-dev/filament-custom-attribute-file-upload)



This component adds a custom field to save a caption attribute to the images. It has two versions: one to work with the native FileUpload component and another to work with the SpatieMediaLibraryFileUpload component.

![image](https://github.com/user-attachments/assets/06e0729f-dfd9-4861-9919-75f1c07068ec)


## Installation

You can install the package via composer:

```bash
composer require elmudo-dev/filament-custom-attribute-file-upload
```

> [!IMPORTANT]
> If you have not set up a custom theme and are using Filament Panels follow the instructions in the [Filament Docs](https://filamentphp.com/docs/4.x/styling/overview#creating-a-custom-theme) first.

After setting up a custom theme add the plugin's views to your theme css file or your app's css file if using the standalone packages.

```css
@source '../../../../vendor/elmudo-dev/filament-custom-attribute-file-upload/resources/**/*.blade.php';
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

## Usage
### Using with Spatie media Library
```php
CustomAttributeSpatieMediaLibraryFileUpload::make('gallery')
    ->collection('gallery')
    ->conversion('thumb')
    ->downloadable()
    ->panelLayout('grid')
    ->multiple()
    ->reorderable(),
```
### Using with File Upload
```php
CustomAttributeFileUpload::make('gallery')                 
    ->downloadable()
    ->panelLayout('grid')
    ->multiple()
    ->reorderable(),
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

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Hernan Soto](https://github.com/elmudometal)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
