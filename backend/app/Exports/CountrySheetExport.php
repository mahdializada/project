<?php

namespace App\Exports;

use App\Models\Country;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class CountrySheetExport implements FromCollection, withMapping, withTitle
{
    protected $countryStatus = '';
    public function __construct($status)
    {
        $this->countryStatus = $status;
    }

    public function title(): string
    {
        return 'countries';
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(): \Illuminate\Support\Collection
    {
        return Country::whereStatus($this->countryStatus)->orderBy('name', 'asc')->get();
    }

    public function map($country): array
    {
        return [
            $country->name
        ];
    }
}
