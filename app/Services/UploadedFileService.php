<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use App\Models\UploadedFile as UploadedFileModel;
use Illuminate\Support\Carbon;
use App\Interfaces\UploadedFileServiceInterface;

class UploadedFileService implements UploadedFileServiceInterface
{
    public function storeUploadedFile(UploadedFile $file): UploadedFileModel
    {
        $folder = 'uploads/' . Carbon::today()->format('Y-m-d');
        $path = $file->store($folder);

        if (!$path) {
            throw new \RuntimeException('File storage failed');
        }

        return UploadedFileModel::create([
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
        ]);
    }
}
