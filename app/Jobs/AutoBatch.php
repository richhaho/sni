<?php

namespace App\Jobs;

use App\Client;
use App\InvoiceBatches;
use App\Mail\AutoBatchInvoices;
use App\Mail\AutoBatchToAdmin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use PDF;
use Storage;

class AutoBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::raw('AutoBatch Invoice email job started.', function ($message) {
            $message->subject('AutoBatch Start');
            $message->from('no-reply@sunshinenotice.com', 'SunshineNotice');
            $message->to('jwatson@ironrocksoftware.com');
        });
        $batches = [];
        $clients = Client::where('deleted_at', null)->get();
        foreach ($clients as $client) {
            if ($client->autobatch != 1) {
                continue;
            }
            $invoices = $client->invoices()->whereIn('status', ['open', 'unpaid'])->where('batch_id', null)->orderBy('created_at')->get();
            if (! isset($invoices)) {
                continue;
            }
            if (count($invoices) == 0) {
                continue;
            }

            $client_name = $client->full_name;
            $client_companyNameCity = $client->company_name.' , '.$client->city;

            $invoices_id = serialize($invoices->pluck('id')->toArray());

            $batch_data = [
                'client_id' => $client->id,
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
            $batch->type = 'autobatch';
            $batch->save();

            $batches[] = $batch;
            //========= PDF Generate ============
            $start_period = date('m/d/y', strtotime($invoices[0]->created_at));
            $period = $start_period.' - '.date('m/d/y', strtotime($batch->created_at));
            $period_total = $total_charge;
            $past_total = 0;
            $past_postage = 0;
            foreach ($client->batch_invoices()->where('payed_at', null)->where('id', '!=', $batch->id)->get() as $un_batch) {
                $past_total += (float) filter_var($un_batch->total_amount, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                foreach ($un_batch->invoices()->where('payed_at', null) as $inv) {
                    $postage = $inv->total_amount - $inv->lines()->where('description', 'not like', '%MAIL%')->where('description', 'not like', '%POSTAGE%')->where('description', 'not like', '%CERTIFIED%')->where('description', 'not like', '%PREVIOUS%')->sum('amount');
                    $past_postage += $postage;
                }
            }
            $first_batch_number = count($client->batch_invoices()->where('payed_at', null)->where('id', '!=', $batch->id)->get()) > 0 ? '#B'.$client->batch_invoices()->where('payed_at', null)->where('id', '!=', $batch->id)->orderBy('id')->first()->id : '';

            $data['document'] = 'batchinvoice';
            $data['period'] = $period;
            $data['start_period'] = $start_period;
            $data['period_total'] = $period_total;
            $data['past_total'] = $past_total;
            $data['past_postage'] = $past_postage;
            $data['client'] = $client;
            $data['invoices'] = $invoices;
            $data['batch'] = $batch;
            $data['first_batch_number'] = $first_batch_number;

            $pdf = PDF::loadView('admin.pdf.pdf-document-landscape', $data)->setPaper('Letter');
            $autobatch_pdf = $pdf->output();
            $file = 'batchinvoices/'.$batch->id.'.pdf';
            Storage::put($file, $autobatch_pdf);

            $autobatch_pdf = 'app/'.$file;
            //========= Sending Email ===========

            if (isset($client->users)) {
                $users = $client->users->where('deleted_at', null);
            }
            $useremails = [];
            if (count($users) > 0) {
                $useremails = $users->pluck('email')->toArray();
            } else {
                if ($client->email) {
                    $useremails[] = $client->email;
                }
            }

            $useremails[] = 'Suzanne@sunshinenotices.com';

            for ($i = 0; $i < count($useremails); $i++) {
                if (! email_validate($useremails[$i])) {
                    unset($useremails[$i]);
                }
            }

            if (json_encode(unserialize($client->override_weekly)) != 'false' && json_encode(unserialize($client->override_weekly)) != 'null') {
                Mail::to(unserialize($client->override_weekly))
                    ->cc(['Suzanne@sunshinenotices.com'])
                    ->send(new AutoBatchInvoices($autobatch_pdf, $batch, $client_name, $client_companyNameCity));
            } else {
                if (count($useremails) > 1) {
                    Mail::to($useremails)->send(new AutoBatchInvoices($autobatch_pdf, $batch, $client_name, $client_companyNameCity));
                }
            }
        }

        $adminusers[] = 'suzanne@sunshinenotices.com';
        $period = date('m/d/y', strtotime('-6 days')).' - '.date('m/d/y', strtotime(\Carbon\Carbon::now()));
        if (count($batches) > 0) {
            Mail::to($adminusers)->send(new AutoBatchToAdmin($batches, $period));
        }

        Mail::raw('AutoBatch Invoice email job completed.', function ($message) {
            $message->subject('AutoBatch End');
            $message->from('no-reply@sunshinenotice.com', 'SunshineNotice');
            $message->to('jwatson@ironrocksoftware.com');
        });
    }
}
