<?php

namespace App\Http\Controllers\Admin;

use App\Client;
use App\CompanySetting;
use App\Custom\Payeezy;
use App\Http\Controllers\Controller;
use App\Invoice;
use App\InvoiceBatches;
use App\Mail\PaymentMade;
use App\Payment;
use Auth;
use Illuminate\Http\Request;
use Mail;
use Session;

class InvoicesbatchesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoicesbatches = InvoiceBatches::query()->orderBy('id', 'desc');
        //$invoicesbatches->where('client_id',Auth::user()->client_id)->where('deleted_at',null);
        if (session()->has('invoicesbatches_filter.status')) {
            if (session('invoicesbatches_filter.status') != 'all') {
                if (session('invoicesbatches_filter.status') == 'unpaid') {
                    $invoicesbatches->whereNull('payed_at');
                } else {
                    $invoicesbatches->whereNotNull('payed_at');
                }
            }
        } else {
            $invoicesbatches->whereNull('payed_at');
            session()->put('invoicesbatches_filter.status', 'unpaid');
        }
        foreach ($invoicesbatches->get() as $batch) {
            $invoices_batch = unserialize($batch->invoice_id);
            $invoices_id = $batch->invoice_id;
            $invoices = Invoice::find($invoices_batch);
            $total_charge = $invoices->sum('total_amount');
            $batch->total_amount = number_format($total_charge, 2);
            $batch->save();
        }

        $invoicesbatches = $invoicesbatches->paginate(20);

        $data = [
            'invoicesbatches' => $invoicesbatches,
            'statuses' => ['all' => 'All', 'unpaid' => 'Unpaid', 'paid' => 'Paid'],

        ];

        return view('admin.invoicesbatches.index', $data);
    }

    public function delete($batch_id)
    {
        $invoicesbatches = InvoiceBatches::where('id', $batch_id)->where('deleted_at', null)->first();

        if (count($invoicesbatches) < 1) {
            Session::flash('message', 'This Invoice Batch has already been deleted.');

            return redirect()->route('invoicesbatches.index');
        }

        $invoices_id = unserialize($invoicesbatches->invoice_id);
        $invoices = Invoice::find($invoices_id)->where('payed_at', null);
        foreach ($invoices as $invoice) {
            $invoice->batch_id = null;
            $invoice->save();
        }
        $invoicesbatches->delete();

        Session::flash('message', 'A Invoice Batch has been deleted.');

        return redirect()->route('invoicesbatches.index');
    }

    public function printview($batch_id)
    {
        $batch = InvoiceBatches::where('id', $batch_id)->where('deleted_at', null)->first();

        if (count($batch) < 1) {
            Session::flash('message', 'This Invoice Batch has already been deleted.');

            return redirect()->route('invoicesbatches.index');
        }
        $invoices_batch = unserialize($batch->invoice_id);

        $invoices_id = $batch->invoice_id;

        $invoices = Invoice::whereIn('id', $invoices_batch)->orderBy('work_order_id')->get();

        $total_charge = $invoices->sum('total_amount');
        $batch->total_amount = $total_charge;

        $data = [
            'invoices_id' => $invoices_id,
            'invoices' => $invoices,
            'total_charge' => $total_charge,
            'todownload' => false,
            'work_id' => 0,
            'attach_id' => 0,
            'batch_date' => date('Y-m-d', strtotime($batch->created_at)),
            'batch_id' => $batch->id,
            'client_company' => $batch->client()->company_name,

        ];

        return view('admin.invoicesbatches.printview', $data);
    }

    public function payment($batch_id)
    {
        $invoicesbatches = InvoiceBatches::where('id', $batch_id)->first();
        $invoices_id = unserialize($invoicesbatches->invoice_id);

        $paid_invoices = Invoice::find($invoices_id)->where('payed_at', '!=', null);
        if (count($paid_invoices) > 0) {
            Session::flash('message', count($paid_invoices).' Invoice(s) has already been paid. Please select again.');

            return redirect()->route('invoicesbatches.index');
        }

        //$invoices = Invoice::find($invoices_id)->where('payed_at',null);
        $invoices = Invoice::whereIn('id', $invoices_id)->where('payed_at', null)->orderBy('work_order_id')->get();

        $total_charge = $invoices->sum('total_amount');
        $data = [
            'invoices_id' => $invoicesbatches->invoice_id,
            'invoices' => $invoices,
            'total_charge' => $total_charge,
            'todownload' => false,
            'work_id' => 0,
            'attach_id' => 0,
            'batch_id' => $batch_id,

        ];

        return view('admin.invoicesbatches.payment', $data);
    }

    public function submitcheck(Request $request)
    {
        $batch_id = $request->batch_id;

        $invoicesbatches = InvoiceBatches::where('id', $batch_id)->first();

        $invoices_id = unserialize($invoicesbatches->invoice_id);
        if (count($invoicesbatches) < 1) {
            Session::flash('message', 'Invoice Batch has already been deleted.');

            return redirect()->route('invoicesbatches.index');
        }

        $invoices = Invoice::whereIn('id', $invoices_id)->where(function ($q) {
            $q->where('status', 'open')->Orwhere('status', 'unpaid');
        })->get();

        if (count($invoices) == 0) {
            Session::flash('message', 'Invoice Batch has already been paid.');

            return redirect()->route('invoicesbatches.index');
        }
        $total_charge = $invoices->sum('total_amount');
        $company = CompanySetting::first();
        $client = Client::findOrfail($invoicesbatches->client_id);

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
        $invoicesbatches->payment_id = $payment->id;
        $invoicesbatches->payed_at = \Carbon\Carbon::now();
        $invoicesbatches->save();

        $mailto = [];
        foreach ($invoices as $invoice) {
            $invoice->status = 'paid';
            $invoice->payed_at = \Carbon\Carbon::now();
            $invoice->payment_id = $payment->id;
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
        }

        //$users = $client->users;
        $users = $client->activeusers;
        foreach ($users as $user) {
            $mailto[] = $user->email;
        }
        if (json_encode(unserialize($client->override_payment)) != 'false' && json_encode(unserialize($client->override_payment)) != 'null') {
            Mail::to(unserialize($client->override_payment))->send(new PaymentMade($total_charge, $invoices, $client, $payment->created_at));
        } else {
            if (count($mailto) > 0) {
                Mail::to($mailto)->send(new PaymentMade($total_charge, $invoices, $client, $payment->created_at));
            }
        }
        Session::flash('message', ' Invoice Batch has been paid by check. ');

        return redirect()->route('invoicesbatches.index');
    }

    public function submitpayment(Request $request)
    {
        $batch_id = $request->batch_id;
        $invoicesbatches = InvoiceBatches::where('id', $batch_id)->first();
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
        $client = Client::findOrfail($invoicesbatches->client_id);
        if ($client->company_name == '' || $client->company_name == null) {
            $client_name = $client->first_name.' '.$client->last_name;
        } else {
            $client_name = $client->company_name;
        }
        if (! $client->payeezy_value) {
            Session::flash('message', " Client didn't set payment method.");

            return redirect()->route('invoicesbatches.index');
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

        if ($result_data->transaction_status == 'approved') {
            // change invoice status

            $invoicesbatches->payment_id = $payment->id;
            $invoicesbatches->payed_at = \Carbon\Carbon::now();
            $invoicesbatches->save();
            foreach ($invoices as $invoice) {
                $invoice->status = 'paid';
                $invoice->payment_id = $payment->id;
                $invoice->payed_at = \Carbon\Carbon::now();
                $invoice->save();
            }

            $users = $client->activeusers;
            $mailto = [];
            foreach ($users as $user) {
                $mailto[] = $user->email;
            }
            if (json_encode(unserialize($client->override_payment)) != 'false' && json_encode(unserialize($client->override_payment)) != 'null') {
                Mail::to(unserialize($client->override_payment))->send(new PaymentMade($total_charge, $invoices, $client, $payment->created_at));
            } else {
                if (count($mailto) > 0) {
                    Mail::to($mailto)->send(new PaymentMade($total_charge, $invoices, $client, $payment->created_at));
                }
            }
            $data = [
                'invoices_id' => $invoices_id,
                'invoices' => $invoices,
                'total_charge' => $total_charge,
                'todownload' => $request->todownload,
                'work_id' => $request->work_id,
                'attach_id' => $request->attach_id,

            ];

            return view('admin.invoices.paid', $data);
        } else {
            foreach ($invoices as $invoice) {
                $invoice->status = 'unpaid';
                $invoice->save();
            }

            return view('admin.invoicesbatches.unpaid');
        }
    }

    public function setfilter(Request $request)
    {
        if ($request->has('status')) {
            session(['invoicesbatches_filter.status' => $request->status]);
        }

        return redirect()->route('invoicesbatches.index');
    }

    public function resetfilter(Request $request)
    {
        session()->forget('invoicesbatches_filter');

        return redirect()->route('invoicesbatches.index');
    }
}
