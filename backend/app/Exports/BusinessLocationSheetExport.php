<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class BusinessLocationSheetExport implements FromCollection, WithTitle, WithMapping
{

    protected $businessLocations = [];
    public function __construct($businessLocations)
    {
        $this->businessLocations = $businessLocations;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(): \Illuminate\Support\Collection
    {
        return $this->businessLocations;
    }

    public function title(): string
    {
        return 'business_locations';
    }

    public function map($business_location): array
    {
        return [
            $business_location->name
        ];
    }
}
