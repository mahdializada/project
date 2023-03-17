<?php

/**
 * @author [Ahmad Reza Azimi]
 * @email [teebalhoor@info.org]
 * @create date 2022-05-26 10:56:36
 * @modify date 2022-05-26 10:56:36
 * @desc [description]
 * all reserved for Teeb-al-Hoor Commercial Broker 
 */

namespace App\Exports;

use App\Models\Country;
use App\Models\Language;
use App\Models\Role;
use App\Models\State;
use App\Models\Team;
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

class UserExport extends DefaultValueBinder implements
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
    protected $schedule_type = [];
    protected $columns = ['M', 'L', 'O', 'H', 'Q', 'R', 'N', 'P', 'V'];
    protected $column_name = [
        'Country',
        'Current_Country',
        'Language',
        'Gender',
        'Role',
        'Company',
        'State',
        'Team',
    ];
    protected $sheet_names = [
        'countries',
        'states',
        'languages',
        'teams',
        'roles',
        'companies'
    ];
    protected $lengths = [];
    protected $gender = [];
    protected $companies = [];

    public function __construct($init_status, $gender, $schedule_type, $companies)
    {
        $this->init_status = $init_status;
        $this->gender = $gender;
        $this->schedule_type = $schedule_type;
        $this->companies = $companies;
        $this->lengths[] = Country::whereStatus($init_status)->get()->count();
        $this->lengths[] = State::all()->count();
        $this->lengths[] = Language::groupBy('name')->get()->count();
        $this->lengths[] = Team::whereStatus($init_status)->get()->count();
        $this->lengths[] = Role::whereStatus($init_status)->get()->count();
        $this->lengths[] = collect($this->companies)->count();
    }

    public function title(): string
    {
        return 'users';
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets['users'] = new UserExport($this->init_status, $this->gender, $this->schedule_type, $this->companies);
        $sheets['countries'] = new CountrySheetExport($this->init_status);
        $sheets['states'] = new StateSheetExport();
        $sheets['languages'] = new LanguageSheetExport();
        $sheets['teams'] = new TeamSheetExport($this->init_status);
        $sheets['roles'] = new RoleSheetExport($this->init_status);
        $sheets['companies'] = new CompanySheetExport($this->companies);
        return $sheets;
    }

    public function headings(): array
    {
        return [
            'Firstname',
            'Lastname',
            'Username',
            'Phone',
            'Whatsapp',
            'Email',
            'Password',
            'Gender',
            'Birth_date',
            'Address',
            'Note',
            'Current_Country',
            'Country',
            'State',
            'Language',
            'Team',
            'Role',
            'Company',
        ];
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $event->sheet->getStyle('A5:R5')->applyFromArray([
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
                        switch ($this->column_name[$i]) {
                            case 'Current_Country': {
                                    $index = $this->lengths[$i - 1] == 0 ? 1 : $this->lengths[$i - 1];
                                    $objValidation->setFormula1($this->sheet_names[$i - 1] . '!$A$1:$A$' . $index);
                                }
                                break;
                            case 'Gender': {
                                    $objValidation->setFormula1('"' . implode(', ', $this->gender) . '"');
                                }
                                break;
                            default: {
                                    if ($this->column_name[$i]  === 'Team') {
                                        $index = $this->lengths[$i - 4] == 0 ? 1 : $this->lengths[$i - 4];
                                        $objValidation->setFormula1($this->sheet_names[$i - 4] . '!$A$1:$A$' . $index);
                                    } else if ($this->column_name[$i] === 'State') {
                                        $index = $this->lengths[$i - 5] == 0 ? 1 : $this->lengths[$i - 5];
                                        $objValidation->setFormula1($this->sheet_names[$i - 5] . '!$A$1:$A$' . $index);
                                    } else {
                                        $index = $this->lengths[$i] == 0 ? 1 : $this->lengths[$i];
                                        $objValidation->setFormula1($this->sheet_names[$i] . '!$A$1:$A$' . $index);
                                    }
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
        $sheet->getStyle('S1:S2000')->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
        $sheet->getProtection()->setSheet(true);
        $sheet->getStyle('A6:R2000')->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
        $sheet->mergeCells('A1:R1');
        $sheet->mergeCells('A2:R2');
        $sheet->mergeCells('A3:R3');
        $sheet->getColumnDimension('A')->setWidth(45);
        $sheet->setCellValue('A4', 'User Bulk Upload Template');
        $alignment = [
            'vertical' => Alignment::VERTICAL_CENTER,
            'horizontal' => Alignment::HORIZONTAL_CENTER,
        ];
        $sheet->getStyle('A4:R4')->applyFromArray([
            'alignment' => $alignment,
            'font' => ['bold' => true, 'size' => 20],
            'fill' => [
                'fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '#FFFFFF']
            ],
        ]);
        $sheet->getStyle('A5:R5')->applyFromArray([
            'alignment' => $alignment,
            'font' => ['bold' => true],
        ]);
        $headerStyleArray = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => $alignment,
            'font' => ['bold' => true],
        ];
        $sheet->getStyle('A6:R2000')->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
            'alignment' => $alignment,
        ]);
        $sheet->getStyle('A5:R5')->applyFromArray($headerStyleArray);
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
            'B' => '22',
            'C' => '22',
            'D' => '26',
            'E' => '26',
            'F' => '25',
            'G' => '25',
            'H' => '15',
            'I' => '15',
            'J' => '30',
            'k' => '40',
            'L' => '20',
            'M' => '20',
            'N' => '30',
            'O' => '25',
            'P' => '25',
            'Q' => '25',
            'R' => '25',
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
            'F1:E2000' => NumberFormat::FORMAT_TEXT,
            'G1:G2000' => NumberFormat::FORMAT_TEXT,
            'H1:H2000' => NumberFormat::FORMAT_TEXT,
            'I1:I2000' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'J1:J2000' => NumberFormat::FORMAT_TEXT,
            'K1:K2000' => NumberFormat::FORMAT_TEXT,
            'L1:L2000' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'M1:M2000' => NumberFormat::FORMAT_DATE_TIME1,
            'O1:O2000' => NumberFormat::FORMAT_TEXT,
            'P1:P2000' => NumberFormat::FORMAT_TEXT,
            'Q1:Q2000' => NumberFormat::FORMAT_TEXT,
            'R1:R2000' => NumberFormat::FORMAT_TEXT
        ];
    }
}
