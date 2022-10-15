<?php

namespace App\Http\Controllers\Clients;

use App\Folder;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Response;
use Session;

class FoldersController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $folders = Folder::query();
        $folders = $folders->orderBy('id', 'desc')->paginate(15);
        $client = Auth::user()->client;
        $data = [
            'folders' => $folders,
            'client_type' => $client->service ? $client->service : 'full',
        ];

        return view('client.folders.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data['created_at'] = date('Y-m-d H:i:s');
        $folder = Folder::create($data);
        Session::flash('message', 'New folder created.');

        return redirect()->route('client.folders.index');
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
        $folder = Folder::where('id', $id)->first();
        $data = $request->all();
        $data['updated_at'] = date('Y-m-d H:i:s');
        $folder->update($data);

        Session::flash('message', $folder->name.' was Updated.');

        return redirect()->route('client.folders.index');
    }

    public function destroy($id)
    {
        $folder = Folder::where('id', $id)->first();
        $folder->delete();

        Session::flash('message', $folder->name.' was deleted.');

        return redirect()->route('client.folders.index');
    }
}
