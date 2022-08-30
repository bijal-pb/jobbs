<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentType;
use Livewire\Component;

class DocumentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.document-type.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.document-type.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
           
        ]);

       

        $doctype = new DocumentType;
        $doctype->name = $request->name;
        $doctype->description = $request->description;
        $doctype->save();
        
        return redirect()->route('document_type.index')->with('message','Document type added Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $doctype = DocumentType::find($id);
        return view('admin.document-type.edit',compact('doctype'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
           
        ]);
    
        $doctype = DocumentType::find($id);
        $doctype->name = $request->input('name');
        $doctype->description = $request->input('description');
        $doctype->save(); 
        // $this->dispatchBrowserEvent('alert', 
        //         ['type' => 'success',  'message' => 'Content updated successfully!']);
        return redirect()->route('document_type.index')
                        ->with('message','Document type updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $doctype = DocumentType::find($id);
        $doctype->delete();
        //notify()->success('Content deleted successfully');
        // return redirect()->back();
        return redirect()->route('livewire.document-type')
                         ->with('success','Document type deleted successfully');
        //return response()->json(['success'=>'User deleted successfully.']);
    }
}
