<?php

namespace App\Http\Controllers\Researcher;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Job;
use App\JobChangeOrder;
use Session;


class JobChangesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($job_id)
    {
       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$job_id)
    {
         $this->validate($request, [
            'number' => 'required',
            'added_on' => 'required',
            'amount' => 'required|numeric',
        ]);
        
        
        
        $change = new JobChangeOrder;
        $change->number = $request->number;
        $change->added_on = date('Y-m-d', strtotime($request->added_on));
        $change->amount = $request->amount;
        $change->description = $request->description;
        $change->job_id = $job_id;
            
        $change->save();
        Session::flash('message', 'New Change Order created');
    
        return redirect()->to(route('jobs.edit',$job_id) .'?#changes');
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
    public function edit($job_id,$id)
    {
        $change = JobChangeOrder::findOrFail($id);
       return redirect()->to(route('jobs.edit',$job_id) .'?#changes')->with('change', $change);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$job_id, $id)
    {
        $this->validate($request, [
            'number' => 'required',
            'added_on' => 'required',
            'amount' => 'required|numeric',
        ]);
        $job =Job::findOrFail($job_id);

        $change = JobChangeOrder::findOrFail($id);
        $change->number = $request->number;
        $change->added_on = date('Y-m-d', strtotime($request->added_on));
        $change->amount = $request->amount;
        $change->description = $request->input('description');
        
        $change->save();
        Session::flash('message', 'Change Order Updated');
       
        return redirect()->to(route('jobs.edit',$job_id) .'?#changes');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($job_id,$id)
    {
       $change = JobChangeOrder::findOrFail($id);
        $change->delete();
        
        return redirect()->to(route('jobs.edit',$job_id) .'?#changes');
    }
}
