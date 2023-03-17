<?php

namespace App\Exports;

use App\Models\Language;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class LanguageSheetExport implements FromCollection, WithTitle, WithMapping
{
    public function title(): string
    {
        return 'languages';
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(): \Illuminate\Support\Collection
    {
        return Language::groupBy('name')->orderBy('name', 'asc')->get();
    }

    public function map($lang): array
    {
        return [
            $lang->name
        ];
    }
}
