<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Job;
use App\Note;
use App\Notifications\NewJobNote;
use App\Notifications\NewWorkNote;
use App\User;
use App\WorkOrder;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Session;

class NotesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($type, $type_id)
    {
        return view('client.notes.index');
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
    public function store(Request $request, $type, $eid)
    {
        if ($request['note'] == null || $request['note'] == '') {
            Session::flash('message', 'Note field is required.');

            return redirect()->to(url()->previous().'?#notes');
        }

        // $this->validate($request, [
        //     'note' => 'required',
        // ]);

        if ($type == 'jobs') {
            $xentity = Job::findOrFail($eid);
            $this->authorize('wizard', $xentity);
        } else {
            $xentity = WorkOrder::findOrFail($eid);
            $job = $xentity->job;
            $this->authorize('wizard', $job);
        }
        $note = new Note();
        $now = Carbon::now();
        $note->note_text = $request->input('note');
        $note->entered_at = $now->toDateTimeString();
        $note->entered_by = Auth::user()->id;
        if ($type == 'jobs') {
            $note->client_id = $xentity->client->id;
            $client = $xentity->client;
        } else {
            $note->client_id = $xentity->job->client->id;
            $client = $xentity->job->client;
        }

        $note->viewable = 1;

        $note = $xentity->notes()->save($note);

        $note->viewable = 1;
        $data = [
            'note' => str_limit($note->note_text, 25, '...'),
            'entered_at' => $note->entered_at,
        ];

        $adminEmail = \App\AdminEmails::where('class', $type == 'jobs' ? 'NewJobNote' : 'NewWorkNote')->first();
        $adminUserIds = explode(',', $adminEmail->users);
        if (count($adminUserIds) > 0 && $adminEmail->users) {
            $admin_users = User::where('status', 1)->whereIn('id', $adminUserIds)->get();
        } else {
            $admin_users = User::where('status', 1)->isRole(['admin', 'researcher'])->get();
        }
        if ($type == 'jobs') {
            Notification::send($admin_users, new NewJobNote($note->id, $data, Auth::user()->full_name, $xentity->number, $xentity->name, $note->note_text));
        //this could be deleted when client fromn tcreated
            //Notification::send(Auth::user(), new NewJobNote($note->id,$data,Auth::user()->full_name));
        } else {
            Notification::send($admin_users, new NewWorkNote($note->id, $data, Auth::user()->full_name, $xentity->job->number, $xentity->job->name, $xentity->number, $note->note_text));
            //this could be deleted when client fromn tcreated
            //Notification::send(Auth::user(), new NewWorkNote($note->id,$data,Auth::user()->full_name));
        }

        Session::flash('message', 'New note added');

        return redirect()->to(url()->previous().'?#notes');
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
    public function edit($type, $eid, $id)
    {
        if (count(Note::where('id', $id)->get()) < 1) {
            Session::flash('message', 'This Note Already Deleted.');
            if ($type == 'jobs') {
                return redirect()->to(route('client.jobs.edit', $eid).'?#notes');
            } else {
                return redirect()->to(route('client.notices.edit', $eid).'?#notes');
            }
        }

        if ($type == 'jobs') {
            $xentity = Job::findOrFail($eid);
            $this->authorize('wizard', $xentity);
        } else {
            $xentity = WorkOrder::findOrFail($eid);
            $job = $xentity->job;
            $this->authorize('wizard', $job);
        }
        $note = Note::findOrFail($id);
        if ($type == 'jobs') {
            return redirect()->to(route('client.jobs.edit', $eid).'?#notes')->with('note', $note);
        } else {
            return redirect()->to(route('client.notices.edit', $eid).'?#notes')->with('note', $note);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $type, $eid, $id)
    {
        $this->validate($request, [
            'note'.$id => 'required',
        ]);
        if ($type == 'jobs') {
            $xentity = Job::findOrFail($eid);
            $this->authorize('wizard', $xentity);
            $client = $xentity->client;
        } else {
            $xentity = WorkOrder::findOrFail($eid);
            $job = $xentity->job;
            $this->authorize('wizard', $job);
            $client = $job->client;
        }

        $note = Note::findOrFail($id);
        $note->note_text = $request->input('note'.$id);

        $note->save();
        Session::flash('message', 'Note Updated');

        return redirect()->to(url()->previous().'?#notes');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($type, $eid, $id)
    {
        if (count(Note::where('id', $id)->get()) < 1) {
            Session::flash('message', 'This Note Already Deleted.');
            if ($type == 'jobs') {
                return redirect()->to(route('client.jobs.edit', $eid).'?#notes');
            } else {
                return redirect()->to(route('client.notices.edit', $eid).'?#notes');
            }
        }

        if ($type == 'jobs') {
            $xentity = Job::findOrFail($eid);
            $this->authorize('wizard', $xentity);
        } else {
            $xentity = WorkOrder::findOrFail($eid);
            $job = $xentity->job;
            $this->authorize('wizard', $job);
        }
        $note = Note::findOrFail($id);
        $note->delete();

        Session::flash('message', 'Note Deleted');

        return redirect()->to(url()->previous().'?#notes');
    }

    public function removenotification($id)
    {
        $user = \Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->delete();

            return 'DELETED';
        } else {
            return 'ERROR';
        }
    }
}
