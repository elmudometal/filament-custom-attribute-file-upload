<?php

declare(strict_types=1);

namespace ElmudoDev\FilamentCustomAttributeFileUpload\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \ElmudoDev\FilamentCustomAttributeFileUpload\FilamentCustomAttributeFileUpload
 */
class FilamentCustomAttributeFileUpload extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \ElmudoDev\FilamentCustomAttributeFileUpload\FilamentCustomAttributeFileUpload::class;
    }
}
