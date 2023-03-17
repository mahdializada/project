<?php

namespace App\Exports;

use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;;

class CompanySheetExport implements FromCollection, withMapping, withTitle
{
    protected $status = '';
    protected $companies = [];

    public function __construct($companies)
    {
        $this->companies = $companies;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->companies;
    }

    public function title(): string
    {
        return 'companies';
    }

    public function map($company): array
    {
        return [
            $company->name
        ];
    }
}
