<?php

namespace App\Http\Controllers\Clients;

use App\Attachment;
use App\AttachmentType;
use App\Client;
use App\Coordinate;
use App\Http\Controllers\Controller;
use App\Job;
use App\Notifications\ShareJobRequestFromQrScan;
use App\SharedJobToUser;
use App\WorkOrder;
use App\WorkOrderType;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Response;
use Session;
use Storage;

class JobsSharedController extends Controller
{
    private $wo_types;

    private $statuses = [
        'cancelled' => 'Cancelled',
        'cancelled charge' => 'Cancelled Charge',
        'cancelled duplicate' => 'Cancelled Duplicate',
        'cancelled duplicate needs credit' => 'Cancelled Duplicate Needs Credit',
        'cancelled no charge' => 'Cancelled No Charge',
        'closed' => 'Closed',
        'completed' => 'Completed',
        'data entry' => 'Data Entry',
        'edit' => 'Edit',
        'open' => 'Open',
        'payment pending' => 'Payment Pending',
        'pending' => 'Pending',
        'pending client' => 'Pending Client',
        'phone calls' => 'Phone Calls',
        'print' => 'Print',
        'qc' => 'Q/C',
        'search' => 'Search',
        'tax rolls' => 'Tax Rolls',
        'atids' => 'Title Search',
    ];

    private $parties_type = [
        'client' => 'Client',
        'customer' => 'Customer',
        'general_contractor' => 'General Contractor',
        'bond' => 'Bond Info',
        'landowner' => 'Property Owner',
        'leaseholder' => 'Lease Holder',
        'lender' => 'Lender',
        'copy_recipient' => 'Copy Recipient',
        'sub_contractor' => 'Sub Contractor',
        'sub_sub' => 'Sub-Sub Contractor',

    ];

    private $counties = ['ALACHUA', 'BAKER', 'BAY', 'BRADFORD', 'BREVARD', 'BROWARD', 'CALHOUN', 'CHARLOTTE', 'CITRUS', 'CLAY', 'COLLIER', 'COLUMBIA', 'DESOTO', 'DIXIE', 'DUVAL', 'ESCAMBIA', 'FLAGLER', 'FRANKLIN', 'GADSDEN', 'GILCHRIST', 'GLADES', 'GULF', 'HAMILTON', 'HARDEE', 'HENDRY', 'HERNANDO', 'HIGHLANDS', 'HILLSBOROUGH', 'HOLMES', 'INDIAN RIVER', 'JACKSON', 'JEFFERSON', 'LAFAYETTE', 'LAKE', 'LEE', 'LEON', 'LEVY', 'LIBERTY', 'MADISON', 'MANATEE', 'MARION', 'MARTIN', 'MIAMI-DADE', 'MONROE', 'NASSAU', 'OKALOOSA', 'OKEECHOBEE', 'ORANGE', 'OSCEOLA', 'PALM BEACH', 'PASCO', 'PINELLAS', 'POLK', 'PUTNAM', 'SANTA ROSA', 'SARASOTA', 'SEMINOLE', 'ST. JOHNS', 'ST. LUCIE', 'SUMTER', 'SUWANNEE', 'TAYLOR', 'UNION', 'VOLUSIA', 'WAKULLA', 'WALTON', 'WASHINGTON'];

    public function __construct()
    {
        $this->wo_types = WorkOrderType::all()->pluck('name', 'slug')->toArray();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $authUser = Auth::user();
        $clientIds = [];
        foreach (SharedJobToUser::where('user_id', $authUser->id)->get() as $shared) {
            if (isset($shared->job->client->id)) {
                $clientIds[] = $shared->job->client->id;
            }
        }

        $clients = Client::whereIn('id', $clientIds)->get()->sortBy('company_name')->pluck('company_name', 'id')->prepend('All', 0);
        if (! $authUser->client->is_monitoring_user) {
            return view('errors.403');
        }
        $jobs = Job::query();
        if (session()->has('job_shared_filter.name')) {
            $jobs->where('name', 'LIKE', '%'.session('job_shared_filter.name').'%');
        }

        if (session()->has('job_shared_filter.client')) {
            $jobs->where('client_id', session('job_shared_filter.client'));
        }
        $jobIds = $jobs->get()->pluck('id')->toArray();

        $sharedJobs = SharedJobToUser::query()->whereIn('job_id', $jobIds)->where('user_id', $authUser->id);

        $sharedJobs = $sharedJobs->orderBy('id', 'DESC')->paginate(15);
        $teamUsers = $authUser->client->activeUsers->pluck('full_name', 'id')->toArray();
        Session::put('backUrl', \URL::full());
        $data = [
            'jobs' => $sharedJobs,
            'clients' => $clients,
            'teamUsers' => $teamUsers,
            'my_job_list' => $authUser->client->jobs->pluck('name', 'id')->prepend('', '')->toArray(),
        ];

        return view('client.jobs_shared.index', $data);
    }

    /**
     * Show summary
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function summary($job_id)
    {
        $job = Job::where('id', $job_id)->first();
        if (empty($job)) {
            Session::flash('message', 'Job '.$job_id.' has been deleted already.');

            return redirect()->back();
        }
        $work_orders = WorkOrder::where('job_id', $job_id)->where('status', '!=', 'temporary')->get();
        $nto_printed_at = '';
        $nto_filled_timly = 'No';
        foreach ($work_orders as $work) {
            $attachments = $work->attachments()->where('description', 'like', 'AUTOMATICALLY GENERATED NOTICE TO OWNER%')->get();
            foreach ($attachments as $attachment) {
                if ($attachment->printed_at && $attachment->printed_at > $nto_printed_at) {
                    $nto_printed_at = $attachment->printed_at;
                }
                if ($nto_filled_timly == 'Yes') {
                    continue;
                }
                $diff = date_diff(date_create($job->started_at), date_create($attachment->created_at));
                $nto_filled_timly = $diff->format('%a') > 40 ? 'No' : 'Yes';
            }
        }

        $parties_type = [
            'client' => 'Client',
            'customer' => 'Customer',
            'general_contractor' => 'General Contractor',
            'bond' => 'Bond Info',
            'landowner' => 'Property Owner',
            'leaseholder' => 'Lease Holder',
            'lender' => 'Lender',
            'copy_recipient' => 'Copy Recipient',
            'sub_contractor' => 'Sub Contractor',
            'sub_sub' => 'Sub-Sub Contractor',
        ];

        $mailing_types = [
            'none' => 'None',
            'standard-mail' => 'Regular Mail',
            'certified-green' => 'Certfied Green RR',
            'certified-nongreen' => 'Certfied Non Green',
            'registered-mail' => 'Registered Mail',
            'express-mail' => 'Express Mail',
            'other-mail' => 'eMail',
        ];

        $data = [
            'job' => $job,
            'work_orders' => $work_orders,
            'nto_printed_at' => $nto_printed_at ? date('m/d/Y', strtotime($nto_printed_at)) : '',
            'nto_filled_timly' => $nto_filled_timly,
            'wo_types' => $this->wo_types,
            'parties_type' => $parties_type,
            'mailing_types' => $mailing_types,
        ];

        return view('client.jobs_shared.summary', $data);
    }

    public function linkTo($job_id, Request $request)
    {
        $linked_to = $request->linked_to;
        $job = Job::where('id', $job_id)->first();
        if (! $linked_to) {
            Session::flash('message', 'Error: You did not select a job to link.');

            return redirect()->back();
        }
        if (! $job) {
            Session::flash('message', 'Error: The shared job was already deleted.');

            return redirect()->back();
        }
        $job->linked_to = $linked_to;
        $job->save();

        Session::flash('message', 'Success: The shared job was linked.');

        return redirect()->back();
    }

    public function unlink($job_id)
    {
        $job = Job::where('id', $job_id)->first();
        if (! $job) {
            Session::flash('message', 'Error: The shared job was already deleted.');

            return redirect()->back();
        }
        $job->linked_to = null;
        $job->save();

        Session::flash('message', 'Success: The shared job was unlinked.');

        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $existsJob = Job::where('id', $id)->get();
        if (count($existsJob) < 1) {
            Session::flash('message', 'Job '.$id.' has been deleted already.');

            return redirect()->route('client.jobs_shared.index');
        }

        if (session()->has('note')) {
            $xnote = session('note');
        } else {
            $xnote = '';
        }

        if (session()->has('payment')) {
            $xpayment = session('payment');
        } else {
            $xpayment = '';
        }

        if (session()->has('change')) {
            $xchange = session('change');
        } else {
            $xchange = '';
        }

        $job = Job::findOrFail($id);

        $job_types = [
            'public' => 'Public - Roadwork, Airport, Government buildings etc',
            'private' => 'Private - Residential, Commercial properties etc',
        ];

        $job_statuses = [

            'closed' => 'Closed',

        ];
        $work_types = WorkOrderType::where('deleted_at', null)->pluck('name', 'slug')->toArray();
        $job_statuses = array_merge($work_types, $job_statuses);

        $attachment_types = AttachmentType::where('slug', '!=', 'generated')->get()->pluck('name', 'slug');
        $available_notices = [
            'amend-claim-of-lien',
            'claim-of-lien',
            'conditional-waiver-and-release-of-lien-upon-final-payment',
            'conditional-waiver-and-release-of-lien-upon-progress-payment',
            'contractors-final-payment-affidavit',
            'notice-of-bond',
            'notice-of-commencement',
            'notice-of-termination',
            'notice-of-contest-of-claim-against-payment-bond',
            'notice-of-contest-of-lien',
            'notice-to-owner',
            'notice-of-non-payment',
            'notice-of-nonpayment-with-intent-to-lien-andor-foreclose',
            'partial-satisfaction-of-lien',
            'satisfaction-of-lien',
            'sworn-statement-of-account',
            'waiver-and-release-of-lien-upon-final-payment',
            'waiver-and-release-of-lien-upon-progress-payment',
            'waiver-of-right-to-claim-against-bond-final-payment',
            'waiver-of-right-to-claim-against-bond-progress-payment',
        ];
        $work_orders = WorkOrder::where('job_id', $id)->where('status', '!=', 'temporary')->get();
        // $locked=count(WorkOrder::where('job_id',$id)->whereIn('status',['pending','data entry','edit','print'])->get());
        $locked = count($job->workorders->whereIn('status', ['pending', 'data entry', 'edit', 'print'])->where('service', '!=', 'self'));
        $style = '';
        if ($locked > 0) {
            $style = 'pointer-events:none;opacity:0.5;';
        }
        $coordinates = Coordinate::where('client_id', Auth::user()->client_id)
                      ->where('deleted_at', null)->get()->pluck('full_name', 'id')->prepend('', '');
        $data = [
            'style' => $style,
            'job_types' => $job_types,
            'job' => $job,
            'attachment_types' => $attachment_types,
            'work_order' => request()->input('workorder'),
            'xnote' => $xnote,
            'job_statuses' => $job_statuses,
            'xpayment' => $xpayment,
            'xchange' => $xchange,
            'wo_types' => ['all' => 'All'] + $this->wo_types,
            'statuses' => ['all' => 'All'] + $this->statuses,
            'available_notices' => $available_notices,
            'counties' => $this->counties,
            'work_orders' => $work_orders,
            'coordinates' => $coordinates,
        ];

        return view('client.jobs_shared.edit', $data);
    }

    public function setfilter(Request $request)
    {
        if ($request->has('resetfilter')) {
            if ($request->resetfilter == 'true') {
                session()->forget('job_shared_filter');
            }
        }

        if ($request->has('job_name')) {
            if ($request->job_name == '') {
                session()->forget('job_shared_filter.name');
            } else {
                session(['job_shared_filter.name' => $request->job_name]);
            }
        }

        if ($request->has('client_filter')) {
            if ($request->client_filter == 0) {
                session()->forget('job_shared_filter.client');
            } else {
                session(['job_shared_filter.client' => $request->client_filter]);
            }
        }

        return redirect()->route('client.jobs_shared.index');
    }

    public function resetfilter(Request $request)
    {
        session()->forget('job_shared_filter');

        return redirect()->route('client.jobs_shared.index');
    }

    public function showthumbnail($job_id, $id)
    {
        $attachment = Attachment::findOrFail($id);
        if (is_null($attachment->thumb_path)) {
            switch ($attachment->file_mime) {
                case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                    $contents = file_get_contents(public_path('images/word.png'));
                    break;
                default:
                    $contents = file_get_contents(public_path('images/file.png'));
                    break;
            }
        } else {
            $contents = Storage::get($attachment->thumb_path);
        }

        $response = Response::make($contents, '200');
        $response->header('Content-Type', 'image/png');

        return $response;
    }

    public function showattachment($job_id, $id)
    {
        if (count(Attachment::where('id', $id)->get()) < 1) {
            Session::flash('message', 'File no longer exists.');

            return redirect()->route('client.jobs_shared.edit', $job_id);
        }

        $attachment = Attachment::findOrFail($id);
        $contents = Storage::get($attachment->file_path);
        $response = Response::make($contents, '200', [
            'Content-Type' => $attachment->file_mime,
            'Content-Disposition' => 'attachment; filename="'.$attachment->original_name.'"',
        ]);

        return $response;
    }

    public function shareRequestFromNotice(Request $request)
    {
        $number = intval($request->number);
        $secret = intval($request->secret);
        $job = Job::where('id', $number)->where('secret_key', $secret)->where('deleted_at', null)->first();
        if (empty($job)) {
            Session::flash('message', 'Error: Job was not found.');

            return redirect()->back();
        }
        $user = Auth::user();
        $client = $job->client;
        foreach ($client->activeusers as $toUser) {
            Notification::send($toUser, new ShareJobRequestFromQrScan($job, $user));
        }
        Session::flash('message', 'Success: We have sent your request to the job owner.');

        return redirect()->back();
    }
}
