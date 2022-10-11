<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Response;
use Session;
use Storage;
use App\ContractTracker;
use App\Client;

class ContractTrackerController extends Controller
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
        $contract_trackers =ContractTracker::query();
        if (session()->has('contract_trackers_filter.name')) {
            $contract_trackers->where('name','LIKE','%' . session('contract_trackers_filter.name') .'%');
        }
        
        if (session()->has('contract_trackers_filter.client_id')) {
            $contract_trackers->where('client_id',session('contract_trackers_filter.client_id'));
        }

        $contract_trackers= $contract_trackers->orderBy('id', 'DESC')->paginate(10);

        $clients =  Client::enable()->get()->sortBy('company_name')->pluck('company_name', 'id')->prepend('All',0);
        $data = [
            'clients' => $clients,
            'contract_trackers' => $contract_trackers,
        ];
        
        return view('admin.contract_trackers.index',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'start_date' => 'required',
            'client_id' => 'required'
        ]);
        
        $data = [
            'client_id' => $request->client_id,
            'name' => $request->name,
            'start_date' => date('Y-m-d H:i:s', strtotime($request->start_date)),
        ];
        $contract_tracker = ContractTracker::create( $data);
        if ($request['contract_file']!=null && $request['contract_file']!="" ) {
            $f = $request->file('contract_file');
            if (!$this->checkFileSize($f)) {
                Session::flash('message', 'This file is too large to upload.');
                return redirect()->route('contract_trackers.index');
            }
            $xfilename = $f->getClientOriginalName();;
            $xpath = 'attachments/contract_trackers/';
            $f->storeAs($xpath,$xfilename);
            $contract_tracker->contract_file = $xpath.$xfilename;
            $contract_tracker->file_original_name = $xfilename;
            $contract_tracker->file_mime = $f->getMimeType();
            $contract_tracker->file_size = $f->getSize();
            $contract_tracker->file_extension = $f->guessExtension();
            $contract_tracker->save();
        }
        Session::flash('message', 'New Contract Tracker was created.');
        return redirect()->route('contract_trackers.index');
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
        $this->validate($request, [
            'name' => 'required',
            'start_date' => 'required',
            'client_id' => 'required'
        ]);

        $data = [
            'client_id' => $request->client_id,
            'name' => $request->name,
            'start_date' => date('Y-m-d H:i:s', strtotime($request->start_date)),
        ];

        $contract_tracker = ContractTracker::where('id',$id)->first();
        $contract_tracker->update($data);
        if ($request['contract_file']!=null && $request['contract_file']!="" ) {
            $f = $request->file('contract_file');
            if (!$this->checkFileSize($f)) {
                Session::flash('message', 'This file is too large to upload.');
                return redirect()->route('contract_trackers.index');
            }
            $xfilename = $f->getClientOriginalName();;
            $xpath = 'attachments/contract_trackers/';
            $f->storeAs($xpath,$xfilename);
            $contract_tracker->contract_file = $xpath.$xfilename;
            $contract_tracker->file_original_name = $xfilename;
            $contract_tracker->file_mime = $f->getMimeType();
            $contract_tracker->file_size = $f->getSize();
            $contract_tracker->file_extension = $f->guessExtension();
            $contract_tracker->save();
        }
        Session::flash('message', 'New Contract Tracker was updated.');
        return redirect()->route('contract_trackers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $contract_tracker = ContractTracker::where('id',$id)->first();
        if ($contract_tracker) {
            $contract_tracker->delete();
            Session::flash('message', 'The contract tracker was deleted.');
        } else {
            Session::flash('message', 'The contract tracker does not exist.');
        }
        return redirect()->route('contract_trackers.index');
    }

    public function download($id)
    {
        $contract_tracker = ContractTracker::where('id',$id)->first();
        $fileName = explode('/', $contract_tracker->contract_file);
        $contents = Storage::get($contract_tracker->contract_file);
        $response = Response::make($contents, '200',[
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$fileName[2].'"',
            ]);
        return $response;
    }

    public function checkFileSize($f) {
        $max_uploadfileSize= min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
        $max_uploadfileSize= substr($max_uploadfileSize, 0, -1)*1024*1024;
         
        if ($f->getSize()>$max_uploadfileSize){
            return false;
        }
        return true;
    }


    public function setfilter (Request $request) {
        
        if ($request->has('name')) {
           
            if($request->name == '' ) {
                session()->forget('contract_trackers_filter.name');
            } else {
                session(['contract_trackers_filter.name' => $request->name]);
            }
        }

        if ($request->has('client_id')) {
            if($request->client_id == 0 ) {
                session()->forget('contract_trackers_filter.client_id');
            } else {
                session(['contract_trackers_filter.client_id' => $request->client_id]);
            }
        }

        return redirect()->route('contract_trackers.index');
    }
    
    
    public function resetfilter (Request $request) {
        session()->forget('contract_trackers_filter');
        return redirect()->route('contract_trackers.index');
    }
}
