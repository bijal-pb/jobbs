<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\UserRequests;
use App\Models\User;
use App\Models\UserService;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.order.index');
    }
}
