<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConvertExcelRequest;
use App\Interfaces\FileConverterServiceInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class FileConverterController extends Controller
{
    public function __construct(
        protected FileConverterServiceInterface $converterService
    ) {}

    public function index()
    {
        return view('convert.index');
    }

    public function convert(ConvertExcelRequest $request): StreamedResponse
    {
        $date = Carbon::parse($request->input('date'));
        $year = $date->year;
        $month = $date->month;

        $writer = $this->converterService->convert($request->file('file'), $year, $month);
        $filename = 'DTVF_Buchungsstapel_' . now()->format('Ymd_His') . '_00001.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
