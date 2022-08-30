<?php

namespace App\Exports;

use App\Models\DocumentType;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

class DocumentTypeExport implements FromQuery
{
    use Exportable;
    protected $doctypes;

    public function _construct($doctypes)
    {
        $this->doctypes = $doctypes;
    }
    
    public function query()
    {
        return DocumentType::query();
    }
}
