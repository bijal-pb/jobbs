<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Models\Service;


class ServiceExport implements FromQuery
{
    use Exportable;
    protected $service;

    public function _construct($service)
    {
        $this->service = $service;
    }
    
    public function query()
    {
        return Service::query();
    }
}
