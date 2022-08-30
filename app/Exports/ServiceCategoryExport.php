<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Models\ServiceCategories;

class ServiceCategoryExport implements FromQuery
{
    use Exportable;
    protected $servicecategory;

    public function _construct($servicecategory)
    {
        $this->servicecategory = $servicecategory;
    }
    
    public function query()
    {
        return ServiceCategories::query();
    }
}
