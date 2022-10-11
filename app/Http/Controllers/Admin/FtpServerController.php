<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\FtpConnection;
use Session;

class FtpServerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $servers = FtpConnection::all();
        
        $data = [
            'servers' => $servers
        ];
        
        return view('admin.ftpservers.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.ftpservers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'ftp_name' => 'required',
            'ftp_host' => 'required',
            'ftp_user' => 'required',
            'ftp_password' => 'required',
        ]);
        
        $server = FtpConnection::create($request->all());
        Session::flash('message', 'New FTP Server have been created successfully');
        return redirect()->route('serversftp.index');
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
        $server = FtpConnection::findOrFail($id);
        $data = [
            'server' =>$server
        ];
        
        return view('admin.ftpservers.edit',$data);
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
            'ftp_name' => 'required',
            'ftp_host' => 'required',
            'ftp_user' => 'required',
            'ftp_password' => 'required',
        ]);
       $server = FtpConnection::findOrFail($id);
       $server->update($request->all());
        Session::flash('message', 'Successfully updated the server: ' . $server->ftp_name);
        
        return redirect()->route('serversftp.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $server = FtpConnection::findOrFail($id);
       
        $server->delete();

        // redirect
        Session::flash('message', 'Successfully deleted the server: ' . $server->ftp_name);
        
        return redirect()->route('serversftp.index');
    }
}
