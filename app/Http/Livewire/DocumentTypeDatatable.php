<?php

namespace App\Http\Livewire;
use Livewire\Component;
use App\Exports\DocumentTypeExport;
use App\Models\DocumentType;
use Livewire\WithPagination;


class DocumentTypeDatatable extends Component
{
    use WithPagination;

    public $name, $description,$doctype_id,$doctype;
    public $sortBy = 'id';
    public $confirming;

    public $updateMode = false;
    public $createMode = false;

    public $sortDirection = 'desc';
    public $perPage = '10';
    public $search = '';

    public $editModel = false;
    public $deleteModel = false;

    public $editDocument;
    public $deleteId = null;

    public $open = false;


    public function render()
    {
        $doctypes = DocumentType::query()
                ->search($this->search)
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        return view('livewire.document-type-datatable', [
            'doctypes' => $doctypes
        ]);
    }
    private function resetInputFields()
    {
        $this->name =  '';
        $this->description =  '';
    }

    public function create()
    {   
        
        $this->createMode = true;
        $this->resetInputFields();
    }
    public function store()
    {   
        $this->validate([
            'name'    =>    'required',
    
        ]);
        $doctype = new DocumentType;
        $doctype->name = $this->name;
        $doctype->description = $this->description;
        $doctype->save();
        $this->createMode = false;
        $this->dispatchBrowserEvent('alert', 
        ['type' => 'success',  'message' => ' Document type created successfully!']);
    }
    public function edit($id)
    {
        $this->doctype_id = $id;
        $doctype = DocumentType::find($id);
        $this->name = $doctype->name;
        $this->description = $doctype->description;
        $this->updateMode = true;
    }

    public function update()
    {   
        $doctype = DocumentType::find($this->doctype_id);
        $doctype->name = $this->name;
        $doctype->description = $this->description;
        $doctype->save();
        $this->updateMode = false;
        $this->dispatchBrowserEvent('alert', 
        ['type' => 'success',  'message' => ' Document type updated successfully!']);
    
    }

    public function cancel()
    {
        $this->updateMode = false;
        $this->createMode = false;
        
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->deleteModel = true;
    }

    public function closeDeleteModel()
    {
        $this->deleteModel = false;
    }

    public function documenttypeDelete()
    {
        $doctype = DocumentType::find($this->deleteId);
        $doctype->delete();
        $this->deleteId = null;
        $this->deleteModel = false;
        $this->dispatchBrowserEvent('alert', 
        ['type' => 'success',  'message' => ' document type deleted successfully!']);
    }
    

    public function sortBy($field)
    {
        if($this->sortDirection == 'asc') {
            $this->sortDirection = 'desc';
        } else {
            $this->sortDirection = 'asc';
        }

        return $this->sortBy = $field;
    }

    public function exportSelected()
    {
        return (new DocumentTypeExport())->download('document-type.xlsx');
    }

    public function pdfexport()
    {
        return (new DocumentTypeExport())->download('document-type.pdf');
    }

    public function csvexport()
    {
        return (new DocumentTypeExport())->download('document-type.csv');
    }
    public function updatingSearch()
    { 
         $this->resetPage(); 
    }
}
