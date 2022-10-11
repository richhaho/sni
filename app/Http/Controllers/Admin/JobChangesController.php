<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Job;
use App\JobChangeOrder;
use Session;
use Auth;
use Storage;
use Response;


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
        //  $this->validate($request, [
        //     'number' => 'required',
        //     'added_on' => 'required',
        //     'amount' => 'required|numeric',
        // ]);

        if ($request['number']==null || $request['number']=="" || $request['added_on']==null || $request['added_on']=="" || $request['amount']==null || $request['amount']=="") {
            Session::flash('message', 'number, added_on and amount are required.');
            return Auth::user()->restricted ? redirect()->to(route('research.edit',$job_id) .'?#changes'):redirect()->to(route('jobs.edit',$job_id) .'?#changes');

        }

        $f = $request->file('attached_file');
        if (isset($request['attached_file'])) {
            $max_uploadfileSize= min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
            $max_uploadfileSize= substr($max_uploadfileSize, 0, -1)*1024*1024;
            if ($f->getSize()>$max_uploadfileSize){
                Session::flash('message', 'Attached file is too large to upload.');
                return Auth::user()->restricted ? redirect()->to(route('research.edit',$job_id) .'?#changes'):redirect()->to(route('jobs.edit',$job_id) .'?#changes');
            }
        }
        
        
        
        $change = new JobChangeOrder;
        $change->number = $request->number;
        $change->added_on = date('Y-m-d', strtotime($request->added_on));
        $change->amount = $request->amount;
        $change->description = $request->description;
        $change->job_id = $job_id;
        $change->save();
        if (isset($request['attached_file'])) {
            $xfilename = $change->id . "." . $f->guessExtension();
            $xpath = 'attachments/job_changes/';
            $f->storeAs($xpath,$xfilename);
            $change->attached_file = $f->getClientOriginalName();
            $change->file_mime = $f->getMimeType();
            $change->file_path = $xpath.$xfilename;
            $change->save();
        }

        Session::flash('message', 'New Change Order created');
    
        return Auth::user()->restricted ? redirect()->to(route('research.edit',$job_id) .'?#changes'):redirect()->to(route('jobs.edit',$job_id) .'?#changes');
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
        return Auth::user()->restricted ? redirect()->to(route('research.edit',$job_id) .'?#changes')->with('change', $change) : redirect()->to(route('jobs.edit',$job_id) .'?#changes')->with('change', $change);
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

        $f = $request->file('attached_file');
        if (isset($request['attached_file'])) {
            $max_uploadfileSize= min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
            $max_uploadfileSize= substr($max_uploadfileSize, 0, -1)*1024*1024;
            if ($f->getSize()>$max_uploadfileSize){
                Session::flash('message', 'Attached file is too large to upload.');
                return Auth::user()->restricted ? redirect()->to(route('research.edit',$job_id) .'?#changes'):redirect()->to(route('jobs.edit',$job_id) .'?#changes');
            }
        }

        $job =Job::findOrFail($job_id);

        $change = JobChangeOrder::findOrFail($id);
        $change->number = $request->number;
        $change->added_on = date('Y-m-d', strtotime($request->added_on));
        $change->amount = $request->amount;
        $change->description = $request->input('description');
        $change->save();
        if (isset($request['attached_file'])) {
            $xfilename = $change->id . "." . $f->guessExtension();
            $xpath = 'attachments/job_changes/';
            $f->storeAs($xpath,$xfilename);
            $change->attached_file = $f->getClientOriginalName();
            $change->file_mime = $f->getMimeType();
            $change->file_path = $xpath.$xfilename;
            $change->save();
        }

        Session::flash('message', 'Change Order Updated');
       
        return Auth::user()->restricted ? redirect()->to(route('research.edit',$job_id) .'?#changes'):redirect()->to(route('jobs.edit',$job_id) .'?#changes');
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
        
        return Auth::user()->restricted ? redirect()->to(route('research.edit',$job_id) .'?#changes'):redirect()->to(route('jobs.edit',$job_id) .'?#changes');
    }

    public function showattachment($id) {
        $change = JobChangeOrder::findOrFail($id);
        $contents = Storage::get($change->file_path);
        $response = Response::make($contents, '200',[
            'Content-Type' => $change->file_mime,
            'Content-Disposition' => 'attachment; filename="' . $change->attached_file . '"',
            ]);
        return $response;
    }
}
