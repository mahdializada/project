<?php

namespace App\Exports;


use Illuminate\Support\Facades\DB;
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
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Str;

class LanguageExport extends DefaultValueBinder implements
    WithHeadings,
    shouldAutoSize,
    withTitle,
    withDrawings,
    withCustomStartCell,
    WithStyles,
    WithColumnWidths,
    WithColumnFormatting,
    WithMapping,
    FromCollection
{
    protected $baseLanguageId = '';
    protected $languageFetchedId = '';
    protected $baseLang = '';
    protected $transLang = '';
    protected $lang_length = '';

    public function __construct($baseLanguageId, $languageFetchedId, $baseLang, $transLang)
    {
        $this->baseLanguageId = $baseLanguageId;
        $this->languageFetchedId = $languageFetchedId;
        $this->baseLang = $baseLang;
        $this->transLang = $transLang;
    }
   
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(): \Illuminate\Support\Collection
    {
        $baseLangPhrases = DB::table('phrases')
            ->leftJoin('language_phrases as lp', 'phrases.id', '=', 'lp.phrase_id')
            ->select('*')
            ->where('lp.translated_language_id', $this->baseLanguageId)
            ->get();
        $this->lang_length = $baseLangPhrases->count();
        $translatedLangPhrases = DB::table('phrases')
            ->leftJoin('language_phrases as lp', 'phrases.id', '=', 'lp.phrase_id')
            ->select('*')
            ->where('lp.translated_language_id', $this->languageFetchedId)
            ->get();
        $finalData = [];
        foreach ($baseLangPhrases as $value) {
            array_push($finalData, [
                'phrase' => $value->phrase, 
                'base_lang' => $value->translation, 
                'translated_lang' => $this->findInArray($value->phrase, $translatedLangPhrases)
            ]);
        }
        return collect($finalData);
    }

    function findInArray($phrase, $arr){
        foreach ($arr as $value) {
           if($value->phrase == $phrase){
                return $value->translation;
           }
        }
    }

    public function title(): string
    {
        return 'languages';
    }

    public function map($lang): array
    {
        return [
            $lang['phrase'],
            $lang['base_lang'],
            $lang['translated_lang'],
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

    public function headings(): array
    {
        return [
            'Phrase',
            $this->baseLang,
            $this->transLang, 
        ];
    } 
    
    public function columnFormats(): array
    {
        return [
            'A1:A2000' => NumberFormat::FORMAT_TEXT,
            'B1:B2000' => NumberFormat::FORMAT_TEXT,
            'C1:C2000' => NumberFormat::FORMAT_TEXT,
        ];
    }  
    
    public function styles(Worksheet $sheet): array
    {
        $sheet->getProtection()->setPassword(Str::uuid());
        $sheet->getStyle('D1:D2000')->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
        $sheet->getStyle('A1:B2000')->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
        $sheet->getStyle('C'.($this->lang_length+5).':C2000')->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
        $sheet->getProtection()->setSheet(true);
        $sheet->getStyle('C4:C'.($this->lang_length+5))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
        $sheet->mergeCells('A1:C1');
        $sheet->mergeCells('A2:C2');
        $sheet->mergeCells('A3:C3');
        $sheet->setCellValue('A4', 'Language Bulk Upload Template');
        $alignment = [
            'vertical' => Alignment::VERTICAL_CENTER,
            'horizontal' => Alignment::HORIZONTAL_CENTER,
        ];
        $sheet->getStyle('A4:C4')->applyFromArray([
            'alignment' => $alignment,
            'font' => ['bold' => true, 'size' => 20],
            'fill' => [
                'fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '#FFFFFF']
            ],
        ]);
        $sheet->getStyle('A5:C5')->applyFromArray([
            'alignment' => $alignment,
            'font' => ['bold' => true],
        ]);
        $headerStyleArray = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => [Alignment::VERTICAL_CENTER],
            'font' => ['bold' => true],
        ];
        $sheet->getStyle('A6:C'.($this->lang_length+5))->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
            'alignment' => [Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle('A5:C5')->applyFromArray($headerStyleArray);
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
            'A' => '60',
            'B' => '60',
            'C' => '60',
        ];
    }    
}
