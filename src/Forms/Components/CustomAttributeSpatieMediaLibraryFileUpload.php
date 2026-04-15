<?php

namespace ElmudoDev\FilamentCustomAttributeFileUpload\Forms\Components;

use Closure;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use League\Flysystem\UnableToCheckFileExistence;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\FileAdder;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class CustomAttributeSpatieMediaLibraryFileUpload extends SpatieMediaLibraryFileUpload
{
    protected string $view = 'filament-custom-attribute-file-upload::custom-attribute-file-upload';

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadStateFromRelationshipsUsing(static function (CustomAttributeSpatieMediaLibraryFileUpload $component, HasMedia $record): void {
            /** @var Model&HasMedia $record */
            $media = $record->load('media')->getMedia($component->getCollection() ?? 'default')
                ->when(
                    $component->hasMediaFilter(),
                    fn (Collection $media) => $component->filterMedia($media)
                )
                ->when(
                    ! $component->isMultiple(),
                    fn (Collection $media): Collection => $media->take(1),
                )
                ->mapWithKeys(function (Media $media): array {
                    $uuid = $media->getAttributeValue('uuid');

                    return [$uuid => ['uuid' => $uuid, 'name' => $media->getAttributeValue('name')]];
                })
                ->toArray();
            $uuids = [];
            $captions = [];
            foreach ($media as $item) {
                $uuids[$item['uuid']] = $item['uuid'];
                $captions[$item['uuid']] = ['caption' => $item['name']];
            }

            $component->state($uuids);
            $component->getLivewire()->data['captions'] = $captions;
        });

        $this->afterStateHydrated(null);

        $this->beforeStateDehydrated(null);

        $this->dehydrated(false);

        $this->getUploadedFileUsing(static function (CustomAttributeSpatieMediaLibraryFileUpload $component, string $file): ?array {
            if (! $component->getRecord()) {
                return null;
            }

            /** @var ?Media $media */
            $media = $component->getRecord()->getRelationValue('media')->firstWhere('uuid', $file);

            $url = null;

            if ($component->getVisibility() === 'private') {
                $conversion = $component->getConversion();

                try {
                    $url = $media?->getTemporaryUrl(
                        now()->addMinutes(30)->endOfHour(),
                        (filled($conversion) && $media->hasGeneratedConversion($conversion)) ? $conversion : '',
                    );
                } catch (Throwable $exception) {
                    // This driver does not support creating temporary URLs.
                }
            }

            if ($component->getConversion() && $media?->hasGeneratedConversion($component->getConversion())) {
                $url ??= $media->getUrl($component->getConversion());
            }

            $url ??= $media?->getUrl();

            return [
                'uuid' => $media?->getAttributeValue('uuid'),
                'name' => $media?->getAttributeValue('name') ?? $media?->getAttributeValue('file_name'),
                'size' => $media?->getAttributeValue('size'),
                'type' => $media?->getAttributeValue('mime_type'),
                'url' => $url,
            ];
        });

        $this->saveRelationshipsUsing(static function (CustomAttributeSpatieMediaLibraryFileUpload $component) {
            $component->deleteAbandonedFiles();
            $component->saveUploadedFiles();
        });

        $this->saveUploadedFileUsing(static function (CustomAttributeSpatieMediaLibraryFileUpload $component, TemporaryUploadedFile $file, ?Model $record): ?string {
            if (! method_exists($record, 'addMediaFromString')) {
                return $file;
            }

            try {
                if (! $file->exists()) {
                    return null;
                }
            } catch (UnableToCheckFileExistence $exception) {
                return null;
            }

            /** @var FileAdder $mediaAdder */
            $mediaAdder = $record->addMediaFromString($file->get());
            $uuid = '';
            $data = $component->getLivewire()->data;

            foreach ($component->getState() as $index => $item) {
                if ($item == $file) {
                    $uuid = $index;
                }
            }

            $filename = $component->getUploadedFileNameForStorage($file);
            $component->getMediaName($data['captions'][$uuid]['caption'] ?? '');
            $media = $mediaAdder
                ->addCustomHeaders($component->getCustomHeaders())
                ->usingFileName($filename)
                ->usingName($data['captions'][$uuid]['caption'] ?? '')
                ->storingConversionsOnDisk($component->getConversionsDisk() ?? '')
                ->withCustomProperties($component->getCustomProperties())
                ->withManipulations($component->getManipulations())
                ->withResponsiveImagesIf($component->hasResponsiveImages())
                ->withProperties($component->getProperties())
                ->toMediaCollection($component->getCollection() ?? 'default', $component->getDiskName());

            if (isset($data['captions'][$uuid])) {
                // Copiar el contenido del antiguo uuid al nuevo uuid de la tabla media
                $component->getLivewire()->data['captions'][$media->getAttributeValue('uuid')] = $data['captions'][$uuid];
            }

            return $media->getAttributeValue('uuid');
        });

        $this->reorderUploadedFilesUsing(static function (CustomAttributeSpatieMediaLibraryFileUpload $component, ?Model $record, array $state): array {

            $data = $component->getLivewire()->data;
            $uuids = array_filter(array_values($state));

            $mediaClass = ($record && method_exists($record, 'getMediaModel')) ? $record->getMediaModel() : null;
            $mediaClass ??= config('media-library.media_model', Media::class);

            $medias = $mediaClass::query()->whereIn('uuid', $uuids)->get();

            foreach ($medias as $media) {
                $media->name = $data['captions'][$media->getAttributeValue('uuid')]['caption'] ?? '';
                $media->save();
            }

            $mappedIds = $medias->pluck('id', 'uuid')->toArray();
            $mediaClass::setNewOrder([
                ...array_flip($uuids),
                ...$mappedIds,
            ]);

            return $state;
        });
    }

    public function mediaName(string | Closure | null $name): static
    {
        $this->mediaName = $name;

        return $this;
    }

    public function getMediaName(TemporaryUploadedFile | string $file): ?string
    {
        $this->mediaName($file);

        return $this->mediaName;
    }
}
