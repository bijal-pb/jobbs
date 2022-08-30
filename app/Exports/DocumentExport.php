<?php

namespace App\Exports;

use App\Models\UserDocument;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

class DocumentExport implements FromQuery
{
    use Exportable;
    protected $documnets;

    public function _construct($documnets)
    {
        $this->documnets = $documnets;
    }
    
    public function query()
    {
        return UserDocument::query();
    }
}
