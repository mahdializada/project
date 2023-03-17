<?php

/**
 * @author [Ahmad Reza Azimi]
 * @email [teebalhoor@info.org]
 * @create date 2021-05-26 10:56:36
 * @modify date 2022-05-26 10:56:36
 * @desc [description]
 * all reserved for Teeb-al-Hoor Commercial Broker 
 */

namespace App\Exports;

use App\Models\Industry;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
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
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use Illuminate\Support\Str;


class DepartmentExport extends DefaultValueBinder implements
    WithEvents,
    WithHeadings,
    shouldAutoSize,
    withMultipleSheets,
    withTitle,
    withDrawings,
    withCustomStartCell,
    WithStyles,
    WithColumnWidths,
    WithCustomValueBinder,
    WithColumnFormatting
{
    protected $init_status = '';
    protected $columns = ['C', 'D', 'E'];
    protected $column_name = [
        'Company',
        'Industry',
        'Business Location'
    ];
    protected $sheet_names = [
        'companies',
        'industries',
        'business_locations',
    ];
    protected $lengths = [];
    protected $companies = [];
    protected $businessLocations = [];

    public function __construct($companies, $businessLocations)
    {
        $this->businessLocations = $businessLocations;
        $this->companies = $companies;
        $this->lengths[] = $companies->count();
        $this->lengths[] = Industry::all()->count();
        $this->lengths[] = $businessLocations->count();
    }

    public function title(): string
    {
        return 'departments';
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets['departments'] = new DepartmentExport($this->companies, $this->businessLocations);
        $sheets['companies'] = new CompanySheetExport($this->companies);
        $sheets['industries'] = new IndustrySheetExport();
        $sheets['business_locations'] = new BusinessLocationSheetExport($this->businessLocations);
        return $sheets;
    }

    public function headings(): array
    {
        return [
            'Name',
            'Note',
            'Company',
            'Industry',
            'Business_location',
        ];
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $event->sheet->getStyle('A5:E5')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ]
                ]);
                for ($j = 6; $j <= 2000; $j++) {
                    for ($i = 0; $i < count($this->column_name); $i++) {
                        $objValidation = $sheet->getCell($this->columns[$i] . $j)->getDataValidation();
                        $objValidation->setType(DataValidation::TYPE_LIST);
                        $objValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                        $objValidation->setAllowBlank(false);
                        $objValidation->setShowInputMessage(true);
                        $objValidation->setShowErrorMessage(true);
                        $objValidation->setShowDropDown(true);
                        $objValidation->setErrorTitle('Input error');
                        $objValidation->setError('Value is not in list.');
                        $objValidation->setPromptTitle('Pick from list');
                        $objValidation->setPrompt('Please pick a value from the drop-down list.');
                        $index = $this->lengths[$i] == 0 ? 1 : $this->lengths[$i];
                        $objValidation->setFormula1($this->sheet_names[$i] . '!$A$1:$A$' . $index);
                    }
                }
            }
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
        $sheet->getStyle('F1:F2000')->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
        $sheet->getProtection()->setSheet(true);
        $sheet->getStyle('A6:E2000')->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');
        $sheet->mergeCells('A3:E3');
        $sheet->setCellValue('A4', 'Department Bulk Upload Template');
        $alignment = [
            'vertical' => Alignment::VERTICAL_CENTER,
            'horizontal' => Alignment::HORIZONTAL_CENTER,
        ];
        $sheet->getStyle('A4:E4')->applyFromArray([
            'alignment' => $alignment,
            'font' => ['bold' => true, 'size' => 20],
            'fill' => [
                'fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '#FFFFFF']
            ],
        ]);
        $sheet->getStyle('A5:E5')->applyFromArray([
            'alignment' => $alignment,
            'font' => ['bold' => true],
        ]);
        $headerStyleArray = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => $alignment,
            'font' => ['bold' => true],
        ];
        $sheet->getStyle('A6:E2000')->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
            'alignment' => $alignment,
        ]);
        $sheet->getStyle('A5:E5')->applyFromArray($headerStyleArray);
        return [
            // Style the first row as bold text.
            'A1:A4' => [
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
            'A' => '58',
            'B' => '55',
            'C' => '26',
            'D' => '26',
            'E' => '26',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A1:A2000' => NumberFormat::FORMAT_TEXT,
            'B1:B2000' => NumberFormat::FORMAT_TEXT,
            'C1:C2000' => NumberFormat::FORMAT_TEXT,
            'D1:D2000' => NumberFormat::FORMAT_TEXT,
            'E1:F2000' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
