<?php

namespace App\Http\Livewire;

use App\Exports\UsersExport;
use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Auth;


class UserDatatable extends Component
{
    use WithPagination;

    public $user_id, $first_name, $last_name, $email, $country_code, $phone, $is_banned;

    public $sortBy = 'id';
    public $confirming;

    public $updateMode = false;

    public $sortDirection = 'desc';
    public $perPage = '10';
    public $search = '';

    public $editModel = false;
    public $deleteModel = false;

    public $editUser;
    public $deleteId = null;

    public $open = false;


    public function render()
    {
        $users = User::query();
        if(Auth::id() == 2){
            $users = $users->whereHas('roles', function($q){
                $q->whereIn('name', ['admin','user','developer']);
            });
        } else {
            $users = $users->whereHas('roles', function($q){
                $q->whereIn('name', ['admin','user']);
            });
        }
        $roles = Role::where('name','!=','developer')->pluck('name','name')->all();
        // $userRole = $user->roles->pluck('name')->all();

        $users = $users->search($this->search)
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        return view('livewire.user-datatable', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    public function edit($id)
    {
        $this->user_id = $id;
        $user = User::find($id);
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->country_code = $user->country_code;
        $this->phone = $user->phone;
        $this->is_banned = $user->is_banned;
        $this->updateMode = true;
    }

    public function update()
    {   
      
        $user = User::find($this->user_id);
        $user->is_banned = $this->is_banned;
        $user->save();
        $user->tokens()->delete();
        $this->updateMode = false;
        $this->dispatchBrowserEvent('alert', 
        ['type' => 'success',  'message' => ' User updated successfully!']);
    }

    public function cancel()
    {
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

    public function userDelete()
    {
        $user = User::find($this->deleteId);
        $user->delete();
        $this->deleteId = null;
        $this->deleteModel = false;
        $this->dispatchBrowserEvent('alert', 
        ['type' => 'success',  'message' => ' User deleted successfully!']);
    }

    public function exportSelected()
    {
        return (new UsersExport())->download('users.xlsx');
    }

    public function pdfexport()
    {
        return (new UsersExport())->download('users.pdf');
    }

    public function csvexport()
    {
        return (new UsersExport())->download('users.csv');
    }
    public function updatingSearch()
    { 
         $this->resetPage(); 
    }
    
}