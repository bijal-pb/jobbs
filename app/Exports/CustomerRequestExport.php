<?php

namespace App\Exports;

use App\Models\UserRequests;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

class CustomerRequestExport implements FromQuery
{
    use Exportable;
    protected $customerrequests;

    public function _construct($customerrequests)
    {
        $this->customerrequests = $customerrequests;
    }
    
    public function query()
    {
        return UserRequests::query();
    }
}
