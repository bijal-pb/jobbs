<?php

namespace App\Http\Livewire;
use App\Models\UserRequests;
use App\Models\UserServices;
use App\Models\User;
use App\Exports\CustomerRequestExport;
use Livewire\WithPagination;
use Livewire\Component;

class CustomerRequestDatatable extends Component
{
    use WithPagination;

    public $from,$to,$start_time,$end_time,$start_date,$end_date,$address,$lat,$lang,$status, $customer_request;
    public $sortBy = 'id';
 
    public $sortDirection = 'asc';
    public $perPage = '10';
    public $search = '';

    public $showMode = false;
    public $open = false;


    public function render()
    {
        $customer_requests = UserRequests::query()
                 ->search($this->search)
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        return view('livewire.customer-request-datatable', [
            'customer_requests' => $customer_requests
        ]);
    }

    public function show($id)
    {
        $this->customer_request = UserRequests::with(['from_user','to_user','user_service'])->where('id',$id)->first();
        $this->showMode = true;
       // return view('admin.customer-request.show',compact('customerrequest'));
    }

    public function store()
    {   
        $customer_request = new UserRequests;
        $customer_request->from = $this->from;
        $customer_request->to = $this->to;
        $customer_request->start_date = $this->start_date;
        $customer_request->end_date = $this->end_date;
        $customer_request->start_time = $this->start_time;
        $customer_request->end_time = $this->end_time;
        $customer_request->address = $this->address;
        $customer_request->lat = $this->lat;
        $customer_request->lang = $this->lang;
        $customer_request->status = $this->status;
        $customer_request->save();
        $this->showMode = false;
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
        return (new CustomerRequestExport())->download('customer-request.xlsx');
    }

    public function pdfexport()
    {
        return (new CustomerRequestExport())->download('customer-request.pdf');
    }

    public function csvexport()
    {
        return (new CustomerRequestExport())->download('customer-request.csv');
    }
    public function updatingSearch()
    { 
         $this->resetPage(); 
    }
}
