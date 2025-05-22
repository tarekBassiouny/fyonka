<?php

namespace App\Interfaces;

use Illuminate\Http\UploadedFile;
use App\Models\UploadedFile as UploadedFileModel;

interface UploadedFileServiceInterface
{
    public function storeUploadedFile(UploadedFile $file): UploadedFileModel;
}
