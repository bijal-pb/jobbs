<?php
use Livewire\Component;
namespace App\Http\Controllers;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.service.index');
    }
}
