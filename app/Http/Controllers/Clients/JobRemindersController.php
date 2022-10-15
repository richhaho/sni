<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\JobReminders;
use Illuminate\Http\Request;
use Response;
use Session;
use Storage;

class JobRemindersController extends Controller
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
        $reminders = JobReminders::query();
        $reminders = $reminders->orderBy('sent_at')->paginate(10);
        $data = [
            'reminders' => $reminders,
        ];

        return view('client.jobs.reminders.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request, $job_id)
    {
        $this->validate($request, [
            'emails' => 'required',
            'note' => 'required',
            'date' => 'required',
        ]);
        $data = [
            'job_id' => $job_id,
            'emails' => $request->emails,
            'note' => $request->note,
            'date' => new \DateTime($request->date),
        ];
        JobReminders::create($data);
        Session::flash('message', 'New Reminder created.');

        return redirect()->to(url()->previous().'?#reminders');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $job_id, $id)
    {
        $this->validate($request, [
            'emails' => 'required',
            'note' => 'required',
            'date' => 'required',
        ]);

        $data = [
            'emails' => $request->emails,
            'note' => $request->note,
            'date' => new \DateTime($request->date),
        ];

        $reminder = JobReminders::where('id', $id)->first();
        $reminder->update($data);

        Session::flash('message', 'Job reminder Updated.');

        return redirect()->to(url()->previous().'?#reminders');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($job_id, $id)
    {
        $reminder = JobReminders::findOrFail($id);
        $reminder->delete();

        Session::flash('message', 'Job reminder successfully deleted.');

        return redirect()->to(url()->previous().'?#reminders');
    }
}
