<?php

namespace App\Exports;

use App\Models\Label;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class LabelSheetExport implements FromCollection, withMapping, withTitle
{

    protected $labels = [];

    public function __construct($labels)
    {
        $this->labels = $labels;
    }

    public function title(): string
    {
        return 'labels';
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(): \Illuminate\Support\Collection
    {
        return $this->labels;
    }

    public function map($label): array
    {
        return [
            $label->title
        ];
    }
}
