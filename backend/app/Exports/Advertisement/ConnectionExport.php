<?php

/**
 * @author [Ahmad Reza Azimi]
 * @email [teebalhoor@info.org]
 * @create date 2021-05-26 10:56:36
 * @modify date 2022-05-26 10:56:36
 * @desc [description]
 * all reserved for Teeb-al-Hoor Commercial Broker
 */

namespace App\Exports\Advertisement;

use App\Exports\Advertisement\Sheets\AdSheet;
use App\Exports\Advertisement\Sheets\AdsetSheet;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ConnectionExport extends DefaultValueBinder implements
    WithEvents,
    WithHeadings,
    shouldAutoSize,
    withTitle,
    withDrawings,
    withCustomStartCell,
    WithStyles,
    WithColumnWidths,
    WithCustomValueBinder,
    WithColumnFormatting,
    WithMultipleSheets
{
    protected $account_id;
    
    public function __construct($account_id){
        $this->account_id = $account_id;
    }

    public function title(): string
    {
        return 'connection-&-campaign-template';
    }

    public function headings(): array
    {
        return [
            'CAMPAIGN_ID',
            'NAME',
            'OBJECTIVE_TYPE',
            'BUDGET_MODE',
            'BUDGET',
            'CAMPAIGN_TYPE',
            'DELIVERY_STATUS',
            'OBJECTIVE',
            'START_TIME',
            'END_TIME',
            'SERVER_CREATED_AT',
            'SERVER_UPDATED_AT',
        ];
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets['connection-template'] = new ConnectionExport($this->account_id);
        $sheets['ad-set-template'] = new AdsetSheet();
        $sheets['ad-template'] = new AdSheet();
        return $sheets;
    }

    /**
     * Write code on Method
     *
     * @return AfterSheet()
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet
                    ->getDelegate()
                    ->getStyle('A1:M1')
                    ->getFont()
                    ->getColor()
                    ->setARGB('FFFFFF');
            },
        ];
    }

    public function drawings(): Drawing
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Teeb-Al-Hoor');
        $drawing->setPath(public_path('/common/teebl-al-hoor.jpeg'));
        $drawing->setHeight(100);
        $drawing->setCoordinates('A1');

        return $drawing;
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function columnFormats(): array
    {
        return [
            'A6:A2000' => NumberFormat::FORMAT_TEXT,
            'B6:A2000' => NumberFormat::FORMAT_TEXT,
            'C6:C2000' => NumberFormat::FORMAT_TEXT,
            'D6:D2000' => NumberFormat::FORMAT_TEXT,
            'E6:E2000' => NumberFormat::FORMAT_TEXT,
            'F6:F2000' => NumberFormat::FORMAT_TEXT,
            'G6:G2000' => NumberFormat::FORMAT_TEXT,
            'H6:H2000' => NumberFormat::FORMAT_TEXT,
            'I6:I2000' => NumberFormat::FORMAT_TEXT,
            'J6:J2000' => NumberFormat::FORMAT_TEXT,
            'K6:K2000' => NumberFormat::FORMAT_TEXT,
            'L6:L2000' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getProtection()->setPassword(Str::uuid());
        $sheet->getStyle('M1:M2000')->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
        $sheet->getProtection()->setSheet(true);
        $sheet->getStyle('A6:L2000')->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
        $sheet->mergeCells('A1:L1');
        $sheet->mergeCells('A2:L2');
        $sheet->mergeCells('A3:L3');
        $sheet->mergeCells('A4:L4');

        $sheet->getStyle('A4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 20]
        ]);

        $sheet->setCellValue('A4', 'Connection & Campaign Template ('.$this->account_id.')');
        $alignment = [
            'vertical' => Alignment::VERTICAL_CENTER,
            'horizontal' => Alignment::HORIZONTAL_CENTER,
        ];
        $sheet->getStyle('A4:L4')->applyFromArray([
            'alignment' => $alignment,
            'font' => ['bold' => true, 'size' => 16],
            'fill' => [
                'fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '#FFFFFF']
            ],
        ]);
        $sheet->getStyle('A5:L5')->applyFromArray([
            'alignment' => $alignment,
            'font' => ['bold' => true],
        ]);
        $headerStyleArray = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => $alignment,
            'font' => ['bold' => true],
        ];
        $sheet->getStyle('A5:L2000')->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
            'alignment' => $alignment,
        ]);
        $sheet->getStyle('A5:L5')->applyFromArray($headerStyleArray);
        return [
            // Style the first row as bold text.
            'A1:A3' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '#FFFFFF']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
                'font' => ['size' => 20],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => '20',
            'B' => '20',
            'C' => '20',
            'D' => '20',
            'E' => '20',
            'F' => '20',
            'G' => '20',
            'H' => '20',
            'I' => '20',
            'J' => '20',
            'K' => '20',
            'L' => '20',
            // 'M' => '20',
            // 'N' => '20',
            // 'O' => '20',
            // 'ٔP' => '20',
            // 'Q' => '20',
            // 'ٔR' => '20',
            // 'ٔS' => '20',
            // 'ٔT' => '20',
            // 'ٔU' => '20',
            // 'ٔV' => '20',
            // 'ٔW' => '20',
            // 'ٔX' => '20',
            // 'ٔY' => '20',
            // 'ٔZ' => '20',
            // 'ٔAA' => '30',
            // 'ٔAB' => '30',
        ];
    }
}
