<?php

namespace App\Exports;

use App\Models\State;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class StateSheetExport implements FromCollection, WithTitle, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return State::orderBy('name', 'asc')->get();
    }

    public function title(): string
    {
        return 'states';
    }

    public function map($state): array
    {
        return [
            $state->name
        ];
    }
}
