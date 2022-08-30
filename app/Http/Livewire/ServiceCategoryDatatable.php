<?php
namespace App\Http\Livewire;

use App\Exports\ServicecategoryExport;
use Livewire\Component;
use App\Models\ServiceCategories;
use App\Models\Service;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Livewire\WithFileUploads;
use Exception;

class ServiceCategoryDatatable extends Component
{
    use WithPagination;
    use WithFileUploads;

    public  $ServiceCategorires,$service_categories_id,$serviceId,$services,$service,$name,$icon,$detail,$img,$newimage,$image_type,$service_id;

    public $sortBy = 'id';
    public $confirming;

    public $updateMode = false;
    public $createMode = false;

    public $sortDirection = 'desc';
    public $perPage = '10';
    public $search = '';

    public $editModel = false;
    public $deleteModel = false;

    public $editServicecategory;
    public $deleteId = null;

    public $open = false;


    public function render()
    { 
        $this->services =  Service::all();
        $servicecategories = ServiceCategories::select('service_categories.*','services.name as service_name')
        ->join('services', 'service_categories.service_id', 'services.id')
        ->search($this->search)
        ->orderBy($this->sortBy, $this->sortDirection)
        ->paginate($this->perPage);
         return view('livewire.service-category-datatable', [
        'servicecategories' => $servicecategories 
     ]);
  }
  private function resetInputFields()
  {
      $this->name =  '';
      $this->icon =  '';
      $this->detail = '';
  }
  public function create()
  {     
    $this->createMode = true;
    $this->resetInputFields();
  }
     public function store()
    {     
            $this->validate([
            'serviceId' => 'nullable|required',
            'name' => 'required',
            'icon' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            // 'detail' => 'required',
        ]);
       try{
        $service_category = new ServiceCategories;
        $service_category->service_id = $this->serviceId;
        $service_category->name = $this->name;
        if($this->icon != null){
            $filename = $this->icon->store('img','public');
            $filename = str_replace("img/", "", $filename);
            $service_category->icon = $filename;
        }
        $service_category->detail = $this->detail;
        $service_category->save();
        $this->createMode = false;
       
        $this->dispatchBrowserEvent('alert', 
        ['type' => 'success',  'message' => ' Service Category created successfully!']);
       }catch(Exception $e)
       {
        $this->dispatchBrowserEvent('alert', 
        ['type' => 'error',  'message' => $e->getMessage()]);
       }
    }
    public function edit($id)
    { 
        $this->service_categories_id = $id;
        $service_category = ServiceCategories::find($id);
        $this->serviceId = $service_category->service_id;
        $this->name = $service_category->name;
        $this->icon = null;
        $this->detail = $service_category->detail;
        $this->updateMode = true;
   }

    public function update()
    {   
         $this->validate([
            'serviceId' => 'nullable|required',
            'name' => 'required',
            'icon' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);
        $service_category = ServiceCategories::find($this->service_categories_id);
        $service_category->name = $this->name;
        if($this->icon != null){
            $filename = $this->icon->store('img','public');
            $filename = str_replace("img/", "", $filename);
            $service_category->icon = $filename;
        }
        $service_category->detail = $this->detail;
        if($this->serviceId != null)
        {
            $service_category->service_id = $this->serviceId;
        }
        $service_category->save();
        $this->updateMode = false;
        $this->dispatchBrowserEvent('alert', 
        ['type' => 'success',  'message' => ' Service Category updated successfully!']);
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

public function servicecategoryDelete()
{
    $service_category = ServiceCategories::find($this->deleteId);
    $service_category->delete();
    $this->deleteId = null;
    $this->deleteModel = false;
    $this->dispatchBrowserEvent('alert', 
    ['type' => 'success',  'message' => ' Service Category deleted successfully!']);
}
public function updatingSearch()
{ 
     $this->resetPage(); 
}
public function exportSelected()
{
    return (new ServicecategoryExport())->download('servicecategory.xlsx');
}

public function pdfexport()
{
    return (new ServiceCategoryExport())->download('servicecategory.pdf');
}

public function csvexport()
{
    return (new ServiceCategoryExport())->download('servicecategory.csv');
}
}


