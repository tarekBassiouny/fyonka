<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Interfaces\FileConverterServiceInterface;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Carbon\Carbon;
use Exception;


class FileConverterService implements FileConverterServiceInterface
{
    public function convert(UploadedFile $file, int $year, int $month): Xlsx
    {
        // Main entry point
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Row 1: Start and End Date in Col 14 (N) and 15 (O)
        $dates = $this->getStartAndEndDates($year, $month);
        $metaRow = [
            'DTVF',
            '700',
            '21',
            'Buchungsstapel',
            '13',
            now()->format('YmdHis') . '888',
            '',
            'RE',
            'Moumn.Suliman',
            '',
            '1391763',
            '10011',
            '20240430',
            '3',
            $dates['start'],
            $dates['end'],
            '',
            'MS',
            '1',
            '0',
            '1',
            'EUR',
            '',
            'KP',
            '',
            '259004',
            '4'
        ];

        $metaRow = array_pad($metaRow, 125, ''); // ensure 125 cells
        foreach ($metaRow as $col => $val) {
            $sheet->setCellValueExplicit([$col + 1, 1], (string) $val, DataType::TYPE_STRING);
        }

        // Row 2: German Headers
        $headers = $this->getGermanHeaders();
        foreach ($headers as $index => $header) {
            $sheet->setCellValueExplicit([$index + 1, 2], (string) $header, DataType::TYPE_STRING);
        }

        // Load English spreadsheet
        $input = IOFactory::load($file->getPathname());
        $data = $input->getActiveSheet()->toArray(null, true, true, true);
        $headerRow = array_shift($data); // Get header row (A, B, C => column names)

        // Map each row
        $mapping = $this->getHeaderMapping();
        $mappedData = [];

        foreach ($data as $row) {
            $assocRow = [];
            foreach ($headerRow as $key => $colName) {
                $assocRow[$colName] = $row[$key] ?? null;
            }
            $mappedData[] = $this->mapRow($assocRow, $mapping);
        }

        // Write mapped data rows
        $startRow = 3;
        foreach ($mappedData as $i => $row) {
            foreach ($row as $col => $val) {
                $sheet->setCellValueExplicit([$col + 1, $startRow + $i], (string) $val, DataType::TYPE_STRING);
            }
        }
        // dd(
        //     $sheet->toArray(null, true, true, true)[1],
        //     $sheet->toArray(null, true, true, true)[2],
        //     $sheet->toArray(null, true, true, true)[3]
        // );
        return new Xlsx($spreadsheet);
    }

    private function getGermanHeaders(): array
    {
        return [
            "Umsatz (ohne Soll/Haben-Kz)",
            "Soll/Haben-Kennzeichen",
            "WKZ Umsatz",
            "Kurs",
            "Basis-Umsatz",
            "WKZ Basis-Umsatz",
            "Konto",
            "Gegenkonto (ohne BU-Schlüssel)",
            "BU-Schlüssel",
            "Belegdatum",
            "Belegfeld 1",
            "Belegfeld 2",
            "Skonto",
            "Buchungstext",
            "Postensperre",
            "Diverse Adressnummer",
            "Geschäftspartnerbank",
            "Sachverhalt",
            "Zinssperre",
            "Beleglink",
            "Beleginfo - Art 1",
            "Beleginfo - Inhalt 1",
            "Beleginfo - Art 2",
            "Beleginfo - Inhalt 2",
            "Beleginfo - Art 3",
            "Beleginfo - Inhalt 3",
            "Beleginfo - Art 4",
            "Beleginfo - Inhalt 4",
            "Beleginfo - Art 5",
            "Beleginfo - Inhalt 5",
            "Beleginfo - Art 6",
            "Beleginfo - Inhalt 6",
            "Beleginfo - Art 7",
            "Beleginfo - Inhalt 7",
            "Beleginfo - Art 8",
            "Beleginfo - Inhalt 8",
            "KOST1 - Kostenstelle",
            "KOST2 - Kostenstelle",
            "Kost-Menge",
            "EU-Land u. UStID (Bestimmung)",
            "EU-Steuersatz (Bestimmung)",
            "Abw. Versteuerungsart",
            "Sachverhalt L+L",
            "Funktionsergänzung L+L",
            "BU 49 Hauptfunktionstyp",
            "BU 49 Hauptfunktionsnummer",
            "BU 49 Funktionsergänzung",
            "Zusatzinformation - Art 1",
            "Zusatzinformation- Inhalt 1",
            "Zusatzinformation - Art 2",
            "Zusatzinformation- Inhalt 2",
            "Zusatzinformation - Art 3",
            "Zusatzinformation- Inhalt 3",
            "Zusatzinformation - Art 4",
            "Zusatzinformation- Inhalt 4",
            "Zusatzinformation - Art 5",
            "Zusatzinformation- Inhalt 5",
            "Zusatzinformation - Art 6",
            "Zusatzinformation- Inhalt 6",
            "Zusatzinformation - Art 7",
            "Zusatzinformation- Inhalt 7",
            "Zusatzinformation - Art 8",
            "Zusatzinformation- Inhalt 8",
            "Zusatzinformation - Art 9",
            "Zusatzinformation- Inhalt 9",
            "Zusatzinformation - Art 10",
            "Zusatzinformation- Inhalt 10",
            "Zusatzinformation - Art 11",
            "Zusatzinformation- Inhalt 11",
            "Zusatzinformation - Art 12",
            "Zusatzinformation- Inhalt 12",
            "Zusatzinformation - Art 13",
            "Zusatzinformation- Inhalt 13",
            "Zusatzinformation - Art 14",
            "Zusatzinformation- Inhalt 14",
            "Zusatzinformation - Art 15",
            "Zusatzinformation- Inhalt 15",
            "Zusatzinformation - Art 16",
            "Zusatzinformation- Inhalt 16",
            "Zusatzinformation - Art 17",
            "Zusatzinformation- Inhalt 17",
            "Zusatzinformation - Art 18",
            "Zusatzinformation- Inhalt 18",
            "Zusatzinformation - Art 19",
            "Zusatzinformation- Inhalt 19",
            "Zusatzinformation - Art 20",
            "Zusatzinformation- Inhalt 20",
            "Stück",
            "Gewicht",
            "Zahlweise",
            "Forderungsart",
            "Veranlagungsjahr",
            "Zugeordnete Fälligkeit",
            "Skontotyp",
            "Auftragsnummer",
            "Buchungstyp (Anzahlungen)",
            "USt-Schlüssel (Anzahlungen)",
            "EU-Land (Anzahlungen)",
            "Sachverhalt L+L (Anzahlungen)",
            "EU-Steuersatz (Anzahlungen)",
            "Erlöskonto (Anzahlungen)",
            "Herkunft-Kz",
            "Buchungs GUID",
            "KOST-Datum",
            "SEPA-Mandatsreferenz",
            "Skontosperre",
            "Gesellschaftername",
            "Beteiligtennummer",
            "Identifikationsnummer",
            "Zeichnernummer",
            "Postensperre bis",
            "Bezeichnung SoBil-Sachverhalt",
            "Kennzeichen SoBil-Buchung",
            "Festschreibung",
            "Leistungsdatum",
            "Datum Zuord. Steuerperiode",
            "Fälligkeit",
            "Generalumkehr (GU)",
            "Steuersatz",
            "Land",
            "Abrechnungsreferenz",
            "BVV-Position",
            "EU-Land u. UStID (Ursprung)",
            "EU-Steuersatz (Ursprung)",
            "Abw. Skontokonto"
        ];
    }

    private function getHeaderMapping(): array
    {
        return [
            'Total amount' => 'Umsatz (ohne Soll/Haben-Kz)',
            'Date completed (UTC)' => 'Belegdatum',
            'Description' => 'Buchungstext',
            'Reference' => 'Belegfeld 1',
            'ID' => 'Buchungs GUID',
            'Fee' => 'Skonto',
            'Card number' => 'Diverse Adressnummer',
            'Payer' => 'Geschäftspartnerbank',
            'Payment currency' => 'WKZ Umsatz',
            'Account' => 'Konto',
            'Beneficiary IBAN' => 'Gegenkonto (ohne BU-Schlüssel)',
            'Related transaction id' => 'Beleginfo - Inhalt 1',
            'Type' => 'Soll/Haben-Kennzeichen',
        ];
    }

    private function getStartAndEndDates(int $year, int $month): array
    {
        // Return ['start' => 'YYYYMM01', 'end' => 'YYYYMMDD']
        $start = Carbon::create($year, $month, 1)->format('Ymd');
        $end = Carbon::create($year, $month, 1)->endOfMonth()->format('Ymd');

        return [
            'start' => $start, // e.g., 20240901
            'end' => $end      // e.g., 20240930
        ];
    }

    private function mapRow(array $englishRow, array $mapping): array
    {
        // Return array with 125 values, mapped + filled
        $row = array_fill(0, 125, ''); // initialize 125 columns with empty strings

        foreach ($mapping as $englishKey => $germanHeader) {
            $value = $englishRow[$englishKey] ?? null;

            switch ($germanHeader) {
                case 'Umsatz (ohne Soll/Haben-Kz)':
                    $row[$this->getGermanIndex($germanHeader)] = $this->formatAmount($value);
                    break;

                case 'Soll/Haben-Kennzeichen':
                    $row[$this->getGermanIndex($germanHeader)] = $this->detectSollHaben($englishRow['Total amount'] ?? 0);
                    break;

                case 'Belegdatum':
                    $row[$this->getGermanIndex($germanHeader)] = $this->formatBelegdatum($value);
                    break;

                case 'Konto':
                    $row[$this->getGermanIndex($germanHeader)] = '1800';
                    break;

                case 'Skonto':
                case 'Gegenkonto (ohne BU-Schlüssel)':
                    $row[$this->getGermanIndex($germanHeader)] = '';
                    break;

                default:
                    $row[$this->getGermanIndex($germanHeader)] = $value;
                    break;
            }
        }

        return $row;
    }

    private function formatAmount($value): string
    {
        // Ensure numeric, take absolute value, and replace dot with comma
        $numeric = is_numeric($value) ? abs((float) $value) : 0;
        return str_replace('.', ',', number_format($numeric, 2, '.', ''));
    }

    private function formatBelegdatum(string $date): string
    {
        // Convert English date to MMDD
        try {
            $parsed = Carbon::parse($date);
            return $parsed->format('md'); // MMYY format
        } catch (Exception $e) {
            return ''; // fallback if invalid date
        }
    }

    private function detectSollHaben($amount): string
    {
        // Return 'H' if amount < 0, else 'S'
        return (float) $amount > 0 ? 'S' : 'H';
    }

    private function getGermanIndex(string $header): int
    {
        return array_search($header, $this->getGermanHeaders());
    }
}
