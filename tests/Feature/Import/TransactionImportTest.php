<?php

namespace Tests\Feature\Import;

use Tests\TestCase;
use App\Models\Transaction;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Imports\TransactionImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\UploadedFile as UploadedFileModel;
use App\Models\TransactionType;

class TransactionImportTest extends TestCase
{
    use RefreshDatabase;

    private UploadedFileModel $uploaded;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        TransactionType::factory()->create(['id' => 1, 'name' => 'income']);
        TransactionType::factory()->create(['id' => 2, 'name' => 'outcome']);
        $this->uploaded = UploadedFileModel::factory()->create();
    }

    /** @test */
    public function it_can_import_a_single_transaction_from_csv()
    {
        // Prepare headers in German
        $csvContent = <<<CSV
auftragskonto,buchungstag,valutadatum,buchungstext,verwendungszweck,glaeubiger_id,mandatsreferenz,kundenreferenz_end_to_end,sammlerreferenz,lastschrift_ursprungsbetrag,auslagenersatz_ruecklastschrift,beguenstigterzahlungspflichtiger,kontonummeriban,bic_swift_code,betrag,waehrung,info
DE123456789,45163,45166,TESTBUCHUNG,Test Zweck,,M-REF,C-REF,S-REF,50,0,Test Name,DE3212345678,TESTBIC,-9.99,EUR,Notiz
CSV;

        $filePath = 'test/import.csv';
        Storage::disk('local')->put($filePath, $csvContent);

        $uploadedFile = UploadedFileModel::create([
            'filename' => 'import.csv',
            'path' => $filePath,
        ]);

        Excel::import(
            new TransactionImport($uploadedFile->id, $uploadedFile->filename),
            $filePath,
            'local'
        );

        $this->assertDatabaseCount('transactions', 1);

        $tx = Transaction::first();
        $this->assertEquals(-9.99, $tx->amount);
        $this->assertEquals('outcome', $tx->type);
        $this->assertEquals($uploadedFile->id, $tx->uploaded_file_id);
        $this->assertEquals('csv', $tx->source);
        $this->assertEquals('import.csv', $tx->source_detail);
        $this->assertEquals('Test Zweck', $tx->purpose);
        $this->assertEquals('EUR', $tx->currency);
    }

    /** @test */
    public function it_fails_gracefully_on_invalid_csv_structure()
    {
        // Malformed CSV (missing required headers)
        $csvContent = <<<CSV
not_a_valid_header,wrong_column
something,else
CSV;

        $filePath = 'test/broken.csv';
        Storage::disk('local')->put($filePath, $csvContent);

        $uploadedFile = UploadedFileModel::create([
            'filename' => 'broken.csv',
            'path' => $filePath,
        ]);

        Excel::import(
            new TransactionImport($uploadedFile->id, $uploadedFile->filename),
            $filePath,
            'local'
        );

        $this->assertDatabaseCount('transactions', 0);
    }

    /** @test */
    public function it_skips_empty_rows_silently()
    {
        // CSV with a valid row + empty line
        $csvContent = <<<CSV
auftragskonto,buchungstag,valutadatum,buchungstext,verwendungszweck,glaeubiger_id,mandatsreferenz,kundenreferenz_end_to_end,sammlerreferenz,lastschrift_ursprungsbetrag,auslagenersatz_ruecklastschrift,beguenstigterzahlungspflichtiger,kontonummeriban,bic_swift_code,betrag,waehrung,info
DE123456789,45163,45166,TESTBUCHUNG,Test Zweck,,,,,,,,,,-9.99,EUR,Note

,,,,,,,,,,,,,,,,
CSV;

        $path = 'test/empty_row.csv';
        Storage::disk('local')->put($path, $csvContent);

        Excel::import(new TransactionImport($this->uploaded->id, $this->uploaded->filename), $path, 'local');

        $this->assertDatabaseCount('transactions', 1);
    }

    /** @test */
    public function it_handles_invalid_amount_format()
    {
        $csv = <<<CSV
auftragskonto,buchungstag,valutadatum,betrag
DE123,45163,45166,NOT_A_NUMBER
CSV;

        $path = 'test/invalid_amount.csv';
        Storage::disk('local')->put($path, $csv);

        Excel::import(new TransactionImport($this->uploaded->id, $this->uploaded->filename), $path, 'local');

        $tx = Transaction::first();
        $this->assertNull($tx->amount);
    }

    /** @test */
    public function it_handles_invalid_excel_date()
    {
        $csv = <<<CSV
auftragskonto,buchungstag,betrag
DE123,NOT_A_DATE,123.45
CSV;

        $path = 'test/invalid_date.csv';
        Storage::disk('local')->put($path, $csv);

        Excel::import(new TransactionImport($this->uploaded->id, $this->uploaded->filename), $path, 'local');

        $tx = Transaction::first();
        $this->assertEquals(now()->format('Y-m-d'), $tx->date->toDateString());
    }

    /** @test */
    public function it_infers_income_type_for_positive_amount()
    {
        $import = new TransactionImport(1);
        $row = ['betrag' => '123.45', 'buchungstag' => '45163',];
        $model = $import->model($row);

        $this->assertEquals('income', $model->type);
        $this->assertEquals(1, $model->type_id);
    }

    /** @test */
    public function it_infers_outcome_type_for_negative_amount()
    {
        $import = new TransactionImport(1);
        $row = ['betrag' => '-456.78', 'buchungstag' => '45163',];
        $model = $import->model($row);

        $this->assertEquals('outcome', $model->type);
        $this->assertEquals(2, $model->type_id);
    }

    /** @test */
    public function it_maps_optional_fields_correctly()
    {
        $csv = <<<CSV
auftragskonto,buchungstag,valutadatum,buchungstext,verwendungszweck,glaeubiger_id,mandatsreferenz,kundenreferenz_end_to_end,sammlerreferenz,lastschrift_ursprungsbetrag,auslagenersatz_ruecklastschrift,beguenstigterzahlungspflichtiger,kontonummeriban,bic_swift_code,betrag,waehrung,info
DE111,45163,45166,Buchung,Beschreibung,GLB123,MREF123,CREF123,SREF123,10,2,Max Mustermann,DE991234567890,TESTBIC,-100.50,EUR,Notiztext
CSV;

        $path = 'test/full_fields.csv';
        Storage::disk('local')->put($path, $csv);

        Excel::import(
            new TransactionImport($this->uploaded->id, $this->uploaded->filename),
            $path,
            'local'
        );

        $tx = Transaction::first();

        $this->assertEquals('DE111', $tx->ordering_account);
        $this->assertEquals('2023-08-25', $tx->booking_date->toDateString()); // 45163
        $this->assertEquals('2023-08-28', $tx->value_date->toDateString());   // 45166
        $this->assertEquals('Buchung', $tx->booking_text);
        $this->assertEquals('Beschreibung', $tx->purpose);
        $this->assertEquals('GLB123', $tx->creditor_id);
        $this->assertEquals('MREF123', $tx->mandate_reference);
        $this->assertEquals('CREF123', $tx->customer_reference);
        $this->assertEquals('SREF123', $tx->batch_reference);
        $this->assertEquals(10, $tx->original_debit_amount);
        $this->assertEquals(2, $tx->refund_fee);
        $this->assertEquals('Max Mustermann', $tx->beneficiary);
        $this->assertEquals('DE991234567890', $tx->iban);
        $this->assertEquals('TESTBIC', $tx->bic);
        $this->assertEquals('EUR', $tx->currency);
        $this->assertEquals('Notiztext', $tx->note);
    }
}
