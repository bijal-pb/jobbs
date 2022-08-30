<?php

namespace App\Exports;

use App\Models\Feedback;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

class FeedbackExport implements FromQuery
{
    use Exportable;
    protected $feedbacks;

    public function _construct($feedbacks)
    {
        $this->feedbacks = $feedbacks;
    }
    
    public function query()
    {
        return Feedback::query();
    }
}
