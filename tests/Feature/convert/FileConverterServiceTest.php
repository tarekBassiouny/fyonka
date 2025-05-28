<?php

namespace Tests\Feature\Convert;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Interfaces\FileConverterServiceInterface;

class FileConverterServiceTest extends TestCase
{
    protected FileConverterServiceInterface $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(FileConverterServiceInterface::class);
        Storage::fake('testing');

        if (!is_dir(storage_path('app/testing'))) {
            mkdir(storage_path('app/testing'), 0777, true);
        }
    }

    /** @test */
    public function test_it_converts_xlsx_file_correctly()
    {
        $uploaded = $this->createExcelUpload('input.xlsx');
        $writer = $this->service->convert($uploaded, 2024, 9);
        $path = storage_path('app/testing/output.xlsx');
        $writer->save($path);

        $sheet = IOFactory::load($path)->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $this->assertEquals('DTVF', $rows[1]['A']); // Metadata row check
        $headers = array_flip($rows[2]);            // Header row (row 2) => get column letters

        // Assert values from first mapped data row (row 3)
        $this->assertEquals('100,50', $rows[3][$headers['Umsatz (ohne Soll/Haben-Kz)']]);
        $this->assertEquals('S', $rows[3][$headers['Soll/Haben-Kennzeichen']]);
        $this->assertEquals('EUR', $rows[3][$headers['WKZ Umsatz']]);
        $this->assertEquals('1800', $rows[3][$headers['Konto']]);
        $this->assertEquals('', $rows[3][$headers['Gegenkonto (ohne BU-Schlüssel)']]);
        $this->assertEquals('', $rows[3][$headers['Skonto']]);
        $this->assertEquals('0901', $rows[3][$headers['Belegdatum']]);
        $this->assertEquals('ref001', $rows[3][$headers['Belegfeld 1']]);
        $this->assertEquals('Test Income', $rows[3][$headers['Buchungstext']]);
        $this->assertEquals('abc123', $rows[3][$headers['Buchungs GUID']]);
    }


    /** @test */
    public function test_it_converts_xls_file_correctly()
    {
        $uploaded = $this->createExcelUpload('input.xls', 'Xls');
        $writer = $this->service->convert($uploaded, 2024, 9);
        $path = storage_path('app/testing/output.xls');
        $writer->save($path);

        $sheet = IOFactory::load($path)->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $headers = array_flip($rows[2]);

        $this->assertEquals('DTVF', $rows[1]['A']);
        $this->assertEquals('100,50', $rows[3][$headers['Umsatz (ohne Soll/Haben-Kz)']]);
        $this->assertEquals('ref001', $rows[3][$headers['Belegfeld 1']]);
        $this->assertEquals('S', $rows[3][$headers['Soll/Haben-Kennzeichen']]);
        $this->assertEquals('H', $rows[4][$headers['Soll/Haben-Kennzeichen']]);
    }

    /** @test */
    public function test_it_converts_csv_file_correctly()
    {
        $csv = "ID,Reference,Date completed (UTC),Total amount,Description,Type,Payment currency\nabc123,ref001,2024-09-01,100.50,Test Income,income,EUR\ndef456,ref002,2024-09-15,-45.00,Test Outcome,outcome,EUR";
        $temp = storage_path('app/testing/input.csv');
        file_put_contents($temp, $csv);
        $uploaded = new UploadedFile($temp, 'input.csv', 'text/csv', null, true);

        $writer = $this->service->convert($uploaded, 2024, 9);
        $path = storage_path('app/testing/output_from_csv.xlsx');
        $writer->save($path);

        $sheet = IOFactory::load($path)->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $headers = array_flip($rows[2]);

        $this->assertEquals('DTVF', $rows[1]['A']);
        $this->assertEquals('ref001', $rows[3][$headers['Belegfeld 1']]);
        $this->assertEquals('ref002', $rows[4][$headers['Belegfeld 1']]);
        $this->assertEquals('100,50', $rows[3][$headers['Umsatz (ohne Soll/Haben-Kz)']]);
        $this->assertEquals('H', $rows[4][$headers['Soll/Haben-Kennzeichen']]);
    }

    /** @test */
    public function test_it_handles_missing_headers_gracefully()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['Total amount', 'Date completed (UTC)', 'Type'],
            ['150.00', '2024-09-01', 'income'],
        ]);

        $path = storage_path('app/testing/missing_headers.xlsx');
        (new Xlsx($spreadsheet))->save($path);
        $uploaded = new UploadedFile($path, 'missing_headers.xlsx', null, null, true);

        $writer = $this->service->convert($uploaded, 2024, 9);
        $outPath = storage_path('app/testing/missing_headers_output.xlsx');
        $writer->save($outPath);

        $sheet = IOFactory::load($outPath)->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $this->assertEquals('', $rows[3]['K']); // Reference missing
        $this->assertEquals('', $rows[3]['N']); // Description missing
    }

    /** @test */
    public function test_it_throws_error_on_invalid_excel_file()
    {
        $path = storage_path('app/testing/invalid.xlsx');

        // Write fake content that is not a valid Excel format
        file_put_contents($path, '<?xml version="1.0"?><invalid></invalid>');

        // Simulate file upload
        $uploaded = new UploadedFile(
            $path,
            'invalid.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        // Assert PhpSpreadsheet throws an exception
        $this->expectException(\PhpOffice\PhpSpreadsheet\Reader\Exception::class);

        $this->service->convert($uploaded, 2024, 9);
    }


    /** @test */
    public function test_it_handles_empty_input_file()
    {
        $spreadsheet = new Spreadsheet();
        $path = storage_path('app/testing/empty.xlsx');
        (new Xlsx($spreadsheet))->save($path);
        $uploaded = new UploadedFile($path, 'empty.xlsx', null, null, true);

        $writer = $this->service->convert($uploaded, 2024, 9);
        $outPath = storage_path('app/testing/empty_output.xlsx');
        $writer->save($outPath);

        $sheet = IOFactory::load($outPath)->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $this->assertEquals('DTVF', $rows[1]['A']);
        $this->assertEquals('Umsatz (ohne Soll/Haben-Kz)', $rows[2]['A']);
        $this->assertArrayNotHasKey(3, $rows); // No data rows
    }

    private function createExcelUpload(string $filename, string $format = 'Xlsx'): UploadedFile
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            [
                'ID',
                'Reference',
                'Date completed (UTC)',
                'Total amount',
                'Description',
                'Type',
                'Payment currency',
                'Account',
                'Card number',
                'Payer',
                'Fee',
                'Beneficiary IBAN',
                'Related transaction id'
            ],
            [
                'abc123',
                'ref001',
                '2024-09-01',
                '100.50',
                'Test Income',
                'income',
                'EUR',
                'Bank A',
                '1234',
                'Payer A',
                '0.00',
                'IBAN0001',
                'rel001'
            ],
            [
                'def456',
                'ref002',
                '2024-09-15',
                '-200.75',
                'Test Outcome',
                'outcome',
                'EUR',
                'Bank B',
                '5678',
                'Payer B',
                '0.00',
                'IBAN0002',
                'rel002'
            ],
        ]);

        $path = storage_path("app/testing/{$filename}");
        IOFactory::createWriter($spreadsheet, $format)->save($path);

        return new UploadedFile($path, $filename, null, null, true);
    }
}
