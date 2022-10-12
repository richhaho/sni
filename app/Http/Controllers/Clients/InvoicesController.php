<?php

namespace App\Http\Controllers\Clients;

use App\Client;
use App\CompanySetting;
use App\Custom\Payeezy;
use App\Http\Controllers\Controller;
use App\Invoice;
use App\InvoiceBatches;
use App\InvoiceLine;
use App\Job;
use App\Mail\PaymentMade;
use App\Payment;
use App\WorkOrder;
use Auth;
use Illuminate\Http\Request;
use Mail;
use Session;

class InvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices = Invoice::query()->orderBy('id', 'desc');
        $invoices->where('client_id', Auth::user()->client_id);

        $jobs = [0 => 'All'];
        if (session()->has('invoice_filter.job')) {
            if (session('invoice_filter.job') != 0) {
                $xjob = session('invoice_filter.job');
                $invoices->whereHas('work_order', function ($q) use ($xjob) {
                    return $q->where('job_id', $xjob);
                });
                $job = Job::where('id', $xjob)->first();
                $jobs = [$xjob => $job ? $job->name : ''];
            }
        }

        if (session()->has('invoice_filter.status')) {
            if (session('invoice_filter.status') != 'all') {
                if (session('invoice_filter.status') == 'unpaid') {
                    $invoices->whereNull('payed_at');
                } else {
                    $invoices->whereNotNull('payed_at');
                }
            }
        } else {
            $invoices->whereNull('payed_at');
            session()->put('invoice_filter.status', 'unpaid');
        }

        if (session()->has('invoice_filter.amount')) {
            $invoices->where('total_amount', session('invoice_filter.amount'));
        }

        $invoices = $invoices->paginate(150);

        $work_available = Invoice::where('client_id', Auth::user()->client_id)->pluck('work_order_id')->unique();
        $jobs_available = WorkOrder::find($work_available)->pluck('job_id')->unique();

        $data = [
            'invoices' => $invoices,
            'statuses' => ['all' => 'All', 'unpaid' => 'Unpaid', 'paid' => 'Paid'],
            'jobs' => $jobs,
        ];

        return view('client.invoices.index', $data);
    }

    public function invoiceforbatch()
    {
        $invoices = Invoice::query()->orderBy('id', 'desc');
        $invoices->where('client_id', Auth::user()->client_id);
        $invoices->whereNull('payed_at')->whereNull('batch_id');
        $invoices = $invoices->paginate(150);

        $work_available = Invoice::where('client_id', Auth::user()->client_id)->pluck('work_order_id')->unique();
        $jobs_available = WorkOrder::find($work_available)->pluck('job_id')->unique();
        $jobs = Job::find($jobs_available)->pluck('name', 'id')->sortBy('name')->prepend('All', 0);

        $data = [
            'invoices' => $invoices,
            'jobs' => $jobs,
        ];

        return view('client.invoices.invoiceforbatch', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
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
            'new_description.*' => 'required',
            'new_quantity.*' => 'required|numeric',
            'new_price.*' => 'required|numeric',
            'description.*' => 'required',
            'quantity.*' => 'required|numeric',
            'price.*' => 'required|numeric',
        ], [
            'new_description.*' => 'The Description is required',
            'new_quantity.*' => 'The quantity must be numeric and required',
            'new_price.*' => 'The price must be numeric and required',
            'description.*' => 'The Description is required',
            'quantity.*' => 'The quantity must be numeric and required',
            'price.*' => 'The price must be numeric and required',
        ]);
        $total = 0;
        if ($request->input('new_description')) {
            foreach ($request->input('new_description') as $key => $description) {
                $total += $request->new_quantity[$key] * $request->new_price[$key];
            }
        }
        if ($total <= 0) {
            if ($request->has('from')) {
                if ($request->from == 'document-generator') {
                    // $data = [
                    //     'work_order_id' => $request->work_order_id,
                    // ];
                    // return view('client.pdf.complete',$data);
                    $work = WorkOrder::findOrFail($request->work_order_id);
                    $job = $work->job;
                    $work->status = 'print';
                    $work->save();

                    return redirect()->route('client.jobs.edit', $job->id);
                }
            }
            Session::flash('message', 'Invoice total amount should not be 0.');

            return redirect()->back();
        }

        $invoice = new Invoice();
        $invoice->client_id = $request->client;
        $invoice->work_order_id = $request->work_order_id;
        $invoice->total_amount = 0;
        $invoice->status = 'open';
        $invoice->save();

        if ($request->input('new_description')) {
            $xpostage = 0;
            $xfee = 0;
            $xother = 0;
            foreach ($request->input('new_description') as $key => $description) {
                $line = new InvoiceLine();
                $line->description = $request->new_description[$key];
                $line->quantity = $request->new_quantity[$key];
                $line->price = $request->new_price[$key];
                $line->amount = $line->quantity * $line->price;
                $line->status = 'open';
                $invoice->lines()->save($line);
                $xpostage = 0;
                $xother = 0;
                $xfee = 0;
            }
        }

        $invoice->updateTotal();

        if ($request->has('from')) {
            if ($request->from == 'document-generator') {
                $company = CompanySetting::first();
                $work = $invoice->work_order;
                $client = $invoice->client;
                $data = [
                    'work' => $work,
                    'client' => $client,
                    'invoice' => $invoice,
                    'api_key' => $company->apikey,
                    'api_secret' => $company->apisecret,
                    'js_security_key' => $company->js_security_key,
                    'ta_token' => $company->ta_token,
                    'payeezy_url' => $company->url,
                ];

                return view('client.pdf.payment', $data);
            }
        }

        return redirect()->route('client.invoices.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $invoice = Invoice::findOrFail($id);
        if ($invoice->client_id != Auth::user()->client->id) {
            abort(403);
        }

        $data = [
            'invoice' => $invoice,

        ];

        return view('client.invoices.view', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }

    public function setfilter(Request $request)
    {
        if ($request->has('job')) {
            if ($request->job == 0) {
                session()->forget('invoice_filter.job');
            } else {
                session(['invoice_filter.job' => $request->job]);
            }
        }
        if ($request->has('status')) {
            //if($request->status == 'all' ) {
            //    session()->forget('invoice_filter.status');
            //} else {
            session(['invoice_filter.status' => $request->status]);
            //}
        }

        if ($request->has('amount')) {
            session(['invoice_filter.amount' => $request->amount]);
        }

        return redirect()->route('client.invoices.index');
    }

    public function resetfilter(Request $request)
    {
        session()->forget('invoice_filter');

        return redirect()->route('client.invoices.index');
    }

    public function payment(Request $request)
    {
        if (! $request->pay) {
            Session::flash('message', 'No Invoices Selected');

            return redirect()->route('client.invoices.index');
        }
        $invoices_id = serialize(array_keys($request->pay));

        $paid_invoices = Invoice::find(array_keys($request->pay))->where('payed_at', '!=', null);
        if (count($paid_invoices) > 0) {
            Session::flash('message', count($paid_invoices).' Invoice(s) has already been paid. Please select again.');

            return redirect()->route('client.invoices.index');
        }

        $invoices = Invoice::find(array_keys($request->pay))->where('payed_at', null);

        $total_charge = $invoices->sum('total_amount');
        $data = [
            'invoices_id' => $invoices_id,
            'invoices' => $invoices,
            'total_charge' => $total_charge,
            'todownload' => false,
            'work_id' => 0,
            'attach_id' => 0,

        ];

        return view('client.invoices.payment', $data);
    }

    public function tobatch(Request $request)
    {
        if (! $request->pay) {
            Session::flash('message', 'No Invoices Selected');

            return redirect()->route('client.invoices.invoiceforbatch');
        }
        $invoices_id = serialize(array_keys($request->pay));

        $paid_invoices = Invoice::find(array_keys($request->pay))->where('payed_at', '!=', null);

        if (count($paid_invoices) > 0) {
            Session::flash('message', count($paid_invoices).' Invoice(s) has already been paid. Please select again.');

            return redirect()->route('client.invoices.invoiceforbatch');
        }
        $batched_invoices = Invoice::find(array_keys($request->pay))->where('batch_id', '!=', null);
        if (count($batched_invoices) > 0) {
            Session::flash('message', 'One or more of Invoice(s) has already been batched. Please select again.');

            return redirect()->route('client.invoices.invoiceforbatch');
        }

        //$invoices = Invoice::find(array_keys($request->pay))->where('payed_at',null);
        $invoices = Invoice::whereIn('id', array_keys($request->pay))->where('payed_at', null)->orderBy('work_order_id')->get();

        $batch_data = [
            'client_id' => Auth::user()->client_id,
            'invoice_id' => $invoices_id,
            'payed_at' => null,
            'payment_id' => null,
            'created_at' => \Carbon\Carbon::now(),
        ];
        $batch = InvoiceBatches::create($batch_data);
        $batch->save();

        foreach ($invoices as $invoice) {
            $invoice->batch_id = $batch->id;
            $invoice->save();
        }

        $total_charge = $invoices->sum('total_amount');
        $batch->total_amount = number_format($total_charge, 2);

        $batch->save();
        $data = [
            'invoices_id' => $invoices_id,
            'invoices' => $invoices,
            'total_charge' => $total_charge,
            'todownload' => false,
            'work_id' => 0,
            'attach_id' => 0,
            'batch_date' => date('Y-m-d', strtotime($batch->created_at)),
            'batch_id' => $batch->id,
            'client_company' => Auth::user()->client->company_name,

        ];

        return view('client.invoices.batchinvoices', $data);
    }

    public function paytodownload($work_id, $attach_id)
    {
        $workorder = WorkOrder::findOrFail($work_id);
        $job = $workorder->job;
        $this->authorize('wizard', $job);

        $xids = $workorder->invoices()->where(function ($q) {
            $q->where('status', 'open')->Orwhere('status', 'unpaid');
        })->pluck('id')->toArray();
        $invoices_id = serialize($xids);
        $invoices = Invoice::find($xids);
        $total_charge = $invoices->sum('total_amount');
        $data = [
            'invoices_id' => $invoices_id,
            'invoices' => $invoices,
            'total_charge' => $total_charge,
            'todownload' => true,
            'work_id' => $work_id,
            'attach_id' => $attach_id,
        ];

        foreach ($invoices as $invoice) {
            if ($invoice->batch_id) {
                Session::flash('message', 'This invoice is part of batch B'.$invoice->batch_id.'.  Please pay the batch invoice first.');

                return redirect()->route('client.notices.edit', ['id' => $work_id, '#attachments']);
            }
        }

        return view('client.invoices.payment', $data);
    }

    public function submitpayment(Request $request)
    {
        $invoices_id = unserialize($request->invoices_id);
        $invoices = Invoice::whereIn('id', $invoices_id)->where(function ($q) {
            $q->where('status', 'open')->Orwhere('status', 'unpaid');
        })->get();

        if (count($invoices) == 0) {
            return redirect()->route('client.invoices.index');
        }
        $total_charge = $invoices->sum('total_amount');
        $company = CompanySetting::first();

        //dd($data);
        $client = Auth::user()->client;
        if (! $client->payeezy_value) {
            return view('client.invoices.setupcard');
        }

        $py = new Payeezy();
        $py->setApiKey($company->apikey);
        $py->setApiSecret($company->apisecret);
        $py->setMerchantToken($company->merchant_token);
        $py->setUrl('https://'.$company->url.'/v1/transactions');

        if ($client->company_name == '' || $client->company_name == null) {
            $client_name = $client->first_name.' '.$client->last_name;
        } else {
            $client_name = $client->company_name;
        }

        $payload = [
            //'merchant_ref' => 'Payment for Invoice(s): ' .  implode(', ', $invoices_id),
            'merchant_ref' => $client_name,
            'transaction_type' => 'purchase',
            'method' => 'token',
            'amount' => number_format($total_charge, 2, '', ''),
            'currency_code' => 'USD',
            'token' => [
                'token_type' => 'FDToken',
                'token_data' => [
                    'type' => $client->payeezy_type,
                    'value' => $client->payeezy_value,
                    'cardholder_name' => $client->payeezy_cardholder_name,
                    'exp_date' => $client->payeezy_exp_date,
                ],
            ],
        ];

        $result = $py->purchase($payload);
        $result_data = json_decode($result);

        //save into payments
        $payment = new Payment();
        $payment->invoices_id = serialize($invoices_id);
        $payment->type = 'credit_card';
        $payment->amount = $total_charge;
        $payment->client_id = $client->id;
        $payment->reference = $result_data->correlation_id;
        $payment->gateway = 'payeezy';
        $payment->transaction_status = $result_data->transaction_status;
        $payment->log_result = $result;
        $payment->user_id = Auth::user()->id;
        $payment->save();
        // change invoice status
        if ($result_data->transaction_status == 'approved') {
            foreach ($invoices as $invoice) {
                $invoice->status = 'paid';
                $invoice->payment_id = $payment->id;
                $invoice->payed_at = \Carbon\Carbon::now();
                $invoice->save();
                if ($invoice->type == 'additional-service') {
                    foreach ($invoice->todos() as $todo) {
                        $todo->status = 'paid';
                        $todo->save();
                    }
                    $work = $invoice->work_order;
                    if (count($work->incompleteTodos())) {
                        $work->has_todo = 1;
                        $work->save();
                    }
                }

                $wo = $invoice->work_order;

                if ($client->billing_type == 'invoiced') {
                } else {
                    if ($wo->status == 'payment pending') {
                        $wo->status = 'open';
                        $wo->save();
                    }
                }
            }

            //$users = Auth::user()->client->users;
            $users = Auth::user()->client->activeusers;
            foreach ($users as $user) {
                $mailto[] = $user->email;
            }
            $client = Auth::user()->client;
            if (json_encode(unserialize($client->override_payment)) != 'false' && json_encode(unserialize($client->override_payment)) != 'null') {
                Mail::to(unserialize($client->override_payment))->send(new PaymentMade($total_charge, $invoices, $client, $payment->created_at));
            } else {
                if (count($mailto) > 0) {
                    Mail::to($mailto)->send(new PaymentMade($total_charge, $invoices, $client, $payment->created_at));
                }
            }
            //$client=Auth::user()->client;
            //if ($client->notification_setting=='immediate'){
            //Mail::to($mailto)->send(new PaymentMade($total_charge, $invoices));
            //}

            $data = [
                'invoices_id' => $invoices_id,
                'invoices' => $invoices,
                'total_charge' => $total_charge,
                'todownload' => $request->todownload,
                'work_id' => $request->work_id,
                'attach_id' => $request->attach_id,

            ];

            return view('client.invoices.paid', $data);
        } else {
            foreach ($invoices as $invoice) {
                $invoice->status = 'unpaid';
                $invoice->save();
            }

            return view('client.invoices.unpaid');
        }
    }
}
