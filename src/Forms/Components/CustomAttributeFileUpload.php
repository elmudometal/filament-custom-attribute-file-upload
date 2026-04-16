<?php

namespace ElmudoDev\FilamentCustomAttributeFileUpload\Forms\Components;

use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Arr;
use League\Flysystem\UnableToCheckFileExistence;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Throwable;

class CustomAttributeFileUpload extends FileUpload
{
    /** @var view-string */
    protected string $view = 'filament-custom-attribute-file-upload::custom-attribute-file-upload'; // @phpstan-ignore property.defaultValue

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(static function (BaseFileUpload $component, string | array | null $state): void {
            if (blank($state)) {
                $component->state([]);

                return;
            }

            $shouldFetchFileInformation = $component->shouldFetchFileInformation();

            $files = collect(Arr::wrap($state))
                ->filter(static function (array | string $file) use ($component, $shouldFetchFileInformation): bool {
                    if (blank($file)) {
                        return false;
                    }

                    if (! $shouldFetchFileInformation) {
                        return true;
                    }

                    try {
                        return $component->getDisk()->exists($file['file'] ?? $file);
                    } catch (UnableToCheckFileExistence) {
                        return false;
                    }
                })
                ->mapWithKeys(static fn (array | string $file, $key): array => [((string) $key) => $file['file']])
                ->all();

            $component->state($files);

            $captions = [];
            foreach ($state as $fileKey => $file) {
                $captions[$fileKey] = ['caption' => $file['caption']];
            }
            /** @phpstan-ignore property.notFound */
            $component->getLivewire()->data['captions'] = $captions;
        });

        $this->getUploadedFileUsing(static function (BaseFileUpload $component, string | array $file, ?string $fileKey, string | array | null $storedFileNames): ?array {
            /** @var FilesystemAdapter $storage */
            $storage = $component->getDisk();

            $shouldFetchFileInformation = $component->shouldFetchFileInformation();

            if ($shouldFetchFileInformation) {
                try {
                    if (! $storage->exists($file)) {
                        return null;
                    }
                } catch (UnableToCheckFileExistence) {
                    return null;
                }
            }

            $url = null;

            if ($component->getVisibility() === 'private') {
                try {
                    $url = $storage->temporaryUrl(
                        $file,
                        now()->addMinutes(5),
                    );
                } catch (Throwable) {
                    // This driver does not support creating temporary URLs.
                }
            }

            $url ??= $storage->url($file);

            return [
                'uuid' => $fileKey,
                'size' => $shouldFetchFileInformation ? $storage->size($file) : 0,
                'type' => $shouldFetchFileInformation ? $storage->mimeType($file) : null,
                'url' => $url,
            ];
        });

        $this->reorderUploadedFilesUsing(static function (CustomAttributeFileUpload $component, array $state): array {
            /** @phpstan-ignore property.notFound */
            $data = $component->getLivewire()->data;

            foreach ($state as $key => $item) {
                $state[$key] = [
                    'file' => $item,
                    'caption' => $data['captions'][$key]['caption'] ?? '',
                ];
            }

            return $state;
        });
    }

    public function getUploadedFiles(): ?array
    {
        $urls = [];

        foreach ($this->getState() ?? [] as $fileKey => $file) {
            if ($file instanceof TemporaryUploadedFile) {
                $urls[$fileKey] = null;

                continue;
            }

            $callback = $this->getUploadedFileUsing;

            if (! $callback instanceof \Closure) {
                return [$fileKey => null];
            }

            $urls[$fileKey] = $this->evaluate($callback, [
                'file' => $file,
                'fileKey' => $fileKey,
                'storedFileNames' => $this->getStoredFileNames(),
            ]) ?: null;
        }

        return $urls;
    }
}
