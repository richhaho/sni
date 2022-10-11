<?php

namespace App\Http\Controllers\Admin;

use App\AdminEmails;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Response;
use Session;

class AdminEmailsController extends Controller
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
       $adminemails = AdminEmails::get();
       $admin_users = User::where('status',1)->isRole(['admin','researcher'])->get()->pluck('full_name', 'id')->toArray();
       $data = [
           'adminemails' => $adminemails,
           'admin_users' => $admin_users,
       ];
       return view('admin.adminemails.index',$data);
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
        $adminEmail =AdminEmails::where('id', $id)->first();
        $adminEmail->users = $request->users;
        $adminEmail->save();

        Session::flash('message', $adminEmail->name." was Updated.");
        return redirect()->route('adminemails.index');
    }
    
}
