<?php

namespace App\Http\Controllers\Admin;

use App\Attachment;
use App\AttachmentType;
use App\Client;
use App\Entity;
use App\Http\Controllers\Controller;
use App\Invoice;
use App\InvoiceLine;
use App\Job;
use App\JobParty;
use App\Mail\NoticeComplete;
use App\Note;
use App\Notifications\NewAttachment;
use App\Notifications\SendWorkorderLink;
use App\Notifications\TodoItemCompleted;
use App\Template;
use App\TempUser;
use App\Todo;
use App\TodoDocument;
use App\TodoInstruction;
use App\User;
use App\WorkOrder;
use App\WorkOrderAnswers;
use App\WorkOrderFields;
use App\WorkOrderType;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Imagick;
use Mail;
use Response;
use Session;
use Storage;

class WorkOrdersController extends Controller
{
    private $wo_types;

    // private $statuses = [
    //        'open' => 'Open',
    //        'cancelled' => 'Cancelled',
    //        'cancelled charge' => 'Cancelled Charge',
    //        'search' => 'Search',
    //        'tax rolls' => 'Tax Rolls',
    //        'phone calls' => 'Phone Calls',
    //        'atids' => 'Title Search',
    //        'pending' => 'Pending',
    //        'data entry' => 'Data Entry',
    //        'edit' => 'Edit',
    //        'completed' => 'Completed',
    //        'print' => 'Print',
    //        'cancelled no charge' => 'Cancelled No Charge',
    //        'closed' => 'Closed',
    //        'payment pending' => 'Payment Pending'
    //     ];
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

    public function __construct()
    {
        $this->wo_types = WorkOrderType::all()->pluck('name', 'slug')->toArray();
    }

    /**
     * Display a listing of the resource. full-service
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if ($request['rush'] == '1') {
            session()->forget('work_order_filter');
            session(['work_order_filter.work_rush' => '1']);
        }
        if ($request['fromDashboard'] == '1') {
            session()->forget('work_order_filter');
        }

        $clients = Client::get()->sortby('company_name')->pluck('company_name', 'id')->prepend('All', 0);

        $works = WorkOrder::query()->where(function ($q) {
            $q->where('service', null)->orwhere('service', 'full');
        });

        if (session()->has('work_order_filter.client')) {
            $xclient = session('work_order_filter.client');
            $works->whereHas('job', function ($q) use ($xclient) {
                return $q->where('client_id', $xclient);
            });
        }

        $jobs_list = [0 => 'All'];

        if (session()->has('work_order_filter.job')) {
            $job_id = session('work_order_filter.job');
            if ($job_id) {
                $works->where('job_id', $job_id);
                $xjob = Job::where('id', $job_id)->first();
                $jobs_list = [$job_id => $xjob ? $xjob->name : ''];
            } else {
                session()->forget('work_order_filter.job');
            }
        }

        if (session()->has('work_order_filter.work_type')) {
            if (session('work_order_filter.work_type') != 'all') {
                $works->where('type', session('work_order_filter.work_type'));
            }
        }

        if (session()->has('work_order_filter.work_rush')) {
            if (session('work_order_filter.work_rush') != 'all') {
                $works->where('is_rush', session('work_order_filter.work_rush'));
            }
        }

        if (session()->has('work_order_filter.work_status')) {
            if (session('work_order_filter.work_status') != 'all') {
                $works->where('status', session('work_order_filter.work_status'));
            } else {
                $works->where('status', '!=', 'temporary');
            }
        } else {
            $works->where('status', '!=', 'temporary');
        }

        if (session()->has('work_order_filter.work_condition')) {
            switch (session('work_order_filter.work_condition')) {
               case 1:
                    $works->whereNotIn('status', ['completed', 'cancelled', 'cancelled charge', 'cancelled no charge', 'closed', 'cancelled duplicate', 'cancelled duplicate needs credit']);
                    break;
               case 2:
                   $works->whereIn('status', ['completed', 'cancelled', 'cancelled charge', 'cancelled no charge', 'closed', 'cancelled duplicate', 'cancelled duplicate needs credit']);
                    break;
               default:
           }
        } else {
            session(['work_order_filter.work_condition' => 1]);
            $works->whereNotIn('status', ['completed', 'cancelled', 'cancelled charge', 'cancelled no charge', 'closed', 'cancelled duplicate', 'cancelled duplicate needs credit']);
        }

        if (session()->has('work_order_filter.daterange')) {
            if (session('work_order_filter.daterange') != '') {
                $date_range = session('work_order_filter.daterange');
                $date_type = '/^[0-1][0-9]-[0-3][0-9]-[0-9]{4} - [0-1][0-9]-[0-3][0-9]-[0-9]{4}$/';

                if (preg_match($date_type, $date_range)) {
                    $dates = explode(' - ', session('work_order_filter.daterange'));

                    $from_date = \Carbon\Carbon::createFromFormat('m-d-Y', $dates[0]);
                    $to_date = \Carbon\Carbon::createFromFormat('m-d-Y', $dates[1]);
                    $from = substr($from_date, 0, 10).' 00:00:00';
                    $to = substr($to_date, 0, 10).' 23:59:59';
                    $works->where([['mailing_at', '>=', $from], ['mailing_at', '<=', $to]])->orderBy('mailing_at', 'desc');
                    Session::flash('message', null);
                } else {
                    Session::flash('message', 'Input not in expected range format');
                }

                //$works->whereBetween('due_at',[$from_date,$to_date])->orderBy('due_at');
            }
        }

        if (session()->has('work_order_filter.work_number')) {
            if (session('work_order_filter.work_number') != '') {
                $works->where('id', session('work_order_filter.work_number'));
            }
        }

        if (session()->has('work_order_filter.job_number')) {
            if (session('work_order_filter.job_number') != '') {
                $xjob_number = session('work_order_filter.job_number');
                $works->whereHas('job', function ($q) use ($xjob_number) {
                    return $q->where('number', 'like', '%'.$xjob_number.'%');
                });
            }
        }
        if (session()->has('work_order_filter.job_address')) {
            if (session('work_order_filter.job_address') != '') {
                $xjob_address = session('work_order_filter.job_address');
                $works->whereHas('job', function ($q) use ($xjob_address) {
                    return $q->where('address_1', 'like', '%'.$xjob_address.'%')->orwhere('address_2', 'like', '%'.$xjob_address.'%')->orwhere('city', 'like', '%'.$xjob_address.'%')->orwhere('zip', 'like', '%'.$xjob_address.'%');
                });
            }
        }
        if (session()->has('work_order_filter.job_county')) {
            if (session('work_order_filter.job_county') != '') {
                $xjob_county = session('work_order_filter.job_county');
                $works->whereHas('job', function ($q) use ($xjob_county) {
                    return $q->where('county', 'like', '%'.$xjob_county.'%');
                });
            }
        }

        $customers = [0 => 'All'];
        if (session()->has('work_order_filter.customer_name')) {
            $entityId = session('work_order_filter.customer_name');
            if ($entityId) {
                $job_ids = JobParty::where('type', 'customer')->where('entity_id', $entityId)->get()->pluck('job_id')->toArray();
                if (count($job_ids) > 0) {
                    $works->whereIn('job_id', $job_ids);
                }
            }
            $xentity = Entity::where('id', $entityId)->first();
            $customers = [$entityId => $xentity ? $xentity->firm_name : ''];
        }

        $works = $works->orderBy(\DB::raw("case when status in ('completed','cancelled','cancelled charge','cancelled no charge','closed') then 0 else is_rush end"), 'desc')->orderBy('id', 'DESC')->paginate(15);

        $available_notices = [
            'amend-claim-of-lien',
            'amended-notice-to-owner',
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
            'notice-of-nonpayment-for-bonded-private-jobs-statutes-713',
            'notice-of-nonpayment-for-government-jobs-statutes-255',
            'notice-of-nonpayment-with-intent-to-lien-andor-foreclose',
            'partial-satisfaction-of-lien',
            'out-of-state-nto-preliminary-notice-of-lien-rights',
            'rescission-letter',
            'satisfaction-of-lien',
            'sworn-statement-of-account',
            'waiver-and-release-of-lien-upon-final-payment',
            'waiver-and-release-of-lien-upon-progress-payment',
            'waiver-of-right-to-claim-against-bond-final-payment',
            'waiver-of-right-to-claim-against-bond-progress-payment',
        ];

        $conditions = [
            '0' => 'All',
            '1' => 'Open',
            '2' => 'Close',
        ];
        Session::put('backUrl', \URL::full());

        //    $customers=array_filter(JobParty::where('type','customer')
        //                     ->with('firm')->get()
        //                     ->pluck('firm.firm_name','firm.firm_name')
        //                     ->prepend('All','')->toArray());
        //    ksort($customers);

        $data = [
            'clients' => $clients,
            'works' => $works,
            'customers' => $customers,
            'wo_types' => ['all' => 'All'] + $this->wo_types,
            'statuses' => ['all' => 'All'] + $this->statuses,
            'conditions' => $conditions,
            'jobs' => $jobs_list,
            'available_notices' => $available_notices,
        ];

        return view('admin.workorders.index', $data);
    }

    /**
     * Display a listing of the resource. self-service
     *
     * @return Response
     */
    public function index2(Request $request)
    {
        if ($request['rush'] == '1') {
            session()->forget('work_order_filter2');
            session(['work_order_filter2.work_rush' => '1']);
        }
        if ($request['fromDashboard'] == '1') {
            session()->forget('work_order_filter2');
        }

        $clients = Client::get()->sortby('company_name')->pluck('company_name', 'id')->prepend('All', 0);

        $works = WorkOrder::query()->where('service', 'self');

        if (session()->has('work_order_filter2.client')) {
            $xclient = session('work_order_filter2.client');
            $works->whereHas('job', function ($q) use ($xclient) {
                return $q->where('client_id', $xclient);
            });
        }

        $jobs_list = [0 => 'All'];

        if (session()->has('work_order_filter2.job')) {
            $job_id = session('work_order_filter2.job');
            if ($job_id) {
                $works->where('job_id', $job_id);
                $xjob = Job::where('id', $job_id)->first();
                $jobs_list = [$job_id => $xjob ? $xjob->name : ''];
            } else {
                session()->forget('work_order_filter2.job');
            }
        }

        if (session()->has('work_order_filter2.work_type')) {
            if (session('work_order_filter2.work_type') != 'all') {
                $works->where('type', session('work_order_filter2.work_type'));
            }
        }

        if (session()->has('work_order_filter2.work_rush')) {
            if (session('work_order_filter2.work_rush') != 'all') {
                $works->where('is_rush', session('work_order_filter2.work_rush'));
            }
        }

        if (session()->has('work_order_filter2.work_status')) {
            if (session('work_order_filter2.work_status') != 'all') {
                $works->where('status', session('work_order_filter2.work_status'));
            } else {
                $works->where('status', '!=', 'temporary');
            }
        } else {
            $works->where('status', '!=', 'temporary');
        }

        if (session()->has('work_order_filter2.work_condition')) {
            switch (session('work_order_filter2.work_condition')) {
               case 1:
                    $works->whereNotIn('status', ['completed', 'cancelled', 'cancelled charge', 'cancelled no charge', 'closed', 'cancelled duplicate', 'cancelled duplicate needs credit']);
                    break;
               case 2:
                   $works->whereIn('status', ['completed', 'cancelled', 'cancelled charge', 'cancelled no charge', 'closed', 'cancelled duplicate', 'cancelled duplicate needs credit']);
                    break;
               default:
           }
        } else {
            session(['work_order_filter2.work_condition' => 1]);
            $works->whereNotIn('status', ['completed', 'cancelled', 'cancelled charge', 'cancelled no charge', 'closed', 'cancelled duplicate', 'cancelled duplicate needs credit']);
        }

        if (session()->has('work_order_filter2.daterange')) {
            if (session('work_order_filter2.daterange') != '') {
                $date_range = session('work_order_filter2.daterange');
                $date_type = '/^[0-1][0-9]-[0-3][0-9]-[0-9]{4} - [0-1][0-9]-[0-3][0-9]-[0-9]{4}$/';

                if (preg_match($date_type, $date_range)) {
                    $dates = explode(' - ', session('work_order_filter2.daterange'));

                    $from_date = \Carbon\Carbon::createFromFormat('m-d-Y', $dates[0]);
                    $to_date = \Carbon\Carbon::createFromFormat('m-d-Y', $dates[1]);
                    $from = substr($from_date, 0, 10).' 00:00:00';
                    $to = substr($to_date, 0, 10).' 23:59:59';
                    $works->where([['mailing_at', '>=', $from], ['mailing_at', '<=', $to]])->orderBy('mailing_at', 'desc');
                    Session::flash('message', null);
                } else {
                    Session::flash('message', 'Input not in expected range format');
                }

                //$works->whereBetween('due_at',[$from_date,$to_date])->orderBy('due_at');
            }
        }

        if (session()->has('work_order_filter2.work_number')) {
            if (session('work_order_filter2.work_number') != '') {
                $works->where('id', session('work_order_filter2.work_number'));
            }
        }

        if (session()->has('work_order_filter2.job_number')) {
            if (session('work_order_filter2.job_number') != '') {
                $xjob_number = session('work_order_filter2.job_number');
                $works->whereHas('job', function ($q) use ($xjob_number) {
                    return $q->where('number', 'like', '%'.$xjob_number.'%');
                });
            }
        }
        if (session()->has('work_order_filter2.job_address')) {
            if (session('work_order_filter2.job_address') != '') {
                $xjob_address = session('work_order_filter2.job_address');
                $works->whereHas('job', function ($q) use ($xjob_address) {
                    return $q->where('address_1', 'like', '%'.$xjob_address.'%')->orwhere('address_2', 'like', '%'.$xjob_address.'%')->orwhere('city', 'like', '%'.$xjob_address.'%')->orwhere('zip', 'like', '%'.$xjob_address.'%');
                });
            }
        }
        if (session()->has('work_order_filter2.job_county')) {
            if (session('work_order_filter2.job_county') != '') {
                $xjob_county = session('work_order_filter2.job_county');
                $works->whereHas('job', function ($q) use ($xjob_county) {
                    return $q->where('county', 'like', '%'.$xjob_county.'%');
                });
            }
        }

        $customers = [0 => 'All'];
        if (session()->has('work_order_filter2.customer_name')) {
            $entityId = session('work_order_filter2.customer_name');
            if ($entityId) {
                $job_ids = JobParty::where('type', 'customer')->where('entity_id', $entityId)->get()->pluck('job_id')->toArray();
                if (count($job_ids) > 0) {
                    $works->whereIn('job_id', $job_ids);
                }
            }
            $xentity = Entity::where('id', $entityId)->first();
            $customers = [$entityId => $xentity ? $xentity->firm_name : ''];
        }

        $works = $works->orderBy('has_todo', 'desc')->orderBy('id', 'DESC')->paginate(15);

        $available_notices = [
            'amend-claim-of-lien',
            'amended-notice-to-owner',
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
            'notice-of-nonpayment-for-bonded-private-jobs-statutes-713',
            'notice-of-nonpayment-for-government-jobs-statutes-255',
            'notice-of-nonpayment-with-intent-to-lien-andor-foreclose',
            'partial-satisfaction-of-lien',
            'out-of-state-nto-preliminary-notice-of-lien-rights',
            'rescission-letter',
            'satisfaction-of-lien',
            'sworn-statement-of-account',
            'waiver-and-release-of-lien-upon-final-payment',
            'waiver-and-release-of-lien-upon-progress-payment',
            'waiver-of-right-to-claim-against-bond-final-payment',
            'waiver-of-right-to-claim-against-bond-progress-payment',
        ];

        $conditions = [
            '0' => 'All',
            '1' => 'Open',
            '2' => 'Close',
        ];
        Session::put('backUrl', \URL::full());

        $data = [
            'clients' => $clients,
            'works' => $works,
            'customers' => $customers,
            'wo_types' => ['all' => 'All'] + $this->wo_types,
            'statuses' => ['all' => 'All'] + $this->statuses,
            'conditions' => $conditions,
            'jobs' => $jobs_list,
            'available_notices' => $available_notices,
        ];

        return view('admin.workorders.index2', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $job_id = '';
        $job_name = '';
        if ($request->has('job_id')) {
            $job_id = $request->input('job_id');
            session(['work_order_filter.job' => $request->input('job_id')]);
            $job = Job::where('id', $job_id)->first();
            if ($job) {
                $job_name = $job->name;
            }
        } else {
            if (session()->has('work_order_filter.job')) {
                $job_id = session('work_order_filter.job');
            } else {
                $job_id = '';
            }
        }
        // $jobs_list = Job::where('status','!=','closed')->orwhere('status',null);
        // $jobs_list = $jobs_list->pluck('name','id')->toArray();

        $jobs_list = [];

        $question_list = WorkOrderFields::where('workorder_type', 'notice-to-owner')->orderBy('field_order')->get();
        $admin_users = User::where('deleted_at', null)->whereHas('roles', function ($p) {
            return $p->whereIn('name', ['admin', 'researcher']);
        })->get()->pluck('full_name', 'id')->prepend('', '')->toArray();

        $data = [
            'jobs_list' => $jobs_list,
            'wo_types' => $this->wo_types,
            'job_id' => $job_id,
            'job_name' => $job_name,
            'question_list' => $question_list,
            'admin_users' => $admin_users,
        ];

        return view('admin.workorders.create', $data);
    }

    //for ajax on create page
    public function getfields(Request $request)
    {
        if (! $request['work_order_id']) {
            $question_list = WorkOrderFields::where('workorder_type', $request->work_order_type)->orderBy('field_order')->get();

            return response()->json($question_list);
        } else {
            $work = WorkOrder::findOrFail($request['work_order_id']);
            $answer_list = WorkOrderAnswers::where('work_order_id', $request['work_order_id'])->pluck('answer', 'work_order_field_id');
            $question_list = WorkOrderFields::where('workorder_type', $request->work_order_type)->where('created_at', '<', $work->created_at)->orderBy('field_order')->get();

            $data['answer'] = $answer_list;
            $data['question'] = $question_list;

            return response()->json($data);
        }
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
            'due_at' => 'required|date|date_format:m/d/Y',
            'job_id' => 'required|exists:jobs,id',
            'type' => 'required',

        ]);

        $data = $request->all();
        $job = Job::findOrFail($request->job_id);
        if ($request->has('is_rush')) {
            $data['is_rush'] = 1;
        } else {
            $data['is_rush'] = 0;
        }
        $data['due_at'] = date('Y-m-d', strtotime($data['due_at']));
        $data['mailing_at'] = date('Y-m-d', strtotime($data['mailing_at']));
        if ($job->client->billing_type == 'invoiced') {
            $data['status'] = 'open';
        } else {
            $data['status'] = 'pending';
        }

        if ($request->has('last_day')) {
            if (strlen(trim($data['last_day'])) > 0) {
                $job->last_day = date('Y-m-d', strtotime($data['last_day']));
                $job->save();
            }
        }
        $wo = WorkOrder::create($data);
        $wo->created_by = Auth::user()->id;
        $wo->responsible_user = $data['responsible_user'];
        $wo->service = $job->client->service;
        $wo->save();

        $job->status = $wo->type;
        if (count($job->workorders) == 1 && $job->search_status == 'new') {
            $job->search_status = 'done';
            $job->save();
            $note = new Note();
            $now = \Carbon\Carbon::now();
            $note_text = 'Original Address: '.$job->address_1.' '.$job->address_2.', '.$job->city.', '.$job->state.' '.$job->zip_code;
            $note->note_text = $note_text;
            $note->entered_at = $now->toDateTimeString();
            $note->entered_by = 1;
            $note->viewable = 0;
            $note->noteable_type = 'App\Job';
            $note->client_id = $job->client->id;
            $note = $job->notes()->save($note);
        }
        $job->save();

        if ($request->input('answer')) {
            foreach ($request->input('answer') as $key => $answer) {
                $answer_data = [
                    'work_order_id' => $wo->id,
                    'work_order_field_id' => $key,
                    'answer' => $request->answer[$key],
                ];

                $answer = WorkOrderAnswers::create($answer_data);
            }
        }

        Session::flash('message', 'Work Order '.$wo->number.' created');

        return redirect()->route('workorders.edit', $wo->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $work = WorkOrder::findOrFail($id);

        $attachments = $work->attachments->where('type', 'generated');
        //dd($attachments);
        if (count($attachments) > 0) {
            $data = [
                'id' => $id,
                'attachments' => $attachments,
            ];

            return view('admin.workorders.show', $data);
        } else {
            return redirect()->back();
        }
    }

    public function view($work_order_id, $id)
    {
        $attachment = Attachment::findOrFail($id);
        //dd($attachment->file_path);
        $content = Storage::get($attachment->file_path);

        return Response::make($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$attachment->original_filename.'"',
        ]);
    }

    /**
     * Display the document.
     *
     * @param  int  $id
     * @return Response
     */

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $existsWorkorder = WorkOrder::where('id', $id)->get();
        if (count($existsWorkorder) < 1) {
            Session::flash('message', 'Work Order 00000'.$id.' has been deleted already.');

            return redirect()->route('workorders.index');
        }

        $available_notices = [
            'amend-claim-of-lien',
            'amended-notice-to-owner',
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
            'notice-of-nonpayment-for-bonded-private-jobs-statutes-713',
            'notice-of-nonpayment-for-government-jobs-statutes-255',
            'notice-of-nonpayment-with-intent-to-lien-andor-foreclose',
            'partial-satisfaction-of-lien',
            'out-of-state-nto-preliminary-notice-of-lien-rights',
            'rescission-letter',
            'satisfaction-of-lien',
            'sworn-statement-of-account',
            'waiver-and-release-of-lien-upon-final-payment',
            'waiver-and-release-of-lien-upon-progress-payment',
            'waiver-of-right-to-claim-against-bond-final-payment',
            'waiver-of-right-to-claim-against-bond-progress-payment',
        ];
        if (session()->has('note')) {
            $xnote = session('note');
        } else {
            $xnote = '';
        }

        $work = WorkOrder::findOrFail($id);
        //dd($work->invoicesPending);
        $client_id = $work->job->client_id;
        if (count($work->invoices) > 0) {
            $jobs_list = Job::where('status', '!=', 'closed')->orwhere('status', null)->get()->where('client_id', $client_id)->pluck('name', 'id')->toArray();
        } else {
            $jobs_list = Job::where('status', '!=', 'closed')->orwhere('status', null)->get()->pluck('name', 'id')->toArray();
        }
        if ($work->job->status == 'closed') {
            //$closed_job=array($work->job->id =>$work->job->name);
            $jobs_list[$work->job->id] = $work->job->name;
        }

        $attachment_types = AttachmentType::where('slug', '!=', 'generated')->get()->pluck('name', 'slug');

        $question_list = WorkOrderFields::where('workorder_type', $work->type)->where('created_at', '<', $work->created_at)->orderBy('field_order')->get();
        $answer_list = WorkOrderAnswers::where('work_order_id', $work->id)->pluck('answer', 'work_order_field_id');

        $users = $work->job->client->activeusers->pluck('full_name', 'id')->prepend('', '');
        $admin_users = User::where('deleted_at', null)->whereHas('roles', function ($p) {
            return $p->whereIn('name', ['admin', 'researcher']);
        })->get()->pluck('full_name', 'id')->prepend('', '')->toArray();

        $data = [
            'jobs_list' => $jobs_list,
            'wo_types' => $this->wo_types,
            'statuses' => $this->statuses,
            'work' => $work,
            'attachment_types' => $attachment_types,
            'parties_type' => $this->parties_type,
            'xnote' => $xnote,
            'question_list' => $question_list,
            'answer_list' => $answer_list,
            'available_notices' => $available_notices,
            'users' => $users,
            'admin_users' => $admin_users,
        ];

        return view('admin.workorders.edit', $data);
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
            'due_at' => 'required|date|date_format:m/d/Y',
            'job_id' => 'required|exists:jobs,id',
            'type' => 'required',
            'status' => 'required',
        ]);
        $wo = WorkOrder::findOrFail($id);
        $wo_type = $wo->type;
        $data = $request->all();

        if ($request->has('is_rush')) {
            $data['is_rush'] = 1;
        } else {
            $data['is_rush'] = 0;
        }
        $data['due_at'] = date('Y-m-d', strtotime($data['due_at']));
        $data['mailing_at'] = date('Y-m-d', strtotime($data['mailing_at']));

        if ($wo->status != 'completed') {
            if ($request->status == 'completed') {
                $mailto = [];
                $client = $wo->job->client;
                if ($client->notification_setting == 'immediate') {
                    if (json_encode(unserialize($client->override_notice)) != 'false' && json_encode(unserialize($client->override_notice)) != 'null') {
                        Mail::to(unserialize($client->override_notice))->send(new NoticeComplete($wo->id, $wo->invoicesPending));
                    } else {
                        $mailto = [];
                        $responsible_user = User::where('id', $wo->responsible_user)->first();
                        if ($wo->responsible_user && count($responsible_user) > 0) {
                            $mailto[] = $responsible_user->email;
                        } else {
                            $users = $client->activeusers;
                            foreach ($users as $user) {
                                $mailto[] = $user->email;
                            }
                        }
                        if (count($mailto) > 0) {
                            Mail::to($mailto)->send(new NoticeComplete($wo->id, $wo->invoicesPending));
                        }
                    }
                }
                if ($wo->job->notify_email) {
                    Mail::to($wo->job->notify_email)->send(new NoticeComplete($wo->id, $wo->invoicesPending));
                }
            }
        }

        $wo->update($data);

        $question_list = WorkOrderFields::where('workorder_type', $wo->type)->orderBy('field_order')->get();
        $answer_list = WorkOrderAnswers::where('work_order_id', $wo->id)->pluck('answer', 'work_order_field_id');

        if ($request->input('answer')) {
            if ($wo_type == $wo->type) {
                foreach ($request->input('answer') as $key => $answer) {
                    $answer_data = [
                        'work_order_id' => $wo->id,
                        'work_order_field_id' => $key,
                        'answer' => $request->answer[$key],
                    ];
                    if (isset($answer_list[$key])) {
                        $answer = WorkOrderAnswers::where('work_order_id', $wo->id)->where('work_order_field_id', $key)->first();
                        $answer->update($answer_data);
                    } else {
                        $answer = WorkOrderAnswers::create($answer_data);
                    }
                }
            } else {
                WorkOrderAnswers::where('work_order_id', $wo->id)->delete();
                foreach ($request->input('answer') as $key => $answer) {
                    $answer_data = [
                        'work_order_id' => $wo->id,
                        'work_order_field_id' => $key,
                        'answer' => $request->answer[$key],
                    ];

                    $answer = WorkOrderAnswers::create($answer_data);
                }
            }
        }

        Session::flash('message', 'Work Order '.$wo->number.' updated');

        return redirect()->route('workorders.edit', $wo->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $work = WorkOrder::findOrFail($id);
        $wo_id = $work->id;
        $invoice = Invoice::where('work_order_id', $wo_id)->get();
        $len = count($invoice);

        // echo json_encode($invoice);
        // return;

        if ($len > 0) {
            Session::flash('message', 'This work order has an invoice. Please delete the invoice first before deleting this work order.');
        } else {
            $temp_name = $work->number;
            $work->delete();

            // redirect
            Session::flash('message', 'Work Order  '.$temp_name.' successfully deleted.');
        }

        return redirect()->route('workorders.index');
    }

    public function uploadattachment($id, Request $request)
    {
        if ($request['file'] == null || $request['file'] == '') {
            Session::flash('message', 'file is required.');

            return redirect()->route('workorders.edit', ['id' => $id, '#attachments']);
        }

        $work = WorkOrder::findOrFail($id);
        $client = $work->job->client;
        $job = $work->job;

        $attachment = new Attachment();
        $f = $request->file('file');

        $max_uploadfileSize = min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
        $max_uploadfileSize = substr($max_uploadfileSize, 0, -1) * 1024 * 1024;

        if ($f->getSize() > $max_uploadfileSize) {
            Session::flash('message', 'This file is too large to upload.');

            return redirect()->route('workorders.edit', ['id' => $id, '#attachments']);
        }

        $attachment->type = $request->input('type');
        $attachment->description = $request->input('description');
        $attachment->original_name = $f->getClientOriginalName();
        $attachment->file_mime = $f->getMimeType();
        $attachment->file_size = $f->getSize();
        $attachment->user_id = Auth::user()->id;
        $work->attachments()->save($attachment);
        $attachment->save();

        $xfilename = 'attachment-'.$attachment->id.'.'.$f->guessExtension();
        $xpath = 'attachments/workorders/'.$id.'/';
        $f->storeAs($xpath, $xfilename);
        $attachment->file_path = $xpath.$xfilename;
        $attachment->save();

        //dd($f->getMimeType());
        switch ($f->getMimeType()) {
            case 'application/pdf':
                $xblob = file_get_contents($f->getRealPath());
                $img = new Imagick();
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
                $img = new Imagick();
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

        if ($request->has('clientviewable')) {
            $attachment->clientviewable = 'no';
        } else {
            $attachment->clientviewable = 'yes';
        }
        $attachment->save();

        if ($request->has('notify')) {
            if ($request->custom_message) {
                $note = new Note();
                $now = Carbon::now();
                $note->note_text = $request->custom_message;
                $note->entered_at = $now->toDateTimeString();
                $note->entered_by = 1;
                $note->viewable = 1;
                $note->noteable_type = 'App\WorkOrder';
                $note->client_id = $client->id;
                $work = $work->notes()->save($note);
            }
            $data = [
                'note' => 'Have been added to a Notice',
                'entered_at' => $attachment->created_at->format('Y-m-d H:i:s'),
            ];
            //if ($client->notification_setting=='immediate'){
            $notifiable_user = $client->activeusers;
            if ($work) {
                if ($work->responsible_user) {
                    $responsible_user = User::where('id', $work->responsible_user)->get();
                    if (count($responsible_user) > 0) {
                        $notifiable_user = $responsible_user;
                    }
                }
            }
            Notification::send($notifiable_user, new NewAttachment($attachment->id, $data, $request->custom_message, Auth::user()->full_name, 'notice'));

            if ($job->notify_email) {
                $notify_user = TempUser::create(['email' => $job->notify_email]);
                Notification::send($notify_user, new NewAttachment($attachment->id, $data, $request->custom_message, Auth::user()->full_name, 'notice'));
                $notify_user->delete();
            }
        }

        Session::flash('message', 'Attachment added');

        return redirect()->route('workorders.edit', ['id' => $id, '#attachments']);
    }

    public function showattachment($workorder_id, $id)
    {
        $is_attach = Attachment::where('id', $id)->get();
        if (count($is_attach) < 1) {
            Session::flash('message', 'This attachment was deleted.');

            return redirect()->route('workorders.edit', ['id' => $workorder_id, '#attachments']);
        }
        $attachment = Attachment::findOrFail($id);
        $contents = Storage::get($attachment->file_path);
        $response = Response::make($contents, '200', [
            'Content-Type' => $attachment->file_mime,
            'Content-Disposition' => 'attachment; filename="'.$attachment->original_name.'"',
        ]);

        return $response;
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
        $work_id = $attachment->attachable_id;
        if (is_null($attachment->thumb_path)) {
        } else {
            Storage::delete($attachment->thumb_path);
        }
        Storage::delete($attachment->file_path);
        $attachment->delete();

        Session::flash('message', 'Attachment removed');

        return redirect()->route('workorders.edit', ['id' => $work_id, '#attachments']);
    }

    public function setfilter(Request $request)
    {
        if ($request->has('resetfilter')) {
            if ($request->resetfilter == 'true') {
                session()->forget('work_order_filter');
            }
        }

        if ($request->has('client_filter')) {
            if ($request->client_filter == 0) {
                session()->forget('work_order_filter.client');
            } else {
                session(['work_order_filter.client' => $request->client_filter]);
            }
        }

        if ($request->has('job_filter')) {
            if ($request->job_filter == 0) {
                session()->forget('work_order_filter.job');
            } else {
                session(['work_order_filter.job' => $request->job_filter]);
            }
        }

        if ($request->has('work_type')) {
            if ($request->work_type == 'all') {
                session()->forget('work_order_filter.work_type');
            } else {
                session(['work_order_filter.work_type' => $request->work_type]);
            }
        }

        if ($request->has('work_rush')) {
            if ($request->work_rush == 'all') {
                session()->forget('work_order_filter.work_rush');
            } else {
                session(['work_order_filter.work_rush' => $request->work_rush]);
            }
        }

        if ($request->has('work_status')) {
            if ($request->work_status == 'all') {
                session()->forget('work_order_filter.work_status');
            } else {
                session(['work_order_filter.work_status' => $request->work_status]);
            }
        }

        if ($request->has('work_condition')) {
            session(['work_order_filter.work_condition' => $request->work_condition]);
        }

        if ($request->has('daterange')) {
            if ($request->daterange == '') {
                session()->forget('work_order_filter.daterange');
            } else {
                session(['work_order_filter.daterange' => $request->daterange]);
            }
        }

        if ($request->has('job_number')) {
            if ($request->job_number == '') {
                session()->forget('work_order_filter.job_number');
            } else {
                session(['work_order_filter.job_number' => $request->job_number]);
            }
        }
        if ($request->has('job_address')) {
            if ($request->job_address == '') {
                session()->forget('work_order_filter.job_address');
            } else {
                session(['work_order_filter.job_address' => $request->job_address]);
            }
        }
        if ($request->has('job_county')) {
            if ($request->job_county == '') {
                session()->forget('work_order_filter.job_county');
            } else {
                session(['work_order_filter.job_county' => $request->job_county]);
            }
        }

        if ($request->has('customer_name')) {
            if ($request->customer_name == '') {
                session()->forget('work_order_filter.customer_name');
            } else {
                session(['work_order_filter.customer_name' => $request->customer_name]);
            }
        } else {
            session()->forget('work_order_filter.customer_name');
        }

        if ($request->has('work_number')) {
            if ($request->work_number == '') {
                session()->forget('work_order_filter.work_number');
            } else {
                session(['work_order_filter.work_number' => $request->work_number]);
            }
        }

        return redirect()->route('workorders.index');
    }

    public function resetfilter(Request $request)
    {
        session()->forget('work_order_filter');

        return redirect()->route('workorders.index');
    }

    public function setfilter2(Request $request)
    {
        if ($request->has('resetfilter')) {
            if ($request->resetfilter == 'true') {
                session()->forget('work_order_filter2');
            }
        }

        if ($request->has('client_filter')) {
            if ($request->client_filter == 0) {
                session()->forget('work_order_filter2.client');
            } else {
                session(['work_order_filter2.client' => $request->client_filter]);
            }
        }

        if ($request->has('job_filter')) {
            if ($request->job_filter == 0) {
                session()->forget('work_order_filter2.job');
            } else {
                session(['work_order_filter2.job' => $request->job_filter]);
            }
        }

        if ($request->has('work_type')) {
            if ($request->work_type == 'all') {
                session()->forget('work_order_filter2.work_type');
            } else {
                session(['work_order_filter2.work_type' => $request->work_type]);
            }
        }

        if ($request->has('work_rush')) {
            if ($request->work_rush == 'all') {
                session()->forget('work_order_filter2.work_rush');
            } else {
                session(['work_order_filter2.work_rush' => $request->work_rush]);
            }
        }

        if ($request->has('work_status')) {
            if ($request->work_status == 'all') {
                session()->forget('work_order_filter2.work_status');
            } else {
                session(['work_order_filter2.work_status' => $request->work_status]);
            }
        }

        if ($request->has('work_condition')) {
            session(['work_order_filter2.work_condition' => $request->work_condition]);
        }

        if ($request->has('daterange')) {
            if ($request->daterange == '') {
                session()->forget('work_order_filter2.daterange');
            } else {
                session(['work_order_filter2.daterange' => $request->daterange]);
            }
        }

        if ($request->has('job_number')) {
            if ($request->job_number == '') {
                session()->forget('work_order_filter2.job_number');
            } else {
                session(['work_order_filter2.job_number' => $request->job_number]);
            }
        }
        if ($request->has('job_address')) {
            if ($request->job_address == '') {
                session()->forget('work_order_filter2.job_address');
            } else {
                session(['work_order_filter2.job_address' => $request->job_address]);
            }
        }
        if ($request->has('job_county')) {
            if ($request->job_county == '') {
                session()->forget('work_order_filter2.job_county');
            } else {
                session(['work_order_filter2.job_county' => $request->job_county]);
            }
        }

        if ($request->has('customer_name')) {
            if ($request->customer_name == '') {
                session()->forget('work_order_filter2.customer_name');
            } else {
                session(['work_order_filter2.customer_name' => $request->customer_name]);
            }
        } else {
            session()->forget('work_order_filter2.customer_name');
        }

        if ($request->has('work_number')) {
            if ($request->work_number == '') {
                session()->forget('work_order_filter2.work_number');
            } else {
                session(['work_order_filter2.work_number' => $request->work_number]);
            }
        }

        return redirect()->route('workorders.index2');
    }

    public function resetfilter2(Request $request)
    {
        session()->forget('work_order_filter2');

        return redirect()->route('workorders.index2');
    }

    public function newinvoice(Request $request, $work_id)
    {
        $frompage = $request['fromindex'];

        $work = WorkOrder::findOrFail($work_id);
        $client = $work->job->client;
        $data = [
            'work' => $work,
            'client' => $client,
            'frompage' => $frompage,
        ];

        return view('admin.workorders.create_invoice', $data);
    }

    public function createinvoice(Request $request, $work_id)
    {
        $work = WorkOrder::findOrFail($work_id);
        $client = $work->job->client;

        $template = Template::where('type_slug', $work->type)->where('client_id', $client->id)->first();
        $doit = false;
        if ($template) {
            $doit = true;
        } else {
            $template = Template::where('type_slug', $work->type)->where('client_id', 0)->first();
            if ($template) {
                $doit = true;
            }
        }

        $invoice = new Invoice();
        $invoice->client_id = $client->id;
        $invoice->work_order_id = $work->id;
        switch ($client->billing_type) {
            case 'none':
            case 'attime':
                $invoice->due_at = \Carbon\Carbon::now();
                break;
            case 'invoiced':
                $invoice->due_at = new \Carbon\Carbon('next friday');
                break;
        }
        $invoice->status = 'open';
        $invoice->total_amount = 0;
        $invoice->save();
        $total_amount = 0;

        if ($doit) {
            foreach ($template->lines as $tline) {
                if ($tline->type == 'aply-when-rush' && $work->is_rush) {
                    $line = new InvoiceLine();
                    $line->invoice_id = $invoice->id;
                    $line->description = $tline->description;
                    $line->quantity = $tline->quantity;
                    $line->price = $tline->price;
                    $line->amount = $tline->quantity * $tline->price;
                    $line->status = '';
                    $total_amount += $line->amount;
                    $line->save();
                } else {
                }
                if ($tline->type == 'apply-always') {
                    $line = new InvoiceLine();
                    $line->invoice_id = $invoice->id;
                    $line->description = $tline->description;
                    $line->quantity = $tline->quantity;
                    $line->price = $tline->price;
                    $line->status = '';
                    $line->amount = $tline->quantity * $tline->price;
                    $total_amount += $line->amount;
                    $line->save();
                }
            }
            $invoice->total_amount = $total_amount;
            $invoice->save();
        } else {
        }
        if ($request->has('from')) {
            return redirect()->to(route('invoices.edit', $invoice->id).'?from=workorder');
        } else {
            return redirect()->route('invoices.edit', $invoice->id);
        }
    }

    public function checkType($job_id, $type)
    {
        $work_orders = WorkOrder::where('job_id', $job_id)->where('type', $type)->get();
        if (count($work_orders) > 0) {
            return 'YES';
        }

        return 'NO';
    }

    public function todoEdit($work_id, $id)
    {
        $todo = Todo::findOrFail($id);
        $work = WorkOrder::findOrFail($work_id);
        $data = [
            'work' => $work,
            'todo' => $todo,
        ];

        return view('admin.workorders.additionalservice.todo', $data);
    }

    public function todoComplete($work_id, $id)
    {
        $todo = Todo::findOrFail($id);
        $todo->status = 'completed';
        $todo->completed_at = Carbon::now();
        $todo->save();
        $work = $todo->workorder();
        if (count($work->incompleteTodos()) == 0) {
            $work->has_todo = 0;
            $work->save();
        }
        $job = $work->job;
        if (isset($job->client)) {
            $client = $job->client;
            $client_users = $client->activeusers;
            Notification::send($client_users, new TodoItemCompleted($todo->workorder(), Auth::user(), $todo));
        }

        return redirect()->to(url()->previous().'?#todos');
    }

    public function todoUpload($id, Request $request)
    {
        if ($request['file'] == null || $request['file'] == '') {
            Session::flash('message', 'file is required.');

            return redirect()->back();
        }
        $todo = Todo::findOrFail($id);

        $attachment = new TodoDocument();
        $f = $request->file('file');

        $max_uploadfileSize = min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
        $max_uploadfileSize = substr($max_uploadfileSize, 0, -1) * 1024 * 1024;

        if ($f->getSize() > $max_uploadfileSize) {
            Session::flash('message', 'This file is too large to upload.');

            return redirect()->back();
        }

        $attachment->description = $request->input('description');
        $attachment->original_name = $f->getClientOriginalName();
        $attachment->file_mime = $f->getMimeType();
        $attachment->file_size = $f->getSize();
        $attachment->user_id = Auth::user()->id;
        $attachment->todo_id = $todo->id;
        $attachment->save();

        $xfilename = 'attachment-'.$attachment->id.'.'.$f->guessExtension();
        $xpath = 'attachments/workorders/todos/'.$id.'/';
        $f->storeAs($xpath, $xfilename);
        $attachment->file_path = $xpath.$xfilename;
        $attachment->save();

        switch ($f->getMimeType()) {
            case 'application/pdf':
                $xblob = file_get_contents($f->getRealPath());
                $img = new Imagick();
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
                $img = new Imagick();
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

        Session::flash('message', 'To Do Document uploaded');

        return redirect()->back();
    }

    public function destroyTodoDocument($id)
    {
        $attachment = TodoDocument::findOrFail($id);
        if (is_null($attachment->thumb_path)) {
        } else {
            Storage::delete($attachment->thumb_path);
        }
        Storage::delete($attachment->file_path);
        $attachment->delete();
        Session::flash('message', 'ToDo document removed');

        return redirect()->back();
    }

    public function downloadTodoDocument($id)
    {
        if (count(TodoDocument::where('id', $id)->get()) < 1) {
            Session::flash('message', 'File no longer exists.');

            return redirect()->back();
        }
        $attachment = TodoDocument::findOrFail($id);

        return response()->download(Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix().$attachment->file_path);
    }

    public function todoInstruction($id, Request $request)
    {
        if (! $request['instruction']) {
            Session::flash('message', 'file is required.');

            return redirect()->back();
        }
        $todo = Todo::findOrFail($id);

        $instruction = new TodoInstruction();

        $instruction->instruction = $request->input('instruction');
        $instruction->entered_by = Auth::user()->id;
        $instruction->todo_id = $todo->id;
        $instruction->created_at = Carbon::now();
        $instruction->viewable = 1;
        $instruction->save();
        Session::flash('message', 'To Do Instruction was added.');

        return redirect()->back();
    }

    public function destroyTodoInstruction($id)
    {
        $instruction = TodoInstruction::findOrFail($id);
        $instruction->delete();
        Session::flash('message', 'ToDo instruction removed');

        return redirect()->back();
    }

    public function showTodoThumbnail($id)
    {
        $attachment = TodoDocument::findOrFail($id);
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

    public function sendlink($id)
    {
        $work = WorkOrder::where('id', $id)->first();
        if (! $work) {
            Session::flash('message', 'This work order does not exist. It might be deleted.');

            return redirect()->back();
        }
        $client = $work->job->client;
        foreach ($client->activeusers as $user) {
            Notification::send($user, new SendWorkorderLink($user, $work));
        }

        Session::flash('message', 'Email was sent successfully.');

        return redirect()->back();
    }
}
