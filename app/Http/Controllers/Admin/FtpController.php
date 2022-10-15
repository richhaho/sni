<?php

namespace App\Http\Controllers\Admin;

use App\FtpLocation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;

class FtpController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $paths = FtpLocation::orderBy('connection_id')->get();
        $servers = \App\FtpConnection::all()->pluck('ftp_name', 'id')->toArray();
        $data = [
            'servers' => $servers,
            'paths' => $paths,
        ];

        return view('admin.ftp.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'name' => 'required',
            'path' => 'required',
        ]);

        $path = FtpLocation::create($request->all());

        Session::flash('message', 'Successfully added FTP Location');

        return redirect()->route('ftp.index');
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $path = FtpLocation::findOrFail($id);

        $path->delete();

        Session::flash('message', 'Successfully deleted FTP Location');

        return redirect()->route('ftp.index');
    }
}
