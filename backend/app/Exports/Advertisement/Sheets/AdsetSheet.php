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
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class AdsetSheet extends DefaultValueBinder implements
    WithEvents,
    WithHeadings,
    shouldAutoSize,
    withTitle,
    withDrawings,
    withCustomStartCell,
    WithStyles,
    WithColumnWidths,
    WithCustomValueBinder,
    WithColumnFormatting
{

    public function title(): string
    {
        return 'ad-set-template';
    }

    public function headings(): array
    {
        return [
            'AD_SET_ID',
            'NAME',
            'CAMPAIGN_ID',
            'DELIVERY_STATUS',
            'DAILY_BUDGET',
            'CURRENCY',
            'BID',
            'OPTIMIZATION_GOAL',
            'PIXEL_ID',
            'START_TIME',
            'END_TIME',
        ];
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
                    ->getStyle('A1:L1')
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
        $sheet->getStyle('L1:L2000')->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
        $sheet->getProtection()->setSheet(true);
        $sheet->getStyle('A6:K2000')->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
        $sheet->mergeCells('A1:K1');
        $sheet->mergeCells('A2:K2');
        $sheet->mergeCells('A3:K3');
        $sheet->mergeCells('A4:K4');

        $sheet->getStyle('A4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 20]
        ]);

        $sheet->setCellValue('A4', 'Ad Set Sheet Template');
        $alignment = [
            'vertical' => Alignment::VERTICAL_CENTER,
            'horizontal' => Alignment::HORIZONTAL_CENTER,
        ];
        $sheet->getStyle('A4:K4')->applyFromArray([
            'alignment' => $alignment,
            'font' => ['bold' => true, 'size' => 16],
            'fill' => [
                'fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '#FFFFFF']
            ],
        ]);
        $sheet->getStyle('A5:K5')->applyFromArray([
            'alignment' => $alignment,
            'font' => ['bold' => true],
        ]);
        $headerStyleArray = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => $alignment,
            'font' => ['bold' => true],
        ];
        $sheet->getStyle('A5:K2000')->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
            'alignment' => $alignment,
        ]);
        $sheet->getStyle('A5:K5')->applyFromArray($headerStyleArray);
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
        ];
    }
}
