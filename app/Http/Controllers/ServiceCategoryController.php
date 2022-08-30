<?php

use Livewire\Component;
namespace App\Http\Controllers;
use App\Models\ServiceCategories;

use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.servicecategory.index');
    }
}
