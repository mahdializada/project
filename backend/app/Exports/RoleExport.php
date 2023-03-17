<?php

namespace App\Exports;

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

class RoleExport extends DefaultValueBinder implements
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
    protected $schedule_type = [];
    protected $column_name = [
        'Department',
        'Schedule_type'
    ];
    protected $sheet_names = [
        'departments',
        'actions',
        'subsystems'
    ];
    protected $lengths = [];
    protected $departments = [];

    public function __construct($schedule_type, $departments)
    {
        $this->departments = $departments;
        $this->schedule_type = $schedule_type;
        $this->lengths[] = collect($departments)->count();
     }

    public function title(): string
    {
        return 'roles';
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets['roles'] = new RoleExport($this->schedule_type, $this->departments);
        $sheets['departments'] = new DepartmentSheetExport($this->departments);
        return $sheets;
    }

    public function headings(): array{
        return [
            'Name',
            'Note',
            'Department',
            'Schedule_type',
            'Date_range',
            'Time_range'
        ];
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $event->sheet->getStyle('A5:F5')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ]
                ]);
                for ($j = 6; $j <= 2000; $j++)
                {
                    for ($i = 0; $i < count($this->column_name); $i++)
                    {
                        $objValidation = $sheet->getCell($this->columns[$i].$j)->getDataValidation();
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
                        switch ($this->column_name[$i]) {
                            case 'Schedule_type': {
                                $objValidation->setFormula1('"'.implode(', ', $this->schedule_type).'"');
                            }
                                break;
                            default: {
                                $index = $this->lengths[$i] == 0 ? 1 : $this->lengths[$i];
                                $objValidation->setFormula1($this->sheet_names[$i].'!$A$1:$A$'.$index);
                            }
                                break;
                        }
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
        $sheet->getStyle('G1:G2000')->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
        $sheet->getProtection()->setSheet(true);
        $sheet->getStyle('A6:F2000')->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        $sheet->mergeCells('A3:F3');
        $sheet->getColumnDimension('A')->setWidth(45);
        $sheet->setCellValue('A4', 'Role Bulk Upload Template');
        $alignment = [
            'vertical' => Alignment::VERTICAL_CENTER,
            'horizontal' => Alignment::HORIZONTAL_CENTER,
        ];
        $sheet->getStyle('A4:F4')->applyFromArray([
            'alignment' => $alignment,
            'font' => ['bold' => true, 'size' => 20],
            'fill' => [
                'fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '#FFFFFF']
            ],
        ]);
        $sheet->getStyle('A5:F5')->applyFromArray([
            'alignment' => $alignment,
            'font' => ['bold' => true],
        ]);
        $headerStyleArray = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => $alignment,
            'font' => ['bold' => true],
        ];
        $sheet->getStyle('A6:F2000')->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
            'alignment' => $alignment,
        ]);
        $sheet->getStyle('A5:F5')->applyFromArray($headerStyleArray);
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
            'A' => '22',
            'B' => '40',
            'C' => '26',
            'D' => '26',
            'E' => '26',
            'F' => '25',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A1:A2000' => NumberFormat::FORMAT_TEXT,
            'B1:B2000' => NumberFormat::FORMAT_TEXT,
            'C1:C2000' => NumberFormat::FORMAT_TEXT,
            'D1:D2000' => NumberFormat::FORMAT_TEXT,
            'E1:E2000' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'F1:E2000' => NumberFormat::FORMAT_DATE_TIME1,
        ];
    }
}
