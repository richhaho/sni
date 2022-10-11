<?php

namespace App\Http\Controllers\Clients;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Coordinate;
use App\Job;
use Session;
use DB;
use Auth;
use Response;

class CoordinateController extends Controller
{
    public function index()
    {
      $coordinates = Coordinate::where('client_id', Auth::user()->client_id)->where('deleted_at', null);
      if (session()->has('coordinate_filter.name')) {
          $coordinates->where('name','LIKE','%' . session('coordinate_filter.name') .'%');
      }
      $coordinatesOnJobs = Job::where('coordinate_id', '!=', null)->where('deleted_at', null)->get()->pluck('coordinate_id')->toArray();
      if (session()->has('coordinate_filter.usedonjob')) {
        if (session('coordinate_filter.usedonjob')==1) {
          $coordinates->whereIn('id', $coordinatesOnJobs);
        } else {
          $coordinates->whereNotIn('id', $coordinatesOnJobs);
        }
      }
      $coordinates = $coordinates->orderBy('id','DESC')->paginate(15);
      $usedonjob = [
        '0' => 'All',
        '1' => 'Used',
        '2' => 'Unused',
      ];
      $data = [
        'coordinates' => $coordinates,
        'usedonjob' => $usedonjob,
      ];

      return view('client.coordinates.index', $data);
    }

    public function create()
    {
       
      
       $data = [
         
         'job_types'=>$job_types,
          'job_statuses' => $job_statuses,
          
        ];
        return view('client.coordinates.create',$data);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['client_id'] = Auth::user()->client_id;
        $coordinate = Coordinate::create($data);
        Session::flash('message', 'New Coordinate ' .$coordinate->name. ' created');
        return redirect()->route('client.coordinates.index');
    }

    public function edit($id)
    {
        //return view('client.jobs.edit',$data);
    }

    public function update(Request $request, $id)
    {
        $coordinate = Coordinate::where('id', $id)->first();
        if (!$coordinate) {
          Session::flash('message', 'Coordinate does not exist.');
          return redirect()->route('client.coordinates.index');
        }
        $data = $request->all();
        $coordinate->update($data);
        Session::flash('message', 'The coordinate was updated successfully.');
        return redirect()->route('client.coordinates.index');
    }

    public function delete($id)
    {
        $coordinate = Coordinate::where('id', $id)->first();
        if (!$coordinate) {
          Session::flash('message', 'Coordinate does not exist.');
          return redirect()->route('client.coordinates.index');
        }
        $jobs = $coordinate->jobs();
        foreach ($jobs as $job) {
          $job->coordinate_id = null;
          $job->save();
        }
        $coordinate->delete();
        Session::flash('message', 'The coordinate was deleted successfully.');
        return redirect()->route('client.coordinates.index');
    }

    public function setfilter (Request $request) {
        if ($request->has('coordinate_name')) {
            if($request->coordinate_name == '' ) {
                session()->forget('coordinate_filter.name');
            } else {
                session(['coordinate_filter.name' => $request->coordinate_name]);
            }
        }
        if ($request->has('usedonjob')) {
          if($request->usedonjob == 0 || $request->usedonjob == '') {
              session()->forget('coordinate_filter.usedonjob');
          } else {
              session(['coordinate_filter.usedonjob' => $request->usedonjob]);
          }
        }
        return redirect()->route('client.coordinates.index');
    }
    
    public function resetfilter (Request $request) {
        session()->forget('coordinate_filter');
        return redirect()->route('client.coordinates.index');
    }
}
