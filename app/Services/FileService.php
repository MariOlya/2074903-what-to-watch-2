<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class FileService
{
    public const FOLDER_IMAGES = 'images/';
    public const FOLDER_AVATARS = 'avatars/';

    public const PUBLIC_STORAGE = 'public';

    public static function deleteFileFromStorage(
        string $path,
        string $folder = self::FOLDER_IMAGES,
        string $disk = self::PUBLIC_STORAGE
    ): void {
        if (Storage::disk($disk)->missing($folder.$path)) {
            Log::warning('Client tried to delete not existed file '.$folder.$path.' on '.$disk.' storage disk. Need to check');
        }

        Storage::disk($disk)->delete($folder.$path);
    }

    /**
     * @param string $link
     * @param string $folder
     * @param string $disk
     * @return void
     * @throws InternalErrorException
     */
    public static function addFileToStorage(
        string $link,
        string $path,
        string $folder = self::FOLDER_IMAGES,
        string $disk = self::PUBLIC_STORAGE
    ): void {
        $fileUpload = file_get_contents($link);

        if (!$fileUpload) {
            throw new InternalErrorException(
                'We can not read the image, please, try again',
                500
            );
        }

        $isSave = Storage::disk($disk)->put($folder.$path, $fileUpload);

        if (!$isSave) {
            throw new InternalErrorException(
                'We can not save the image, please, try again',
                500
            );
        }
    }
}
