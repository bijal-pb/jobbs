<?php

namespace App\Http\Livewire;
use App\Models\Order;
use App\Models\UserRequests;
use App\Models\UserService;
use App\Models\UserOrderRate;
use App\Exports\OrderExport;
use App\Models\User;
use Livewire\WithPagination;
use Livewire\Component;

class OrderDatatable extends Component
{
    use WithPagination;

    
    public $user_order,$from,$to,$first_name,$last_name,$rate,$userservice;
    public $sortBy = 'id';
 
    public $sortDirection = 'desc';
    public $perPage = '10';
    public $search = '';

    public $showMode = false;
    public $open = false;


    public function render()
    {
        $user_orders = Order::select('orders.*','from.first_name as from_user','to.first_name as to_user')
                 ->join('user_requests','orders.user_request_id','user_requests.id')
                ->join('users as from','user_requests.from','from.id')
                ->join('users as to','user_requests.to','to.id')
                 ->search($this->search)
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        return view('livewire.order-datatable', [
            'user_orders' => $user_orders
        ]);
    }

    public function show($id)
    {
        $this->user_order = Order::with(['userRequest'])->where('id',$id)->first();
        $this->showMode = true;
    
    }

    public function store()
    {   
        $user_order = new Order;
        $user_order->from = $this->from;
        $user_order->to = $this->to;
        $user_order->user_service = $this->user_service;
        $user_order->rate = $this->rate;
        $user_order->reach_time = $this->reach_time;
        $user_order->start_time = $this->start_time;
        $user_order->complete_time = $this->complete_time;
        $user_order->price = $this->price;
        $user_order->service_fee = $this->service_fee;
        $user_order->discount = $this->discount;
        $user_order->total_amount = $this->total_amount;
        $user_order->status = $this->status;
        $user_order->save();
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
        return (new OrderExport())->download('order.xlsx');
    }

    public function pdfexport()
    {
        return (new OrderExport())->download('order.pdf');
    }

    public function csvexport()
    {
        return (new OrderExport())->download('order.csv');
    }
    public function updatingSearch()
    { 
         $this->resetPage(); 
    }
}
