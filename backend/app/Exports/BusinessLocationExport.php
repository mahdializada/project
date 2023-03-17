<?php

namespace App\Exports;

use App\Models\Country;
use App\Models\State;
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

class BusinessLocationExport extends DefaultValueBinder implements
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
    protected $columns = ['E', 'F', 'G', 'H', 'I'];
    protected $location_types = ['head office','service branch','branch','none physical location'];
    protected $column_name = [
        'State',
        'Company',
        'Country',
        'Department',
        'Location Type',
    ];
    protected $sheet_names = [
        'states',
        'companies',
        'countries',
        'departments',
    ];
    protected $lengths = [];
    protected $companies = [];
    protected $status = '';
    protected $departments = [];

    public function __construct($companies, $init_status, $departments)
    {
        $this->status = $init_status;
        $this->companies = $companies;
        $this->departments = $departments;
        $this->lengths[] = State::all()->count();
        $this->lengths[] = $this->companies->count();
        $this->lengths[] = Country::whereStatus($init_status)->get()->count();
        $this->lengths[] = $this->departments->count();
    }

    public function title(): string
    {
        return 'business-locations';
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets['business-locations'] = new BusinessLocationExport($this->companies, $this->status, $this->departments);
        $sheets['states'] = new StateSheetExport();
        $sheets['companies'] = new CompanySheetExport($this->companies);
        $sheets['countries'] = new CountrySheetExport($this->status);
        $sheets['departments'] = new DepartmentSheetExport($this->departments);
        return $sheets;
    }

    public function headings(): array{
        return [
            'Name',
            'Email',
            'Map_link',
            'Address',
            'State',
            'Company',
            'Country',
            'Department',
            'Location_type',
        ];
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $event->sheet->getStyle('A5:I5')->applyFromArray([
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
                            case 'Location Type': {
                                $objValidation->setFormula1('"'.implode(', ', $this->location_types).'"');
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
        $sheet->getStyle('J1:J2000')->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
        $sheet->getProtection()->setSheet(true);
        $sheet->getStyle('A6:I2000')->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');
        $sheet->mergeCells('A3:I3');
        $sheet->setCellValue('A4', 'Business Locations Bulk Upload Template');
        $alignment = [
            'vertical' => Alignment::VERTICAL_CENTER,
            'horizontal' => Alignment::HORIZONTAL_CENTER,
        ];
        $sheet->getStyle('A4:I4')->applyFromArray([
            'alignment' => $alignment,
            'font' => ['bold' => true, 'size' => 20],
            'fill' => [
                'fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '#FFFFFF']
            ],
        ]);
        $sheet->getStyle('A5:I5')->applyFromArray([
            'alignment' => $alignment,
            'font' => ['bold' => true],
        ]);
        $headerStyleArray = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => $alignment,
            'font' => ['bold' => true],
        ];
        $sheet->getStyle('A6:I2000')->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
            'alignment' => $alignment,
        ]);
        $sheet->getStyle('A5:I5')->applyFromArray($headerStyleArray);
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
            'B' => '26',
            'C' => '55',
            'D' => '26',
            'E' => '26',
            'F' => '26',
            'G' => '26',
            'H' => '26',
            'I' => '26',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A1:A2000' => NumberFormat::FORMAT_TEXT,
            'B1:B2000' => NumberFormat::FORMAT_TEXT,
            'C1:C2000' => NumberFormat::FORMAT_TEXT,
            'D1:D2000' => NumberFormat::FORMAT_TEXT,
            'E1:E2000' => NumberFormat::FORMAT_TEXT,
            'F1:F2000' => NumberFormat::FORMAT_TEXT,
            'G1:G2000' => NumberFormat::FORMAT_TEXT,
            'H1:H2000' => NumberFormat::FORMAT_TEXT,
            'I1:I2000' => NumberFormat::FORMAT_TEXT,
        ];
    }

}
