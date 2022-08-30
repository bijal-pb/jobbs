<?php

namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Models\Order;

class OrderExport implements FromQuery
{
    use Exportable;
    protected $user_orders;

    public function _construct($user_orders)
    {
        $this->user_orders = $user_orders;
    }
    
    public function query()
    {
        return Order::query();
    }
}
