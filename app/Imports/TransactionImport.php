<?php

namespace App\Imports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Enums\TransactionTypeEnum;
use Illuminate\Support\Facades\Log;

class TransactionImport implements ToModel, WithHeadingRow
{
    private $uploadedFileId;
    private $filename;

    public function __construct($uploadedFileId, $filename = null)
    {
        $this->uploadedFileId = $uploadedFileId;
        $this->filename = $filename;
    }

    public function model(array $row)
    {
        if (empty($row['betrag']) || !isset($row['buchungstag'])) {
            log::info("Excel Invalid rows:", $row);
            return null; // skip row
        }

        // map German column titles to English fields here
        return new Transaction([
            'amount' => $this->parseAmount($row['betrag'] ?? null),
            'description' => $row['beguenstigterzahlungspflichtiger'] ?? null,
            'type' => $this->inferType($row),
            'date' => $this->excelDate($row['buchungstag'] ?? null),
            'store_id' => null, // admin will tag it later
            'is_temp' => true,
            'type_id' => TransactionTypeEnum::id($this->inferType($row)),
            'subtype_id' => null,
            'source' => 'csv',
            'source_detail' => $this->filename,
            'uploaded_file_id' => $this->uploadedFileId,
            'creator_id' => auth()->user()?->id,

            // Extra fields (translated from German headers)
            'ordering_account'       => $row['auftragskonto'] ?? null,
            'original_descreption'       => $row['verwendungszweck'] ?? null,
            'booking_date'           => $this->excelDate($row['buchungstag'] ?? null),
            'value_date'             => $this->excelDate($row['valutadatum'] ?? null),
            'booking_text'           => $row['buchungstext'] ?? null,
            'purpose'                => $row['verwendungszweck'] ?? null,
            'creditor_id'            => $row['glaeubiger_id'] ?? null,
            'mandate_reference'      => $row['mandatsreferenz'] ?? null,
            'customer_reference'     => $row['kundenreferenz_end_to_end'] ?? null,
            'batch_reference'        => $row['sammlerreferenz'] ?? null,
            'original_debit_amount'  => $row['lastschrift_ursprungsbetrag'] ?? null,
            'refund_fee'             => $row['auslagenersatz_ruecklastschrift'] ?? null,
            'beneficiary'            => $row['beguenstigterzahlungspflichtiger'] ?? null,
            'iban'                   => $row['kontonummeriban'] ?? null,
            'bic'                    => $row['bic_swift_code'] ?? null,
            'currency'               => $row['waehrung'] ?? null,
            'note'                   => $row['info'] ?? null,
        ]);
    }

    private function parseAmount($value)
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        // Else assume German format: 1.234,56 → 1234.56
        $cleaned = str_replace(['.', ','], ['', '.'], $value);

        if (!is_numeric($cleaned)) {
            return null;
        }
        
        return (float) $cleaned;
    }


    private function inferType($row)
    {
        // naive logic: negative = outcome, positive = income
        return (isset($row['betrag']) && str_contains($row['betrag'], '-')) ? 'outcome' : 'income';
    }

    private function excelDate($value)
    {
        if (!is_numeric($value)) {
            return now()->format('Y-m-d'); // fallback for invalid input
        }

        try {
            return Date::excelToDateTimeObject($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return now()->format('Y-m-d');
        }
    }
}
