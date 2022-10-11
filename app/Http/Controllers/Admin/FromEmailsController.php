<?php

namespace App\Http\Controllers\Admin;

use App\FromEmails;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Response;
use Session;

class FromEmailsController extends Controller
{
     
    public function __construct() {
     
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
       $froms =FromEmails::get();
       $data = [
           'froms' => $froms,
       ];
       return view('admin.fromemails.index',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $from =FromEmails::where('id', $id)->first();
        $from->from_email = $request->from_email;
        $from->from_name = $request->from_name;
        $from->save();

        Session::flash('message', $from->name."'s [FROM EMAIL] was Updated.");
        return redirect()->route('fromemails.index');
    }
    
}
