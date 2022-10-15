<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Job;
use App\JobNoc;
use Illuminate\Http\Request;
use Response;
use Session;
use Storage;

class JobNocsController extends Controller
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
        $nocs = JobNoc::query();
        $nocs = $nocs->orderBy('recorded_at')->paginate(10);
        $data = [
            'nocs' => $nocs,
        ];

        return view('admin.jobs.nocs.index', $data);
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
            'noc_number' => 'required',
        ]);
        $noc = JobNoc::where('job_id', $job_id)->where('noc_number', $request->noc_number)->first();
        if ($noc) {
            Session::flash('message', 'This NOC# already exist.');

            return redirect()->route('jobs.edit', ['id' => $job_id, '#nocs']);
        }
        $data = [
            'job_id' => $job_id,
            'noc_number' => $request->noc_number,
            'noc_notes' => $request->noc_notes,
            'recorded_at' => $request->recorded_at ? date('Y-m-d H:i:s', strtotime($request->recorded_at)) : date('Y-m-d H:i:s'),
            'expired_at' => $request->expired_at ? date('Y-m-d H:i:s', strtotime($request->expired_at)) : date('Y-m-d H:i:s', strtotime('+1 year')),
        ];
        $noc = JobNoc::create($data);
        if ($request['copy_noc'] != null && $request['copy_noc'] != '') {
            $f = $request->file('copy_noc');
            if (! $this->checkFileSize($f)) {
                Session::flash('message', 'This file is too large to upload.');

                return redirect()->route('jobs.edit', ['id' => $job_id, '#nocs']);
            }
            $xfilename = $noc->id.'.'.$f->guessExtension();
            $xpath = 'attachments/jobs/noc/';
            $f->storeAs($xpath, $xfilename);
            $noc->copy_noc = $xpath.$xfilename;
            $noc->save();
        }
        Session::flash('message', 'New NOC was created.');

        return redirect()->route('jobs.edit', ['id' => $job_id, '#nocs']);
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
            'noc_number' => 'required',
        ]);

        $noc = JobNoc::where('id', '!=', $id)->where('job_id', $job_id)->where('noc_number', $request->noc_number)->first();
        if ($noc) {
            Session::flash('message', 'This NOC# already exist.');

            return redirect()->route('jobs.edit', ['id' => $job_id, '#nocs']);
        }

        $data = [
            'noc_number' => $request->noc_number,
            'noc_notes' => $request->noc_notes,
            'recorded_at' => $request->recorded_at ? date('Y-m-d H:i:s', strtotime($request->recorded_at)) : date('Y-m-d H:i:s'),
            'expired_at' => $request->expired_at ? date('Y-m-d H:i:s', strtotime($request->expired_at)) : date('Y-m-d H:i:s', strtotime('+1 year')),
        ];

        $noc = JobNoc::where('id', $id)->first();
        $noc->update($data);
        if ($request['copy_noc'] != null && $request['copy_noc'] != '') {
            $f = $request->file('copy_noc');
            if (! $this->checkFileSize($f)) {
                Session::flash('message', 'This file is too large to upload.');

                return redirect()->route('jobs.edit', ['id' => $job_id, '#nocs']);
            }
            $xfilename = $noc->id.'.'.$f->guessExtension();
            $xpath = 'attachments/jobs/noc/';
            $f->storeAs($xpath, $xfilename);
            $noc->copy_noc = $xpath.$xfilename;
            $noc->save();
        }
        Session::flash('message', 'Job NOC was Updated.');

        return redirect()->to(url()->previous().'?#nocs');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($job_id, $id)
    {
        $noc = JobNoc::findOrFail($id);
        $job = Job::findOrFail($job_id);
        if ($job->noc_number == $noc->noc_number) {
            $job->noc_number = '';
            $job->save();
        }
        $noc->delete();

        Session::flash('message', 'Job NOC was successfully deleted.');

        return redirect()->to(url()->previous().'?#nocs');
    }

    public function setCurrent($job_id, $id)
    {
        $noc = JobNoc::findOrFail($id);
        $job = Job::findOrFail($job_id);
        $job->noc_number = $noc->noc_number;
        $job->save();
        Session::flash('message', 'Current NOC was set for the job.');

        return redirect()->to(url()->previous().'?#nocs');
    }

    public function downloadNOC($job_id, $id)
    {
        $noc = JobNoc::findOrFail($id);
        $contents = Storage::get($noc->copy_noc);
        $response = Response::make($contents, '200', [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="copy_of_noc.pdf"',
        ]);

        return $response;
    }

    public function checkFileSize($f)
    {
        $max_uploadfileSize = min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
        $max_uploadfileSize = substr($max_uploadfileSize, 0, -1) * 1024 * 1024;

        if ($f->getSize() > $max_uploadfileSize) {
            return false;
        }

        return true;
    }
}
