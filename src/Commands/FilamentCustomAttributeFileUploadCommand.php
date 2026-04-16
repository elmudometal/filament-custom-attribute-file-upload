<?php

declare(strict_types=1);

namespace ElmudoDev\FilamentCustomAttributeFileUpload\Commands;

use Illuminate\Console\Command;

class FilamentCustomAttributeFileUploadCommand extends Command
{
    public $signature = 'filament-custom-attribute-file-upload';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
