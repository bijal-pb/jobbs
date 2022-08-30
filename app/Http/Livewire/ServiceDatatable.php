<?php


namespace App\Http\Livewire;

use App\Exports\ServiceExport;
use Livewire\Component;
use App\Models\Service;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Auth;
use Livewire\WithFileUploads;


class ServiceDatatable extends Component
{
    use WithPagination;
    use WithFileUploads;

    public  $service,$service_id,$name,$icon,$detail,$img,$newimage,$image_type;

    public $sortBy = 'id';
    public $confirming;

    public $updateMode = false;
    public $createMode = false;

    public $sortDirection = 'desc';
    public $perPage = '10';
    public $search = '';

    public $editModel = false;
    public $deleteModel = false;

    public $editService;
    public $deleteId = null;

    public $open = false;


    public function render()
    {
        $services = Service::query()
        ->search($this->search)
        ->orderBy($this->sortBy, $this->sortDirection)
        ->paginate($this->perPage);
      return view('livewire.service-datatable', [
    'services' => $services 
    ]);
  }

  private function resetInputFields()
  {
      $this->name =  '';
      $this->icon =  '';
      $this->detail =  '';
  }

  public function create()
  {
    $this->createMode = true;
    $this->resetInputFields();
  }

  public function store()
    {     
        
        $this->validate([
           'name' => 'required',
           'icon' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            // 'detail' => 'required',
        ]);

       // $filename = $this->icon->store('serviceimages','public');
        //$filename = str_replace("serviceimages/", "", $filename);
        $service = new Service;
        $service->name = $this->name;
        if($this->icon != null){
            $filename = $this->icon->store('serviceimages','public');
            $filename = str_replace("serviceimages/", "", $filename);
            $service->icon = $filename;
        }
       // $service->icon = $filename;
        $service->detail = $this->detail;
        $service->save();
        $this->createMode = false;
        $this->dispatchBrowserEvent('alert', 
        ['type' => 'success', 'message' => ' Service type created successfully!']);
    }
    public function edit($id)
    { 
        $this->service_id = $id;
        $service = Service::find($id);
        $this->name = $service->name;
        $this->icon = null;
        $this->detail = $service->detail;
        $this->updateMode = true;
   }

    public function update()
    {   
        
        $this->validate([
            'name' => 'required',
            'icon' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);
      
        $service = Service::find($this->service_id);
        $service->name = $this->name;
        if($this->icon != null){
            $filename = $this->icon->store('serviceimages','public');
            $filename = str_replace("serviceimages/", "", $filename);
            $service->icon = $filename;
        }
        $service->detail = $this->detail;
        $service->save();
        $this->updateMode = false;
        $this->dispatchBrowserEvent('alert', 
        ['type' => 'success',  'message' => ' Service updated successfully!']);
      
}

public function cancel()
{
    $this->createMode = false;
    $this->updateMode = false;
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

public function confirmDelete($id)
{
    $this->deleteId = $id;
    $this->deleteModel = true;
}

public function closeDeleteModel()
{
    $this->deleteModel = false;
 
}

public function serviceDelete()
{
    $service = Service::find($this->deleteId);
    $service->delete();
    $this->deleteId = null;
    $this->deleteModel = false;
    $this->dispatchBrowserEvent('alert', 
    ['type' => 'success',  'message' => ' Service deleted successfully!']);
}
public function updatingSearch()
{ 
     $this->resetPage(); 
}
public function exportSelected()
{
    return (new ServiceExport())->download('service.xlsx');
}

public function pdfexport()
{
    return (new ServiceExport())->download('service.pdf');
}

public function csvexport()
{
    return (new ServiceExport())->download('service.csv');
}
}

