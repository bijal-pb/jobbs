<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserRequests;

class CustomerRequestController extends Controller
{
    public function index()
    {
        return view('admin.customer-request.index');
    }
}
