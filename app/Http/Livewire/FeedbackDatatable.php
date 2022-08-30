<?php

namespace App\Http\Livewire;
use Livewire\WithPagination;
use App\Models\Feedback;
use Livewire\Component;
use App\Exports\FeedbackExport;

class FeedbackDatatable extends Component
{
    use WithPagination;

    public $name,$rate,$feedback,$suggestion,$user_feedback;

    public $sortBy = 'id';
 
    public $sortDirection = 'asc';
    public $perPage = '10';
    public $search = '';

    public $showMode = false;
    public $open = false;


    public function render()
    {
        $feedbacks = Feedback::query()
                 ->search($this->search)
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        return view('livewire.feedback-datatable', [
            'feedbacks' => $feedbacks
        ]);
    }

    public function show($id)
    {
        $this->user_feedback = Feedback::find($id);
        $this->showMode = true;
    
    }

    public function store()
    {   
        $user_feedback = new Feedback;
        $user_feedback->rate = $this->rate;
        $user_feedback->name = $this->name;
        $user_feedback->email = $this->email;
        $user_feedback->feedback = $this->feedback;
        $user_feedback->suggestion = $this->suggestion;
        $user_feedback->save();
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
        return (new FeedbackExport())->download('feedback.xlsx');
    }

    public function pdfexport()
    {
        return (new FeedbackExport())->download('feedback.pdf');
    }

    public function csvexport()
    {
        return (new FeedbackExport())->download('feedback.csv');
    }
    public function updatingSearch()
    { 
         $this->resetPage(); 
    }
}
