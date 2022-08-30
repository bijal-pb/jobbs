<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserDocument;
use App\Models\user;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.document.index');
    }

}
