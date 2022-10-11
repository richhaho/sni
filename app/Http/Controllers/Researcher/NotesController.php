<?php

namespace App\Http\Controllers\Researcher;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Auth;
use App\Job;
use App\WorkOrder;
use App\Note;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewJobNote;
use App\Notifications\NewWorkNote;


class NotesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($type, $type_id)
    {
        return view('researcher.notes.index');
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
    public function store(Request $request,$type,$eid)
    {
        
        
        $this->validate($request, [
            'note' => 'required',
        ]);
        
        
        
        $note = New Note();
        $now = Carbon::now();
        $note->note_text = $request->input('note');
        $note->entered_at = $now->toDateTimeString();
        $note->entered_by = Auth::user()->id;
        if ($type =="jobs") {
            $xentity = Job::findOrFail($eid);
            $note->client_id = $xentity->client->id;
            $client = $xentity->client;
        } else {
            $xentity = WorkOrder::findOrFail($eid);
            $note->client_id = $xentity->job->client->id;
            $client = $xentity->job->client;
        }


        if($request->has('viewable')) {
            $note->viewable = 1;
            
        } else {
            $note->viewable = 0;
        }
        
        $note = $xentity->notes()->save($note);
        
        if($request->has('viewable')) {
            $note->viewable = 1;
            $data = [
                'note' => str_limit($note->note_text,25,'...'),
                'entered_at' =>  $note->entered_at
            ];
            if ($type =="jobs") {
                Notification::send($client->users, new NewJobNote($note->id,$data,Auth::user()->full_name));
                //this could be deleted when client fromn tcreated
                 //Notification::send(Auth::user(), new NewJobNote($note->id,$data,Auth::user()->full_name));
                //
            } else {
                Notification::send($client->users, new NewWorkNote($note->id,$data,Auth::user()->full_name));
                //this could be deleted when client fromn tcreated
                 //Notification::send(Auth::user(), new NewWorkNote($note->id,$data,Auth::user()->full_name));
                //
            }
        }
        Session::flash('message', 'New note added');
    
        if ($type =="jobs") {
            return redirect()->to(route('jobs.edit',$eid) .'?#notes');
        } else{
            return redirect()->to(route('workorders.edit',$eid) .'?#notes');
        }
        
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
    public function edit($type,$eid,$id)
    {
       $note = Note::findOrFail($id);
       if ($type =="jobs") {
            return redirect()->to(route('jobs.edit',$eid) .'?#notes')->with('note', $note);
        } else{
            return redirect()->to(route('workorders.edit',$eid) .'?#notes')->with('note', $note);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$type,$eid, $id)
    {
        
       
        $this->validate($request, [
            'note'.$id => 'required',
        ]);
       if ($type =="jobs") {
            $xentity = Job::findOrFail($eid);
            $client = $xentity->client;
        } else {
            $xentity = WorkOrder::findOrFail($eid);
            $client = $xentity->job->client;
        }

        $note = Note::findOrFail($id);
        $note->note_text = $request->input('note'.$id);
        $old_viewable = $note->viewable;
        if($request->has('viewable')) {
            $note->viewable = 1;
            if($old_viewable == 0 ) {
                $data = [
                'note' => str_limit($note->note_text,25,'...'),
                'entered_at' =>  $note->entered_at
                ];
                if ($type =="jobs") {
                    Notification::send($client->users, new NewJobNote($note->id,$data,Auth::user()->full_name));
                    //this could be deleted when client fromn tcreated
                     Notification::send(Auth::user(), new NewJobNote($note->id,$data,Auth::user()->full_name));
                    //
                } else {
                    Notification::send($client->users, new NewWorkNote($note->id,$data,Auth::user()->full_name));
                    //this could be deleted when client fromn tcreated
                     Notification::send(Auth::user(), new NewWorkNote($note->id,$data,Auth::user()->full_name));
                    //
                }
            }
        }  else {
            $note->viewable = 0;
        }
        $note->save();
        Session::flash('message', 'Note Updated');
       
        if ($type =="jobs") {
          
            return redirect()->to(route('jobs.edit',$eid) .'?#notes');
        } else{
           
            return redirect()->to(route('workorders.edit',$eid) .'?#notes');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($type,$eid,$id)
    {
        
        $note = Note::findOrFail($id);
        $note->delete();
        
        Session::flash('message', 'Note Deleted');
        if ($type =="jobs") {
            return redirect()->to(route('jobs.edit',$eid) .'?#notes');
        } else{
            return redirect()->to(route('workorders.edit',$eid) .'?#notes');
        }
    }
    
    
     public function removenotification($id) {
        $user = \Auth::user();
        $notification = $user->notifications()->where('id',$id)->first();
        if ($notification)
        {
            $notification->delete();
            return 'DELETED';
        }
        else
        return 'ERROR';
     }
             
}
