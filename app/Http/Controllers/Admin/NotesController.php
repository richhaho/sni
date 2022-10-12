<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Job;
use App\Note;
use App\Notifications\NewJobNote;
use App\Notifications\NewWorkNote;
use App\TempUser;
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
        return view('admin.notes.index');
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

            if ($type == 'jobs') {
                return Auth::user()->restricted ? redirect()->to(route('research.edit', $eid).'?#notes') : redirect()->to(route('jobs.edit', $eid).'?#notes');
            } else {
                return redirect()->to(route('workorders.edit', $eid).'?#notes');
            }
        }
        // $this->validate($request, [
        //     'note' => 'required',
        // ]);

        $note = new Note();
        $now = Carbon::now();
        $note->note_text = $request->input('note');
        $note->entered_at = $now->toDateTimeString();
        $note->entered_by = Auth::user()->id;
        if ($type == 'jobs') {
            $xentity = Job::findOrFail($eid);
            $note->client_id = $xentity->client->id;
            $client = $xentity->client;
            $job = $xentity;
        } else {
            $xentity = WorkOrder::findOrFail($eid);
            $note->client_id = $xentity->job->client->id;
            $client = $xentity->job->client;
            $job = $xentity->job;
        }

        if ($request->has('viewable')) {
            $note->viewable = 1;
        } else {
            $note->viewable = 0;
        }

        $note->notify_admin_researcher = $request->notify_admin_researcher;

        $note = $xentity->notes()->save($note);

        $data = [
            'note' => str_limit($note->note_text, 25, '...'),
            'entered_at' => $note->entered_at,
        ];

        if ($request->has('notify_admin_researcher')) {
            if ($type == 'jobs') {
                $admin_researcher = User::where('id', $note->notify_admin_researcher)->get();
                if (count($admin_researcher) > 0) {
                    Notification::send($admin_researcher, new NewJobNote($note->id, $data, Auth::user()->full_name, $xentity->number, $xentity->name, $note->note_text, true));
                }
            } else {
                $admin_researcher = User::where('id', $note->notify_admin_researcher)->get();
                if (count($admin_researcher) > 0) {
                    Notification::send($admin_researcher, new NewWorkNote($note->id, $data, Auth::user()->full_name, $xentity->job->number, $xentity->job->name, $xentity->number, $note->note_text, true));
                }
            }
        }

        if ($request->has('viewable')) {
            $note->viewable = 1;
            if ($type == 'jobs') {
                $work = $xentity->workorders()->orderBy('id', 'desc')->first();
                $notifiable_user = $client->activeusers;
                if ($work) {
                    if ($work->responsible_user) {
                        $responsible_user = User::where('id', $work->responsible_user)->get();
                        if (count($responsible_user) > 0) {
                            $notifiable_user = $responsible_user;
                        }
                    }
                }
                Notification::send($notifiable_user, new NewJobNote($note->id, $data, Auth::user()->full_name, $xentity->number, $xentity->name, $note->note_text));
            // $admin_researcher = User::where('id', $note->notify_admin_researcher)->get();
                // if (count($admin_researcher)>0) {
                //     Notification::send($admin_researcher, new NewJobNote($note->id,$data,Auth::user()->full_name,$xentity->number,$xentity->name,$note->note_text));
                // }
            } else {
                $work = $xentity;
                $notifiable_user = $client->activeusers;
                if ($work) {
                    if ($work->responsible_user) {
                        $responsible_user = User::where('id', $work->responsible_user)->get();
                        if (count($responsible_user) > 0) {
                            $notifiable_user = $responsible_user;
                        }
                    }
                }
                Notification::send($notifiable_user, new NewWorkNote($note->id, $data, Auth::user()->full_name, $xentity->job->number, $xentity->job->name, $xentity->number, $note->note_text));
                // $admin_researcher = User::where('id', $note->notify_admin_researcher)->get();
                // if (count($admin_researcher)>0) {
                //     Notification::send($admin_researcher, new NewWorkNote($note->id,$data,Auth::user()->full_name,$xentity->job->number,$xentity->job->name,$xentity->number,$note->note_text));
                // }
            }
        }

        if ($job->notify_email) {
            $notify_user = TempUser::create(['email' => $job->notify_email]);
            if ($type == 'jobs') {
                Notification::send($notify_user, new NewJobNote($note->id, $data, Auth::user()->full_name, $xentity->number, $xentity->name, $note->note_text));
            } else {
                Notification::send($notify_user, new NewWorkNote($note->id, $data, Auth::user()->full_name, $xentity->job->number, $xentity->job->name, $xentity->number, $note->note_text));
            }
            $notify_user->delete();
        }
        Session::flash('message', 'New note added');

        if ($type == 'jobs') {
            return Auth::user()->restricted ? redirect()->to(route('research.edit', $eid).'?#notes') : redirect()->to(route('jobs.edit', $eid).'?#notes');
        } else {
            return redirect()->to(route('workorders.edit', $eid).'?#notes');
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
    public function edit($type, $eid, $id)
    {
        if (count(Note::where('id', $id)->get()) < 1) {
            Session::flash('message', 'This Note Already Deleted.');
            if ($type == 'jobs') {
                return Auth::user()->restricted ? redirect()->to(route('research.edit', $eid).'?#notes') : redirect()->to(route('jobs.edit', $eid).'?#notes');
            } else {
                return redirect()->to(route('workorders.edit', $eid).'?#notes');
            }
        }

        $note = Note::findOrFail($id);
        if ($type == 'jobs') {
            return Auth::user()->restricted ? redirect()->to(route('research.edit', $eid).'?#notes')->with('note', $note) : redirect()->to(route('jobs.edit', $eid).'?#notes')->with('note', $note);
        } else {
            return redirect()->to(route('workorders.edit', $eid).'?#notes')->with('note', $note);
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
            $client = $xentity->client;
            $job = $xentity;
        } else {
            $xentity = WorkOrder::findOrFail($eid);
            $client = $xentity->job->client;
            $job = $xentity->job;
        }

        $note = Note::findOrFail($id);
        $note->note_text = $request->input('note'.$id);
        $note->notify_admin_researcher = $request->notify_admin_researcher;
        $old_viewable = $note->viewable;

        $data = [
            'note' => str_limit($note->note_text, 25, '...'),
            'entered_at' => $note->entered_at,
        ];
        if ($request->has('notify_admin_researcher')) {
            if ($type == 'jobs') {
                $admin_researcher = User::where('id', $note->notify_admin_researcher)->get();
                if (count($admin_researcher) > 0) {
                    Notification::send($admin_researcher, new NewJobNote($note->id, $data, Auth::user()->full_name, $xentity->number, $xentity->name, $note->note_text, true));
                }
            } else {
                $admin_researcher = User::where('id', $note->notify_admin_researcher)->get();
                if (count($admin_researcher) > 0) {
                    Notification::send($admin_researcher, new NewWorkNote($note->id, $data, Auth::user()->full_name, $xentity->job->number, $xentity->job->name, $xentity->number, $note->note_text, true));
                }
            }
        }

        if ($request->has('viewable')) {
            $note->viewable = 1;
            if ($old_viewable == 0) {
                if ($type == 'jobs') {
                    //if ($client->notification_setting=='immediate'){

                    Notification::send($client->activeusers, new NewJobNote($note->id, $data, Auth::user()->full_name, $xentity->number, $xentity->name, $note->note_text));

                //}
                    //this could be deleted when client fromn tcreated
                    //if (Auth::user()->client->notification_setting=='immediate'){
                        // Notification::send(Auth::user(), new NewJobNote($note->id,$data,Auth::user()->full_name));
                    //}
                    //
                } else {
                    //if ($client->notification_setting=='immediate'){

                    //Notification::send($client->activeusers, new NewWorkNote($note->id,$data,Auth::user()->full_name));
                    Notification::send($client->activeusers, new NewWorkNote($note->id, $data, Auth::user()->full_name, $xentity->job->number, $xentity->job->name, $xentity->number, $note->note_text));

                    //}
                    //this could be deleted when client fromn tcreated
                    //if (Auth::user()->client->notification_setting=='immediate'){
                         //Notification::send(Auth::user(), new NewWorkNote($note->id,$data,Auth::user()->full_name));
                    //    }
                    //
                }
            }
        } else {
            $note->viewable = 0;
        }
        $note->save();

        if ($job->notify_email) {
            $notify_user = TempUser::create(['email' => $job->notify_email]);
            if ($type == 'jobs') {
                Notification::send($notify_user, new NewJobNote($note->id, $data, Auth::user()->full_name, $xentity->number, $xentity->name, $note->note_text));
            } else {
                Notification::send($notify_user, new NewWorkNote($note->id, $data, Auth::user()->full_name, $xentity->job->number, $xentity->job->name, $xentity->number, $note->note_text));
            }
            $notify_user->delete();
        }

        Session::flash('message', 'Note Updated');

        if ($type == 'jobs') {
            return Auth::user()->restricted ? redirect()->to(route('research.edit', $eid).'?#notes') : redirect()->to(route('jobs.edit', $eid).'?#notes');
        } else {
            return redirect()->to(route('workorders.edit', $eid).'?#notes');
        }
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
                return Auth::user()->restricted ? redirect()->to(route('research.edit', $eid).'?#notes') : redirect()->to(route('jobs.edit', $eid).'?#notes');
            } else {
                return redirect()->to(route('workorders.edit', $eid).'?#notes');
            }
        }

        $note = Note::findOrFail($id);
        $note->delete();

        Session::flash('message', 'Note Deleted');
        if ($type == 'jobs') {
            return Auth::user()->restricted ? redirect()->to(route('research.edit', $eid).'?#notes') : redirect()->to(route('jobs.edit', $eid).'?#notes');
        } else {
            return redirect()->to(route('workorders.edit', $eid).'?#notes');
        }
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
