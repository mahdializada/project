<?php

/**
 * @author [Ahmad Reza Azimi]
 * @email [teebalhoor@info.org]
 * @create date 2021-05-26 10:56:36
 * @modify date 2022-05-26 10:56:36
 * @desc [description]
 * all reserved for Teeb-al-Hoor Commercial Broker
 */

namespace App\Exports\Advertisement\Sheets;

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
use PhpOffice\PhpSpreadsheet\Style\Protection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class AdSheet implements
    WithEvents,
    WithHeadings,
    shouldAutoSize,
    withTitle,
    withDrawings,
    withCustomStartCell,
    WithStyles,
    WithColumnWidths,
    WithColumnFormatting
{

    public function title(): string
    {
        return 'ad-template';
    }

    public function headings(): array
    {
        return [
            'AD_NAME',
            'AD_ID',
            'AD_SET_ID',
            'CAMPAIGN_ID',
            'PRODUCT_CODE',
            'DELIVERY_STATUS',
            'RESULT',
            'IMPRESSIONS',
            'VIEWABLE_IMPRESSIONS',
            'TWO_SECOND_VIDEO_VIEWS',
            'SIX_SECOND_VIDEO_VIEWS',
            'AVERAGE_SCREEN_TIME',
            'VIDEO_VIEWS',
            'VIEW_COMPLETION',
            'SPEND',
            'CLICKS',
            'TOTAL_PAGE_VIEWS',
            'STORY_OPENS',
            'CURRENCY',
            'REACH',
            'COST_PER_FIFTEEN_SEC_VIEW',
            'FREQUENCY',
            'OPTIMIZATION_GOAL',
            'START_TIME',
            'END_TIME',
            'SERVER_CREATED_AT',
            'SERVER_UPDATED_AT',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A6:A2000' => NumberFormat::FORMAT_TEXT,
            'B6:B2000' => NumberFormat::FORMAT_TEXT,
            'C6:C2000' => NumberFormat::FORMAT_TEXT,
            'D6:D2000' => NumberFormat::FORMAT_TEXT,
            'E6:E2000' => NumberFormat::FORMAT_TEXT,
            'F6:F2000' => NumberFormat::FORMAT_TEXT,
            'G6:G2000' => NumberFormat::FORMAT_TEXT,
            'H6:H2000' => NumberFormat::FORMAT_TEXT,
            'I6:I2000' => NumberFormat::FORMAT_TEXT,
            'J6:J2000' => NumberFormat::FORMAT_TEXT,
            'K6:K2000' => NumberFormat::FORMAT_TEXT,
            'M6:M2000' => NumberFormat::FORMAT_TEXT,
            'N6:N2000' => NumberFormat::FORMAT_TEXT,
            'O6:O2000' => NumberFormat::FORMAT_TEXT,
            'P6:P2000' => NumberFormat::FORMAT_TEXT,
            'Q6:Q2000' => NumberFormat::FORMAT_TEXT,
            'R6:R2000' => NumberFormat::FORMAT_TEXT,
            'S6:S2000' => NumberFormat::FORMAT_TEXT,
            'T6:T2000' => NumberFormat::FORMAT_TEXT,
            'U6:U2000' => NumberFormat::FORMAT_TEXT,
            'V6:V2000' => NumberFormat::FORMAT_TEXT,
            'W6:W2000' => NumberFormat::FORMAT_TEXT,
            'X6:X2000' => NumberFormat::FORMAT_TEXT,
            'Y6:Y2000' => NumberFormat::FORMAT_TEXT,
            'Z6:Z2000' => NumberFormat::FORMAT_TEXT,
            'AA6:AA2000' => NumberFormat::FORMAT_TEXT,
        ];
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
                    ->getStyle('A1:AB1')
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


    public function styles(Worksheet $sheet): array
    {
        $sheet->getProtection()->setPassword(Str::uuid());
        $sheet->getStyle('AB1:AB2000')->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
        $sheet->getProtection()->setSheet(true);
        $sheet->getStyle('A6:AA2000')->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
        $sheet->mergeCells('A1:AA1');
        $sheet->mergeCells('A2:AA2');
        $sheet->mergeCells('A3:AA3');
        $sheet->mergeCells('A4:AA4');

        $sheet->getStyle('A4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 20]
        ]);

        $sheet->setCellValue('A4', 'Ad Sheet Template');
        $alignment = [
            'vertical' => Alignment::VERTICAL_CENTER,
            'horizontal' => Alignment::HORIZONTAL_CENTER,
        ];
        $sheet->getStyle('A4:AA4')->applyFromArray([
            'alignment' => $alignment,
            'font' => ['bold' => true, 'size' => 16],
            'fill' => [
                'fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '#FFFFFF']
            ],
        ]);
        $sheet->getStyle('A5:AA5')->applyFromArray([
            'alignment' => $alignment,
            'font' => ['bold' => true],
        ]);
        $headerStyleArray = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => $alignment,
            'font' => ['bold' => true],
        ];
        $sheet->getStyle('A5:AA2000')->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
            'alignment' => $alignment,
        ]);
        $sheet->getStyle('A5:AA5')->applyFromArray($headerStyleArray);
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
            'M' => '20',
            'N' => '20',
            'O' => '20',
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
        ];
    }
}
