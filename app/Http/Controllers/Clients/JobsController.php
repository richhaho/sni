<?php

namespace App\Http\Controllers\Clients;

use App\Attachment;
use App\AttachmentType;
use App\Client;
use App\ContactInfo;
use App\Coordinate;
use App\Entity;
use App\Http\Controllers\Controller;
use App\Job;
use App\JobLog;
use App\JobParty;
use App\Notifications\NewAttachment;
use App\Notifications\ShareJobToMornitoringUser;
use App\PropertyRecords;
use App\SharedJobToUser;
use App\TempUser;
use App\User;
use App\WorkOrder;
use App\WorkOrderType;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Response;
use Session;
use Storage;

class JobsController extends Controller
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
        $jobs = Job::query();
        $job_statuses = [
            'none' => 'All Open',
            'null' => 'Blank/Null',
            'closed' => 'Closed',

        ];
        $work_types = WorkOrderType::where('deleted_at', null)->pluck('name', 'slug')->toArray();
        $job_statuses = array_merge($job_statuses, $work_types);

        $jobs->where('client_id', Auth::user()->client_id);
        if (isset($request['coordinate_id'])) {
            $jobs->where('coordinate_id', $request['coordinate_id']);
        }

        if (session()->has('job_filter.name')) {
            $jobs->where('name', 'LIKE', '%'.session('job_filter.name').'%');
        }

        if (session()->has('job_filter.job_type')) {
            if (session('job_filter.job_type') != 'all') {
                $jobs->where('type', session('job_filter.job_type'));
            }
        }

        if (session()->has('job_filter.job_status')) {
            if (session('job_filter.job_status') != 'none') {
                if (session('job_filter.job_status') != 'null') {
                    $jobs->where('status', session('job_filter.job_status'));
                } else {
                    $jobs->where('status', null)->orwhere('status', '');
                }
            } else {
                $jobs->where(function ($q) {
                    $q->where('status', '!=', 'closed')->orwhereNull('status');
                });
            }
        } else {
            $jobs->where(function ($q) {
                $q->where('status', '!=', 'closed')->orwhereNull('status');
            });
        }

        if (session()->has('job_filter.daterange')) {
            if (session('job_filter.daterange') != '') {
                $date_range = session('job_filter.daterange');
                $date_type = '/^[0-1][0-9]-[0-3][0-9]-[0-9]{4} - [0-1][0-9]-[0-3][0-9]-[0-9]{4}$/';

                if (preg_match($date_type, $date_range)) {
                    $dates = explode(' - ', session('job_filter.daterange'));
                    //dd($dates);
                    $from_date = \Carbon\Carbon::createFromFormat('m-d-Y', $dates[0]);
                    $to_date = \Carbon\Carbon::createFromFormat('m-d-Y', $dates[1]);

                    //$jobs->whereBetween('started_at',[$from_date,$to_date])->orderBy('started_at');

                    $from = substr($from_date, 0, 10).' 00:00:00';
                    $to = substr($to_date, 0, 10).' 23:59:59';
                    $jobs->where([['started_at', '>=', $from], ['started_at', '<=', $to]])->orderBy('started_at', 'desc');
                    Session::flash('message', null);
                } else {
                    Session::flash('message', 'Input not in expected range format');
                }
            }
        }

        $jobs = $jobs->orderBy('id', 'DESC')->paginate(15);

        Session::put('backUrl', \URL::full());
        $data = [
            'jobs' => $jobs,
            'job_statuses' => $job_statuses,
        ];

        return view('client.jobs.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $job_statuses = [
            'notice-to-owner' => 'Notice to Owner',
            'release-of-lien' => 'Release of Lien',
            'demand-letter' => 'Demand Letter',
            'claim-of-lien' => 'Claim of Lien',
            'ammended-claim-of-lien' => 'Amended Claim of Lien',
            'notice-of-non-payment' => 'Demand For Payment',
            'partial-satisfaction-of-lien' => 'Partial Satisfaction of Lien',
            'satisfaction-of-lien' => 'Satisfaction of Lien',
            'closed' => 'Closed',
        ];
        $job_types = [
            'public' => 'Public - Roadwork, Airport, Government buildings etc',
            'private' => 'Private - Residential, Commercial properties etc',
        ];

        $data = [

            'job_types' => $job_types,
            'job_statuses' => $job_statuses,

        ];

        return view('client.jobs.create', $data);
    }

    public function createLog($data, $job)
    {
        $jobfields = ['type', 'client_id', 'number', 'project_number', 'noc_number',
            'name', 'address_source', 'address_1', 'address_2', 'address_corner', 'city',
            'county', 'state', 'zip', 'country', 'started_at', 'last_day', 'status',
            'contract_amount', 'interest_rate', 'default_materials', 'legal_description',
            'folio_number', 'private_type', 'is_mall_unit', 'is_tenant', 'is_condo',
            'association_name', 'a_unit_number', 'mall_name', 'm_unit_number', 'coordinate_id', ];
        $changeArray = [];
        foreach ($jobfields as $field) {
            if (isset($data[$field])) {
                $change['field'] = $field;
                $change['old'] = null;
                $change['new'] = $data[$field];
                $changeArray[] = $change;
            }
        }
        $changes = json_encode($changeArray);
        if (count($changeArray) > 0) {
            JobLog::create([
                'job_id' => $job->id,
                'user_id' => Auth::user()->id,
                'user_name' => Auth::user()->fullName,
                'edited_at' => Carbon::now(),
                'data' => $changes,
                'type' => 'created',
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'client_id' => 'required',
            'type' => 'required',
            'name' => 'required',
            'interest_rate' => 'numeric',
            'contract_amount' => 'required|numeric',

        ]);
        $data = $request->all();
        if (strlen($data['started_at']) > 0) {
            $data['started_at'] = date('Y-m-d', strtotime($data['started_at']));
        } else {
            $data['started_at'] = null;
        }
        if (strlen($data['last_day']) > 0) {
            $data['last_day'] = date('Y-m-d', strtotime($data['last_day']));
        } else {
            $data['last_day'] = null;
        }

        $job = Job::create($data);
        $this->createLog($data, $job);
        $client = Auth::user()->client;

        $contact = $client->contacts->where('primary', 2)->first();

        if ($contact) {
            $job_party = new JobParty();
            $job_party->job_id = $job->id;
            $job_party->contact_id = $contact->id;
            $job_party->entity_id = $contact->entity_id;
            $job_party->type = 'client';
            $job_party->save();
        }
        $temp_name = $job->name;
        Session::flash('message', 'Job '.$temp_name.' created');
        //return redirect()->to(($request->input('redirects_to')));
        //return redirect()->route('parties.index',$job->id);
        return redirect()->route('client.jobs.index');
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

        return view('client.jobs.summary', $data);
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

            return redirect()->route('client.jobs.index');
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
        $this->authorize('wizard', $job);

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

        return view('client.jobs.edit', $data);
    }

    public function copy($id)
    {
        $job = Job::findOrFail($id);
        $this->authorize('wizard', $job);

        $new_job = $job->replicate();
        $new_job->name = 'Copy of '.$new_job->name;
        $new_job->save();
        $this->createLog((array) $new_job, $new_job);

        foreach ($job->parties as $party) {
            $new_party = $party->replicate();
            $new_party->source = 'CL';
            $new_party->job_id = $new_job->id;
            $new_party->save();
            if (strlen($new_party->bond_pdf) > 0) {
                $new_path = 'jobparties/bonds/pdfs/job-'.$new_party->job_id.'-party-'.$new_party->id.'.pdf';
                Storage::copy($new_party->bond_pdf, $new_path);
                $new_party->bond_pdf = $new_path;
                $new_party->save();
            }
        }
        Session::flash('message', 'This a  Copy of Job: '.$new_job->name);

        return redirect()->route('client.jobs.edit', $new_job->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'client_id' => 'required',
            'type' => 'required',
            'name' => 'required',
            'interest_rate' => 'numeric',
            'contract_amount' => 'required|numeric',

        ]);
        $data = $request->all();
        if (strlen($data['started_at']) > 0) {
            $data['started_at'] = date('Y-m-d', strtotime($data['started_at']));
        } else {
            $data['started_at'] = null;
        }
        if (strlen($data['last_day']) > 0) {
            $data['last_day'] = date('Y-m-d', strtotime($data['last_day']));
        } else {
            $data['last_day'] = null;
        }
        $job = Job::findOrFail($id);
        $this->authorize('wizard', $job);
        if ($job->status == 'closed' && ($job->last_day != $request->last_day) && $request->last_day) {
            $data['status'] = 'open';
        }
        $changeArray = $job->getChanges($data);
        $changes = json_encode($changeArray);
        $job->update($data);
        if (count($changeArray) > 0) {
            JobLog::create([
                'job_id' => $job->id,
                'user_id' => Auth::user()->id,
                'user_name' => Auth::user()->fullName,
                'edited_at' => Carbon::now(),
                'data' => $changes,
            ]);
        }

        $temp_name = $job->name;
        $this->recalculate_date($job);

        Session::flash('message', 'Job '.$temp_name.' updated.');
        if ($request->input('workorder') == '') {
            //return redirect()->to(($request->input('redirects_to')));
            return redirect()->route('client.jobs.edit', $job->id);
        } else {
            return redirect()->route('client.notices.edit', $request->input('workorder'));
        }
    }

    public function recalculate_date($job)
    {
        $works = $job->workorders->whereNotIn('work_orders.status', ['completed', 'cancelled', 'cancelled charge', 'cancelled no charge', 'closed', 'temporary'])->where('work_orders.deleted_at', null);

        $job_started_at = date_create($job->started_at);
        $today = date_create(date('c'));
        $last_day = $job->last_day;
        $dif = date_diff($job_started_at, $today)->format('%a');

        foreach ($works as $work) {
            if ($work->type == 'notice-to-owner' || $work->type == 'amended-notice-to-owner') {
                if ($dif >= 36) {
                    $work->is_rush = 1;
                } else {
                    $work->is_rush = 0;
                }
                $due_at = new \DateTime($job->started_at);
                $mailing_at = new \DateTime($job->started_at);
                $due_diff = new \DateInterval('P43D');
                $mailing_diff = new \DateInterval('P39D');
                $due_at->add($due_diff);
                $mailing_at->add($mailing_diff);

                $work->due_at = $due_at->format('Y-m-d H:i:s');
                $work->mailing_at = $mailing_at->format('Y-m-d H:i:s');
            } else {
                if ($work->type == 'claim-of-lien' || $work->type == 'notice-of-non-payment') {
                    if (strlen($last_day) > 0) {
                        $job_lastday = date_create($last_day);
                        $dif = date_diff($job_lastday, $today)->format('%a');
                        if ($dif >= 86) {
                            $work->is_rush = 1;
                        } else {
                            $work->is_rush = 0;
                        }
                        $due_at = new \DateTime($last_day);
                        $mailing_at = new \DateTime($last_day);
                        $due_diff = new \DateInterval('P89D');
                        $mailing_diff = new \DateInterval('P89D');
                        $due_at->add($due_diff);
                        $mailing_at->add($mailing_diff);

                        $work->due_at = $due_at->format('Y-m-d H:i:s');
                        $work->mailing_at = $mailing_at->format('Y-m-d H:i:s');
                    }
                } else {
                    if ($dif >= 4) {
                        $work->is_rush = 1;
                    } else {
                        $work->is_rush = 0;
                    }
                    $due_at = new \DateTime($job->started_at);
                    $mailing_at = new \DateTime($job->started_at);
                    $due_diff = new \DateInterval('P10D');
                    $mailing_diff = new \DateInterval('P7D');
                    $due_at->add($due_diff);
                    $mailing_at->add($mailing_diff);

                    $work->due_at = $due_at->format('Y-m-d H:i:s');
                    $work->mailing_at = $mailing_at->format('Y-m-d H:i:s');
                }
            }

            $work->save();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function closelink(Request $request, $id)
    {
        $job = Job::findOrFail($id);
        $data = [
            'job' => $job,
        ];

        return view('client.jobs.closelink', $data);
    }

    public function closeJob(Request $request, $id)
    {
        $job = Job::findOrFail($id);
        $this->authorize('wizard', $job);
        $temp_name = $job->name;
        $job->status = 'closed';
        $job->save();

        // redirect
        Session::flash('message', 'Job '.$temp_name.' successfully closed.');
        if ($request->input('redirect_to') == 'jobs') {
            return redirect()->route('client.jobs.edit', $job->id);
        } else {
            return redirect()->to(($request->input('redirect_to')));
        }
    }

    public function setfilter(Request $request)
    {
        if ($request->has('resetfilter')) {
            if ($request->resetfilter == 'true') {
                session()->forget('job_filter');
            }
        }

        if ($request->has('job_name')) {
            if ($request->job_name == '') {
                session()->forget('job_filter.name');
            } else {
                session(['job_filter.name' => $request->job_name]);
            }
        }

        if ($request->has('job_type')) {
            if ($request->job_type == 'all') {
                session()->forget('job_filter.job_type');
            } else {
                session(['job_filter.job_type' => $request->job_type]);
            }
        }

        if ($request->has('job_status')) {
            if ($request->job_status == 'none') {
                session()->forget('job_filter.job_status');
            } else {
                session(['job_filter.job_status' => $request->job_status]);
            }
        }
        if ($request->has('daterange')) {
            if ($request->daterange == '') {
                session()->forget('job_filter.daterange');
            } else {
                session(['job_filter.daterange' => $request->daterange]);
            }
        }

        return redirect()->route('client.jobs.index');
    }

    public function resetfilter(Request $request)
    {
        session()->forget('job_filter');

        return redirect()->route('client.jobs.index');
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

    public function destroy_attachment($id)
    {
        $attachment = Attachment::findOrFail($id);
        $job_id = $attachment->attachable_id;
        if (is_null($attachment->thumb_path)) {
        } else {
            Storage::delete($attachment->thumb_path);
        }
        Storage::delete($attachment->file_path);
        $attachment->delete();

        Session::flash('message', 'Attachment removed');

        return redirect()->route('client.jobs.edit', ['id' => $job_id, '#attachments']);
    }

    public function uploadattachment($id, Request $request)
    {
        if ($request['file'] == null || $request['file'] == '') {
            Session::flash('message', 'file is required.');

            return redirect()->route('client.jobs.edit', ['id' => $id, '#attachments']);
        }

        $job = Job::findOrFail($id);
        // $this->validate($request, [
        //     'file' => 'required|file',
        // ]);

        $attachment = new Attachment();
        $f = $request->file('file');

        $max_uploadfileSize = min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
        $max_uploadfileSize = substr($max_uploadfileSize, 0, -1) * 1024 * 1024;

        if ($f->getSize() > $max_uploadfileSize) {
            Session::flash('message', 'This file is too large to upload.');

            return redirect()->route('client.jobs.edit', ['id' => $id, '#attachments']);
        }

        $attachment->type = $request->input('type');
        $attachment->description = $request->input('description');
        $attachment->original_name = $f->getClientOriginalName();
        $attachment->file_mime = $f->getMimeType();
        $attachment->file_size = $f->getSize();
        $attachment->user_id = Auth::user()->id;
        $job->attachments()->save($attachment);
        $attachment->save();

        $xfilename = 'attachment-'.$attachment->id.'.'.$f->guessExtension();
        $xpath = 'attachments/jobs/'.$id.'/';
        $f->storeAs($xpath, $xfilename);
        $attachment->file_path = $xpath.$xfilename;
        $attachment->save();

        //dd($f->getMimeType());
        switch ($f->getMimeType()) {
            case 'application/pdf':
                //dd($f->getRealPath());
                $xblob = file_get_contents($f->getRealPath());
                $img = new \Imagick();
                $img->readImageBlob($xblob);
                $img->setIteratorIndex(0);
                $img->setImageFormat('png');
                $img->setbackgroundcolor('rgb(64, 64, 64)');
                $img->thumbnailImage(300, 300, true, true);
                Storage::put($xpath.'thumbnail-'.$attachment->id.'.png', $img);
                $attachment->thumb_path = $xpath.'thumbnail-'.$attachment->id.'.png';

                break;
            case 'image/jpeg':
            case 'image/png':
                $xblob = file_get_contents($f->getRealPath());
                $img = new \Imagick();
                $img->readImageBlob($xblob);
                $img->setImageFormat('png');
                $img->setbackgroundcolor('rgb(64, 64, 64)');
                $img->thumbnailImage(300, 300, true, true);
                Storage::put($xpath.'thumbnail-'.$attachment->id.'.png', $img);
                $attachment->thumb_path = $xpath.'thumbnail-'.$attachment->id.'.png';
                break;
            default:
                $attachment->thumb_path = null;
                break;
        }
        $attachment->save();
        $adminEmail = \App\AdminEmails::where('class', 'NewAttachment')->first();
        $adminUserIds = explode(',', $adminEmail->users);
        if (count($adminUserIds) > 0 && $adminEmail->users) {
            $admin_users = User::where('status', 1)->whereIn('id', $adminUserIds)->get();
        } else {
            $admin_users = User::where('status', 1)->isRole(['admin', 'researcher'])->get();
        }

        $data = [
            'note' => 'Have been added to a Job',
            'entered_at' => $attachment->created_at->format('Y-m-d H:i:s'),
        ];
        foreach ($admin_users as $user) {
            Notification::send($user, new NewAttachment($attachment->id, $data, '', Auth::user()->full_name, 'job'));
        }

        if ($job->notify_email) {
            $notify_user = TempUser::create(['email' => $job->notify_email]);
            Notification::send($notify_user, new NewAttachment($attachment->id, $data, '', Auth::user()->full_name, 'job'));
            $notify_user->delete();
        }

        Session::flash('message', 'Attachment added');

        return redirect()->route('client.jobs.edit', ['id' => $id, '#attachments']);
    }

    public function joblist(Request $request)
    {
        $client = Auth::user()->client;
        $search_query = $request->input('term');
        $jobs = $client->jobs()->where('name', 'like', "%$search_query%")->where('deleted_at', null)->get();

        return json_encode($jobs->toArray());
    }

    public function listcontacts($id, Request $request)
    {
        $search_query = $request->input('term');
        $job = Job::findOrFail($id);

        // $this->authorize('wizard',  $job);

        // //$remove_contacts =$job->client->contacts->where('hot_id','<>',0)->pluck('hot_id')->toArray();
        // $remove_contacts= [];
        // $client_contacts = $job->client->contacts->pluck('id')->toArray();
        // //dd($client);
        // $entities= Entity::search($search_query)->where('client_id',$job->client->id)->get()->pluck('id')->toArray();
        // $entities_hot= Entity::search($search_query)->where('client_id',0)->get()->pluck('id')->toArray();
        // $entity_contacts=  \App\ContactInfo::whereIn('entity_id',$entities)->get();
        // $entity_contacts_hot=  \App\ContactInfo::whereIn('entity_id',$entities_hot)->get()->where('use_on_client',1);
        // //dd($entity_contacts_hot);
        // $contacts=  \App\ContactInfo::search($search_query)->get()->where('status',1)->whereIn('id',$client_contacts);
        // $contacts_hot = \App\ContactInfo::search($search_query)->get()->where('status',1)->where('use_on_client',1)->where('is_hot',1);
        // $all_contacts = $contacts->merge($contacts_hot);
        // $all_contacts = $all_contacts->merge($entity_contacts);
        // $all_contacts = $all_contacts->merge($entity_contacts_hot)->sortBy('name_entity_name')->toArray();
        // $all_contacts = array_values($all_contacts);
        // $result=array();
        // foreach ($all_contacts as $ac) {
        //     if ($ac['status']==1 && $ac['entity']['hot_id']!=0){
        //        array_push($result, $ac);
        //     }
        //     if ($ac['status']==1 && $ac['entity']['hot_id']==0){
        //        $ifhot=0;
        //        foreach ($all_contacts as $ac_hot) {
        //            if($ac_hot['entity']['hot_id']=$ac['entity']['id'])
        //            {
        //                $ifhot=1;break;
        //            }
        //        }
        //        if ($ifhot==0) {array_push($result, $ac);}
        //     }

        // }
        // return json_encode($result);

        $remove_contacts = $job->client->contacts->where('hot_id', '<>', 0)->pluck('hot_id');
        $client_contacts = $job->client->contacts->pluck('id')->where('status', 1)->toArray();

        $entities = \App\Entity::search($search_query)->where('client_id', $job->client->id)->get()->pluck('id')->toArray();

        //$entities_local= \App\ContactInfo::search($search_query)->get()->where('status',1)->pluck('entity_id')->toArray();

        $entities_hot = \App\Entity::search($search_query)->where('client_id', 0)->get()->pluck('id')->toArray();

        $entities_hot_all = \App\Entity::where('client_id', 0)->orwhere('client_id', $job->client->id)->get()->pluck('id')->toArray();

        $all_entities = array_merge($entities, $entities_hot);

        $entity_contacts = \App\ContactInfo::whereIn('entity_id', $all_entities)->where('status', 1)->whereNotIn('id', $remove_contacts)->get();

        $contacts = \App\ContactInfo::search($search_query)->get()->where('status', 1)->whereIn('id', $client_contacts)->whereNotIn('id', $remove_contacts);
        $contacts_hot = \App\ContactInfo::search($search_query)->get()->where('status', 1)->where('is_hot', 1)->whereNotIn('id', $remove_contacts);
        $contacts_hot_all = \App\ContactInfo::search($search_query)->get()->where('status', 1)->whereIn('entity_id', $entities_hot_all)->whereNotIn('id', $remove_contacts);

        $all_contacts = $contacts->merge($contacts_hot);
        $all_contacts = $all_contacts->merge($contacts_hot_all);
        $all_contacts = $all_contacts->merge($entity_contacts)->sortBy('name_entity_name')->toArray();

        $all_contacts = array_values($all_contacts);

        $result = [];
        foreach ($all_contacts as $ac) {
            if ($ac['is_hot'] == 1 && $ac['use_on_client'] == 0) {
            } else {
                array_push($result, $ac);
            }
        }

        return json_encode($result);
    }

    public function showattachment($job_id, $id)
    {
        if (count(Attachment::where('id', $id)->get()) < 1) {
            Session::flash('message', 'File no longer exists.');

            return redirect()->route('client.jobs.edit', $job_id);
        }

        $attachment = Attachment::findOrFail($id);
        $contents = Storage::get($attachment->file_path);
        $response = Response::make($contents, '200', [
            'Content-Type' => $attachment->file_mime,
            'Content-Disposition' => 'attachment; filename="'.$attachment->original_name.'"',
        ]);

        return $response;
    }

    public function getaddress(Request $request)
    {
        $county = $request->county;
        $address_1 = $request->address_1;

        return PropertyRecords::where('property_county', $county)->where('property_address1_first_word', $address_1)->get();
    }

    public function save_property(Request $request, $id)
    {
        $job = Job::where('id', $id)->first();
        if (count($job) == 0) {
            Session::flash('message', 'Job already deleted.');

            return redirect()->route('client.jobs.index');
        }
        $client = $job->client;
        $data = $request->all();
        if (strpos($job->folio_number, $data['folio_number']) === false) {
            if ($job->folio_number) {
                $data['folio_number'] = $job->folio_number.'/'.$data['folio_number'];
            }
        } else {
            $data['folio_number'] = $job->folio_number;
        }
        if (strpos($job->legal_description, $data['legal_description']) === false) {
            if ($job->legal_description) {
                $data['legal_description'] = $job->legal_description."\n".$data['legal_description'];
            }
        } else {
            $data['legal_description'] = $job->legal_description;
        }
        $changeArray = $job->getChanges($data);
        $changes = json_encode($changeArray);
        $job->update($data);
        if (count($changeArray) > 0) {
            JobLog::create([
                'job_id' => $job->id,
                'user_id' => Auth::user()->id,
                'user_name' => Auth::user()->fullName,
                'edited_at' => Carbon::now(),
                'data' => $changes,
            ]);
        }

        $owner_name = $request->owner_name;
        $owner_address_1 = $request->owner_address_1;
        $owner_address_2 = $request->owner_address_2;
        $owner_city = $request->owner_city;
        $owner_state = $request->owner_state;
        $owner_zip = $request->owner_zip;

        //=== pull landowner from owner data on property_records
        if ($owner_name) {
            $contacts = $client->contacts;
            $matched = false;
            foreach ($contacts as $contact) {
                $entity_contact = Entity::where('id', $contact->entity_id)->first();
                $first_name = '';
                $last_name = '';

                if ($entity_contact->firm_name == strtoupper($owner_name) && $contact->first_name == $first_name && $contact->last_name == $last_name && (substr($contact->address_1, 0, 5) == strtoupper(substr($owner_address_1, 0, 5)) || $contact->address_1 == $owner_address_1) && $contact->city == strtoupper($owner_city) && $contact->zip == $owner_zip) {
                    $entity_contact->latest_type = 'owner';
                    $entity_contact->save();

                    $landowners = $job->parties->where('contact_id', $contact->id)->where('type', 'landowner');
                    if (count($landowners) == 0) {
                        $data['entity_id'] = $entity_contact->id;
                        $data['contact_id'] = $contact->id;
                        $data['type'] = 'landowner';
                        $data['job_id'] = $job->id;
                        $landowner_deed_number = '';
                        $data['source'] = 'OTHR';
                        $data['landowner_deed_number'] = $landowner_deed_number;
                        $newJobParty = JobParty::create($data);
                    }

                    $matched = true;
                }
            }

            if (! $matched) {
                $data['firm_name'] = strtoupper($owner_name);
                $data['latest_type'] = 'owner';
                $data['client_id'] = $job->client_id;
                $data['is_hot'] = 0;
                $data['hot_id'] = 0;

                $entity = Entity::create($data);

                $xdata['first_name'] = '';
                $xdata['last_name'] = '';
                $xdata['gender'] = 'none';
                $xdata['address_1'] = strtoupper($owner_address_1);
                $xdata['address_2'] = strtoupper($owner_address_2);
                $xdata['city'] = strtoupper($owner_city);
                $xdata['state'] = strtoupper($owner_state);
                $xdata['zip'] = $owner_zip;
                $xdata['country'] = 'USA';
                if (strlen($owner_state) > 2) {
                    $xdata['country'] = strtoupper($owner_state);
                    $xdata['state'] = '';
                }

                $new_contact = ContactInfo::create($xdata);
                $new_contact->entity_id = $entity->id;
                $new_contact->primary = 1;
                $new_contact->save();

                $xdata['entity_id'] = $entity->id;
                $xdata['contact_id'] = $new_contact->id;
                $xdata['type'] = 'landowner';
                $xdata['job_id'] = $job->id;

                $landowner_deed_number = '';
                $xdata['landowner_deed_number'] = $landowner_deed_number;
                $newJobParty = JobParty::create($xdata);
            }

            $job->search_status = 'done';
            $job->save();
            Session::flash('message', 'Property Search Succeeded.');

            return redirect()->route('client.jobs.edit', $job->id);
        }
        $job->save();
        Session::flash('message', 'Property Search Failed.');

        return redirect()->route('client.jobs.edit', $job->id);
    }

    public function share(Request $request, $job_id)
    {
        $email = $request->email;
        $user_id = $request->user_id;
        if ($email) {
            $user = User::where('email', $email)->first();
            if (! $user) {
                Session::flash('message', 'Error: You tried to share the job to invalid user email.');

                return redirect()->back();
            }
        } elseif ($user_id) {
            $user = User::where('id', $user_id)->first();
            if (! $user) {
                Session::flash('message', 'Error: The user was deleted already.');

                return redirect()->back();
            }
        } else {
            Session::flash('message', 'Error: User does not exist.');

            return redirect()->back();
        }
        $job = Job::where('id', $job_id)->where('deleted_at', null)->first();
        if (! $job) {
            Session::flash('message', 'Error: The job was deleted.');

            return redirect()->back();
        }
        if ($user->client && ! $user->client->is_monitoring_user) {
            Notification::send($user, new ShareJobToMornitoringUser($job, Auth::user(), false));
            $admin = new User();
            $admin->email = 'Suzanne@sunshinenotices.com';
            Notification::send($admin, new ShareJobToMornitoringUser($job, Auth::user(), false));
            Session::flash('message', 'Error: The user is not a mornitoring user. Will you set up the user to mornitoring user?');

            return redirect()->back();
        }

        $shared = SharedJobToUser::where('user_id', $user->id)->where('job_id', $job_id)->first();
        if ($shared) {
            Session::flash('message', 'Error: The job was already shared to '.$user->full_name);

            return redirect()->back();
        }

        $shared = SharedJobToUser::create(
            [
                'user_id' => $user->id,
                'job_id' => $job_id,
            ]
        );
        if ($user_id) {
            $shared->is_primary = 0;
            $shared->save();
        }
        Notification::send($user, new ShareJobToMornitoringUser($job, Auth::user(), true));
        Session::flash('message', 'The job was shared to '.$user->full_name);

        return redirect()->back();
    }

    public function shareTeam(Request $request, $job_id)
    {
        $users = $request->users;
        $team = $request->team;
        if (! $team) {
            Session::flash('message', 'Error: This client does not have other users.');

            return redirect()->back();
        }
        $job = Job::where('id', $job_id)->where('deleted_at', null)->first();
        if (! $job) {
            Session::flash('message', 'Error: The job was deleted.');

            return redirect()->back();
        }

        $users = explode(',', $users);
        $team = explode(',', $team);
        $usersShared = [];
        $usersAlreadyShared = [];
        $unSharedUsers = [];
        foreach ($team as $user_id) {
            if (! in_array($user_id, $users)) {
                foreach (SharedJobToUser::where('user_id', $user_id)->where('job_id', $job_id)->get() as $shared) {
                    $shared->delete();
                    $unSharedUsers[] = $user_id;
                }
                continue;
            }
            $user = User::where('id', $user_id)->first();
            if (! $user) {
                continue;
            }

            if ($user->client && ! $user->client->is_monitoring_user) {
                Notification::send($user, new ShareJobToMornitoringUser($job, Auth::user(), false));
                $admin = new User();
                $admin->email = 'Suzanne@sunshinenotices.com';
                Notification::send($admin, new ShareJobToMornitoringUser($job, Auth::user(), false));
                continue;
            }

            $shared = SharedJobToUser::where('user_id', $user->id)->where('job_id', $job_id)->first();
            if ($shared) {
                $usersAlreadyShared[] = $user->full_name;
                continue;
            }

            $shared = SharedJobToUser::create(
                [
                    'user_id' => $user->id,
                    'job_id' => $job_id,
                ]
            );
            if ($user_id) {
                $shared->is_primary = 0;
                $shared->save();
            }
            Notification::send($user, new ShareJobToMornitoringUser($job, Auth::user(), true));
            $usersShared[] = $user->full_name;
        }
        $msg = '';
        if (count($usersShared) > 0) {
            $msg = 'The job was shared to '.implode(',', $usersShared).'. ';
        }
        if (count($usersAlreadyShared) > 0) {
            $msg = $msg.'The job was already shared to '.implode(',', $usersAlreadyShared).'. ';
        }
        if (count($unSharedUsers) > 0) {
            $msg = $msg.'The job was un-shared from '.count($unSharedUsers).' user(s).';
        }

        Session::flash('message', $msg);

        return redirect()->back();
    }

    public function shareToUser($job_id, $user_id)
    {
        $user = User::where('id', $user_id)->first();
        if (! $user) {
            Session::flash('message', 'Error: the requested user was deleted.');

            return redirect()->route('client.jobs.edit', $job_id);
        }
        $job = Job::where('id', $job_id)->where('deleted_at', null)->first();
        $this->authorize('wizard', $job);
        if (! $job) {
            Session::flash('message', 'Error: The job was deleted.');

            return redirect()->route('client.jobs.edit', $job_id);
        }
        if ($user->client && ! $user->client->is_monitoring_user) {
            Session::flash('message', 'Error: The requested user was canceled mornitoring user.');

            return redirect()->route('client.jobs.edit', $job_id);
        }

        $shared = SharedJobToUser::where('user_id', $user_id)->where('job_id', $job_id)->first();
        if ($shared) {
            Session::flash('message', 'Error: The job was already shared to '.$user->full_name);

            return redirect()->route('client.jobs.edit', $job_id);
        }

        $shared = SharedJobToUser::create(
            [
                'user_id' => $user->id,
                'job_id' => $job_id,
            ]
        );
        Notification::send($user, new ShareJobToMornitoringUser($job, Auth::user(), true));
        Session::flash('message', 'The job was shared to '.$user->full_name);

        return redirect()->route('client.jobs.edit', $job_id);
    }
}
