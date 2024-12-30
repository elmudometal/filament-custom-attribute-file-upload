<?php

namespace ElmudoDev\FilamentCustomAttributeFileUpload\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \ElmudoDev\FilamentCustomAttributeFileUpload\FilamentCustomAttributeFileUpload
 */
class FilamentCustomAttributeFileUpload extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \ElmudoDev\FilamentCustomAttributeFileUpload\FilamentCustomAttributeFileUpload::class;
    }
}
