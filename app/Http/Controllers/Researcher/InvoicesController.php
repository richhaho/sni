<?php

namespace App\Http\Controllers\Researcher;

use App\Client;
use App\CompanySetting;
use App\Custom\Payeezy;
use App\Http\Controllers\Controller;
use App\Invoice;
use App\InvoiceLine;
use App\Job;
use App\Mail\PaymentMade;
use App\MailingType;
use App\Payment;
use App\WorkOrder;
use App\WorkOrderRecipient;
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

        if (session()->has('invoice_filter.client')) {
            if (session('invoice_filter.client') != 0) {
                $invoices->where('client_id', session('invoice_filter.client'));
            }
        }

        if (session()->has('invoice_filter.job')) {
            if (session('invoice_filter.job') != 0) {
                $xjob = session('invoice_filter.job');
                $invoices->whereHas('work_order', function ($q) use ($xjob) {
                    return $q->where('job_id', $xjob);
                });
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

        $invoices = $invoices->paginate(15);

        $clients_available = Invoice::pluck('client_id')->unique();
        $clients = Client::withTrashed()->find($clients_available)->sortBy('company_name')->pluck('company_name', 'id')->prepend('All', 0);

        $work_available = Invoice::pluck('work_order_id')->unique();
        $jobs_available = WorkOrder::withTrashed()->find($work_available)->pluck('job_id')->unique();
        $jobs = Job::withTrashed()->find($jobs_available)->sortBy('name')->pluck('name', 'id')->prepend('All', 0);

        $data = [
            'invoices' => $invoices,
            'clients' => $clients,
            'jobs' => $jobs,
            'statuses' => ['all' => 'All', 'unpaid' => 'Unpaid', 'paid' => 'Paid'],
        ];

        return view('researcher.invoices.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $clients = Client::all()->pluck('company_name', 'id')->toArray();
        $work_orders = Client::first()->work_orders->pluck('number', 'id')->toArray();
        //dd($work_orders);

        $data = [
            'clients' => $clients,
            'work_orders' => $work_orders,

        ];

        return view('researcher.invoices.create', $data);
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
        ], [
            'new_description.*' => 'The Description is required',
            'new_quantity.*' => 'The quantity must be numeric',
            'new_price.*' => 'The price must be numeric',
        ]);

        //dd('valido');
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

            //$current_line_count= count($request->line_recipient_type);
            foreach ($request->input('new_description') as $key => $description) {
                $line = new InvoiceLine();
                $line->description = $request->new_description[$key];
                $line->quantity = $request->new_quantity[$key];
                $line->price = $request->new_price[$key];
                $line->amount = $line->quantity * $line->price;
                $line->status = 'open';
                $invoice->lines()->save($line);
                //dd($request->line_recipient_type[$key]);
                $xpostage = 0;
                $xother = 0;
                $xfee = 0;
                // if ($key<$current_line_count){
                //   $recipients =  WorkOrderRecipient::where('work_order_id',$request->work_order_id)->where('mailing_type',$request->line_recipient_type[$key])->get();
                //   $mtype = MailingType::where('type',$request->line_recipient_type[$key])->first();

                //   if (count($mtype)==0){
                //     $xpostage = 0;
                //     $xother = 0;
                //     $xfee = 0;
                //   }else{
                //     $xpostage=$mtype->postage;
                //     $xfee=$mtype->fee;
                //   }

                // }
                // if (count($recipients) > 0 ) {
                //     $other = true;
                //     foreach($recipients as $recipient) {
                //         $recipient->postage = $xpostage;
                //         $recipient->fee = $xfee;
                //         $recipient->other = $xother;
                //         $recipient->save();
                //     }

                    // if (strpos(strtoupper($line->description),  strtoupper('POSTAGE'))) {
                    //     $other = false;
                    //     $xpostage +=  $line->amount / count($recipients);
                    // }
                    // if (strpos(strtoupper($line->description),  strtoupper('FEE'))) {
                    //     $other = false;
                    //     $xfee += $line->amount / count($recipients);
                    // }
                    // if ($other) {
                    //     $xother += $line->amount / count($recipients);
                    // }
                    // foreach($recipients as $recipient) {
                    //     if($xpostage == 0 ) {
                    //         $xpostage=$mtype->postage;
                    //     }
                    //     if($xfee == 0 ) {
                    //         $xfee=$mtype->fee;
                    //     }
                    //     $recipient->postage = $xpostage;
                    //     $recipient->fee = $xfee;
                    //     $recipient->other = $xother;
                    //     $recipient->save();
                    // }
                //}
            }
        }

        $invoice->updateTotal();

        if ($invoice->total_amount <= 0) {
            $invoicetodelete = Invoice::findOrFail($invoice->id);
            $invoicetodelete->delete();
        }
        if ($request->has('from')) {
            if ($request->from == 'document-generator') {
                $data = [
                    'work_order_id' => $request->work_order_id,
                ];

                return view('researcher.pdf.complete', $data);
            }

            if ($request->from == 'document-generator-resend') {
                return redirect()->route('mailinghistory.index');
            }
        }

        return redirect()->route('invoices.index');
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

    public function paymentbycheck(Request $request)
    {
        $this->validate($request, [
            'pay' => 'required',
        ]);
        $invoices_id = serialize(array_keys($request->pay));

        $invoices = Invoice::find(array_keys($request->pay));

        $total_charge = $invoices->sum('total_amount');
        $data = [
            'invoices_id' => $invoices_id,
            'invoices' => $invoices,
            'total_charge' => $total_charge,
            'todownload' => false,
            'work_id' => 0,
            'attach_id' => 0,
            'client_id' => $request->client_id,
        ];

        return view('researcher.invoices.paymentbycheck', $data);
    }

    public function submitcheck(Request $request)
    {
        $invoices_id = unserialize($request->invoices_id);
        $invoices = Invoice::whereIn('id', $invoices_id)->where(function ($q) {
            $q->where('status', 'open')->Orwhere('status', 'unpaid');
        })->get();

        if (count($invoices) == 0) {
            return redirect()->route('invoices.index');
        }
        $total_charge = $invoices->sum('total_amount');
        $company = CompanySetting::first();
        $client = Client::findOrfail($request->client_id);

        //dd($data);

        //save into payments
        $payment = new Payment();
        $payment->invoices_id = serialize($invoices_id);
        $payment->type = 'pay_by_check';
        $payment->amount = $total_charge;
        $payment->client_id = $client->id;
        $payment->reference = $request->check_number;
        $payment->gateway = 'none';
        $payment->transaction_status = 'approved';
        $payment->log_result = '';
        $payment->user_id = Auth::user()->id;
        $payment->save();
        // change invoice status
        $mailto = [];
        foreach ($invoices as $invoice) {
            $invoice->status = 'paid';
            $invoice->payed_at = \Carbon\Carbon::now();
            $invoice->save();
        }

        $users = $client->users;
        foreach ($users as $user) {
            $mailto[] = $user->email;
        }
        if (count($mailto) > 0) {
            Mail::to($mailto)->send(new PaymentMade($total_charge, $invoices));
        }

        return redirect()->route('invoices.index');
    }

    public function payment(Request $request)
    {
        $this->validate($request, [
            'pay' => 'required',
        ]);
        $invoices_id = serialize(array_keys($request->pay));

        $invoices = Invoice::find(array_keys($request->pay));

        $total_charge = $invoices->sum('total_amount');
        $data = [
            'invoices_id' => $invoices_id,
            'invoices' => $invoices,
            'total_charge' => $total_charge,
            'todownload' => false,
            'work_id' => 0,
            'attach_id' => 0,
            'client_id' => $request->client_id,
        ];

        return view('researcher.invoices.payment', $data);
    }

    public function submitpayment(Request $request)
    {
        $invoices_id = unserialize($request->invoices_id);
        $invoices = Invoice::whereIn('id', $invoices_id)->where(function ($q) {
            $q->where('status', 'open')->Orwhere('status', 'unpaid');
        })->get();

        if (count($invoices) == 0) {
            return redirect()->route('invoices.index');
        }
        $total_charge = $invoices->sum('total_amount');
        $company = CompanySetting::first();

        //dd($data);

        $py = new Payeezy();
        $py->setApiKey($company->apikey);
        $py->setApiSecret($company->apisecret);
        $py->setMerchantToken($company->merchant_token);
        $py->setUrl('https://'.$company->url.'/v1/transactions');
        $client = Client::findOrfail($request->client_id);
        $payload = [
            'merchant_ref' => 'Payment for Invoice(s): '.implode(', ', $invoices_id),
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
                $invoice->payed_at = \Carbon\Carbon::now();
                $invoice->save();
            }

            $users = $client->users;
            $mailto = [];
            foreach ($users as $user) {
                $mailto[] = $user->email;
            }
            if (count($mailto) > 0) {
                Mail::to($mailto)->send(new PaymentMade($total_charge, $invoices));
            }
            $data = [
                'invoices_id' => $invoices_id,
                'invoices' => $invoices,
                'total_charge' => $total_charge,
                'todownload' => $request->todownload,
                'work_id' => $request->work_id,
                'attach_id' => $request->attach_id,

            ];

            return view('researcher.invoices.paid', $data);
        } else {
            foreach ($invoices as $invoice) {
                $invoice->status = 'unpaid';
                $invoice->save();
            }

            return view('researcher.invoices.unpaid');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        if ($request->has('from')) {
            $from = $request->input('from');
        } else {
            $from = '';
        }

        $data = [
            'invoice' => $invoice,
            'from' => $from,
        ];

        return view('researcher.invoices.edit', $data);
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
            'new_description.*' => 'required',
            'new_quantity.*' => 'required|numeric',
            'new_price.*' => 'required|numeric',
        ], [
            'new_description.*' => 'The Description is required',
            'new_quantity.*' => 'The quantity must be numeric',
            'new_price.*' => 'The price must be numeric',
        ]);

        //dd('valido');
        $invoice = Invoice::findOrFail($id);

        if ($request->input('description')) {
            foreach ($request->input('description') as $key => $description) {
                $line = InvoiceLine::findOrFail($key);

                $line->description = $request->description[$key];
                $line->quantity = $request->quantity[$key];
                $line->price = $request->price[$key];
                $line->amount = $line->quantity * $line->price;
                $line->status = 'open';
                $line->save();
            }
        }

        if ($request->input('new_description')) {
            foreach ($request->input('new_description') as $key => $description) {
                $line = new InvoiceLine();
                $line->description = $request->new_description[$key];
                $line->quantity = $request->new_quantity[$key];
                $line->price = $request->new_price[$key];
                $line->amount = $line->quantity * $line->price;
                $line->status = 'open';
                $invoice->lines()->save($line);
            }
        }

        $invoice->updateTotal();

        if ($request->from == '') {
            return redirect()->route('invoices.index');
        } else {
            return redirect()->to(route('workorders.edit', $invoice->work_order_id).'#invoices');
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
        $invoice = Invoice::findOrFail($id);
        $old_name = $invoice->number;
        $invoice->delete();

        Session::flash('message', 'Invoice '.$old_name.' deleted');

        return redirect()->route('invoices.index');
    }

    public function setfilter(Request $request)
    {
        if ($request->has('client_filter')) {
            if ($request->client_filter == 0) {
                session()->forget('invoice_filter.client');
            } else {
                session(['invoice_filter.client' => $request->client_filter]);
            }
        }

        if ($request->has('job')) {
            if ($request->job == 0) {
                session()->forget('invoice_filter.job');
            } else {
                session(['invoice_filter.job' => $request->job]);
            }
        }

        if ($request->has('status')) {
            //if($request->status == 'all' ) {
            //session()->forget('invoice_filter.status');
            //} else {
            session(['invoice_filter.status' => $request->status]);
            //}
        }

        if ($request->has('amount')) {
            session(['invoice_filter.amount' => $request->amount]);
        }

        return redirect()->route('invoices.index');
    }

    public function resetfilter(Request $request)
    {
        session()->forget('invoice_filter');

        return redirect()->route('invoices.index');
    }
}
