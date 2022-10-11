<?php

namespace App\Http\Controllers\Admin;

use App\Site;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Response;
use Session;

class SitesController extends Controller
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
       $sites =Site::query();
       if (session()->has('sites.county')) {
          $sites->where('county','LIKE','%' . session('sites.county') .'%');
       }
       if (session()->has('sites.site_name')) {
          $sites->where('name','LIKE','%' . session('sites.site_name') .'%');
       }

       $sites= $sites->orderBy('county')->paginate(15);
       $data = [
           'sites' => $sites,
       ];
       return view('admin.sites.index',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data=$request->all();
        $site =  Site::create($data);
        Session::flash('message', 'New site created.');
        return redirect()->route('sites.index');
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
        $site =Site::where('id', $id)->first();
        $data = $request->all();
        $site->update($data);

        Session::flash('message', $site->name." was Updated.");
        return redirect()->route('sites.index');
    }

    public function destroy($id)
    {
        $site =Site::where('id', $id)->first();
        $site->delete();

        Session::flash('message', $site->name." was deleted.");
        return redirect()->route('sites.index');
    }

    public function setfilter (Request $request) {
        if ($request->has('county')) {
            if($request->county == '' ) {
                session()->forget('sites.county');
            } else {
                session(['sites.county' => $request->county]);
            }
        }
        if ($request->has('site_name')) {
            if($request->site_name == '' ) {
                session()->forget('sites.site_name');
            } else {
                session(['sites.site_name' => $request->site_name]);
            }
        }
        return redirect()->route('sites.index');
    }
    
    public function resetfilter (Request $request) {
        session()->forget('sites');
        return redirect()->route('sites.index');
    }
}
