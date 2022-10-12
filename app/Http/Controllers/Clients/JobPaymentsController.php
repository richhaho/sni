<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Job;
use App\JobPaymentHistory;
use Illuminate\Http\Request;
use Response;
use Session;
use Storage;

class JobPaymentsController extends Controller
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
    public function store(Request $request, $job_id)
    {
        // $this->validate($request, [
        //     'payed_on' => 'required',
        //     'amount' => 'required|numeric',
        // ]);

        if ($request['payed_on'] == null || $request['payed_on'] == '' || $request['amount'] == null || $request['amount'] == '') {
            Session::flash('message', 'payed_on and amount are required.');

            return redirect()->to(route('client.jobs.edit', $job_id).'?#payments');
        }

        $f = $request->file('attached_file');
        if (isset($request['attached_file'])) {
            $max_uploadfileSize = min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
            $max_uploadfileSize = substr($max_uploadfileSize, 0, -1) * 1024 * 1024;
            if ($f->getSize() > $max_uploadfileSize) {
                Session::flash('message', 'Attached file is too large to upload.');

                return redirect()->to(route('client.jobs.edit', $job_id).'?#payments');
            }
        }

        $job = Job::findOrFail($job_id);
        $this->authorize('wizard', $job);

        $payment = new JobPaymentHistory;
        $payment->payed_on = date('Y-m-d', strtotime($request->payed_on));
        $payment->amount = $request->amount;
        $payment->description = $request->description;
        $payment->job_id = $job_id;
        $payment->save();
        if (isset($request['attached_file'])) {
            $xfilename = $payment->id.'.'.$f->guessExtension();
            $xpath = 'attachments/job_payments/';
            $f->storeAs($xpath, $xfilename);
            $payment->attached_file = $f->getClientOriginalName();
            $payment->file_mime = $f->getMimeType();
            $payment->file_path = $xpath.$xfilename;
            $payment->save();
        }

        Session::flash('message', 'New Payment added');

        return redirect()->to(route('client.jobs.edit', $job_id).'?#payments');
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
    public function edit($job_id, $id)
    {
        $job = Job::findOrFail($job_id);
        $this->authorize('wizard', $job);
        $payment = JobPaymentHistory::findOrFail($id);

        return redirect()->to(route('client.jobs.edit', $job_id).'?#payments')->with('payment', $payment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $job_id, $id)
    {
        $this->validate($request, [
            'payed_on' => 'required',
            'amount' => 'required|numeric',
        ]);
        $f = $request->file('attached_file');
        if (isset($request['attached_file'])) {
            $max_uploadfileSize = min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
            $max_uploadfileSize = substr($max_uploadfileSize, 0, -1) * 1024 * 1024;
            if ($f->getSize() > $max_uploadfileSize) {
                Session::flash('message', 'Attached file is too large to upload.');

                return redirect()->to(route('client.jobs.edit', $job_id).'?#payments');
            }
        }

        $job = Job::findOrFail($job_id);
        $this->authorize('wizard', $job);

        $payment = JobPaymentHistory::findOrFail($id);
        $payment->payed_on = date('Y-m-d', strtotime($request->payed_on));
        $payment->amount = $request->amount;
        $payment->description = $request->input('description');
        $payment->save();
        if (isset($request['attached_file'])) {
            $xfilename = $payment->id.'.'.$f->guessExtension();
            $xpath = 'attachments/job_payments/';
            $f->storeAs($xpath, $xfilename);
            $payment->attached_file = $f->getClientOriginalName();
            $payment->file_mime = $f->getMimeType();
            $payment->file_path = $xpath.$xfilename;
            $payment->save();
        }

        Session::flash('message', 'Payment Updated');

        return redirect()->to(route('client.jobs.edit', $job_id).'?#payments');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($job_id, $id)
    {
        $job = Job::findOrFail($job_id);
        $this->authorize('wizard', $job);
        $payment = JobPaymentHistory::findOrFail($id);
        $payment->delete();

        return redirect()->to(route('client.jobs.edit', $job_id).'?#payments');
    }

    public function showattachment($id)
    {
        $payment = JobPaymentHistory::findOrFail($id);
        $contents = Storage::get($payment->file_path);
        $response = Response::make($contents, '200', [
            'Content-Type' => $payment->file_mime,
            'Content-Disposition' => 'attachment; filename="'.$payment->attached_file.'"',
        ]);

        return $response;
    }
}
