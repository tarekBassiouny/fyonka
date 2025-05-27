<?php

namespace App\Interfaces;

use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

interface FileConverterServiceInterface
{
    public function convert(UploadedFile $file, int $year, int $month): Xlsx;
}
