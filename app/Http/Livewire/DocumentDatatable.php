<?php

namespace App\Http\Livewire;
use App\Models\UserDocument;
use Livewire\WithPagination;
use App\Exports\DocumentExport;
use Livewire\WithFileUploads;
use Livewire\Component;

class DocumentDatatable extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $user_doc,$user_id,$document_type_id,$status,$first_name,$last_name,$name;

    public $sortBy = 'id';

    public $sortDirection = 'desc';
    public $perPage = '10';
    public $search = '';

    public $approveMode = false;
    public $showMode = false;
    public $open = false;


    public function render()
    {
        $documents = UserDocument::select('user_documents.*','users.first_name as user_name')
                                ->join('users', 'user_documents.user_id', 'users.id')
                 ->search($this->search)
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        return view('livewire.document-datatable', [
            'documents' => $documents
        ]);
    }

    public function show($id)
    {
        $this->user_doc = UserDocument::with(['uploadby','documentname'])->where('id',$id)->first();
        $this->showMode = true;
    }
    
    public function store()
    {   
        $this->validate([
          
            'document' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $filename = $this->document->store('documentimages','public');
        $filename = str_replace("documentimages/", "", $filename);
        
        $user_doc = new UserDocument;
        $user_doc->user_id = $this->user_id;
        $user_doc->document_type_id = $this->document_type_id;
        $user_doc->document = $filename;
        $user_doc->status = $this->status;
        $user_doc->first_name = $this->first_name;
        $user_doc->last_name = $this->last_name;
        $user_doc->name = $this->name;
        $user_doc->save();
        $this->showMode = false;
    }

    public function approve($id)
    {
        $user_doc = UserDocument::find($id);
        $user_doc->status = 1;
        $user_doc->save();
        $this->dispatchBrowserEvent('alert', 
        ['type' => 'success',  'message' => 'Document approved successfully!']);

    }
    public function disApprove($id)
    {
        $user_doc = UserDocument::find($id);
       $user_doc->status = 2;
        $user_doc->save();
        $this->dispatchBrowserEvent('alert', 
        ['type' => 'success',  'message' => ' Document disapproved successfully!']);
        
    }


    public function cancel()
    {
        $this->showMode = false;
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
        return (new DocumentExport())->download('document.xlsx');
    }
    public function pdfexport()
    {
        return (new DocumentExport())->download('document.pdf');
    }

    public function csvexport()
    {
        return (new DocumentExport())->download('document.csv');
    }
    public function updatingSearch()
    { 
         $this->resetPage(); 
    }

}
