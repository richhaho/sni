<?php

namespace App\Http\Controllers\Admin;

use App\Attachment;
use App\BatchDetail;
use App\Client;
use App\CompanySetting;
use App\FtpLocation;
use App\Http\Controllers\Controller;
use App\Jobs\FTPUpload;
use App\Mail\NoticeComplete;
use App\MailingBatch;
use App\MailingType;
use App\User;
use App\WorkOrder;
use App\WorkOrderType;
use Auth;
use iio\libmergepdf\Merger;
use Illuminate\Http\Request;
use League\Flysystem\Adapter\Ftp as FtpAdapter;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Filesystem;
// The two filesystem adapters we will use
use League\Flysystem\MountManager;
use Mail;
use PDF;
// MountManager for quick and easy copying
use Response;
use Session;
use Storage;

class MailingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $batches = MailingBatch::orderBy('created_at', 'DESC')->paginate(15);

        $data = [
            'batches' => $batches,

        ];

        return view('admin.mailing.index', $data);
    }

    public function completeworkorder($batch_id)
    {
        $batch = MailingBatch::where('id', $batch_id)->first();
        if (count($batch) < 1) {
            Session::flash('message', 'This batch has already been deleted.');

            return redirect()->route('mailing.index');
        }

        //return json_encode($batch->details[0]->recipient->work_order);
        $incompleted_work = '';
        foreach ($batch->details as $detail) {
            if (! isset($detail->recipient->work_order)) {
                continue;
            }
            $work = $detail->recipient->work_order;

            if ($work->status != 'completed') {
                $work->status = 'completed';
                $work->save();
                $incompleted_work = $incompleted_work.'  #'.$work->number;

                $client = $work->job->client;
                if ($client->notification_setting == 'immediate') {
                    if (json_encode(unserialize($client->override_notice)) != 'false' && json_encode(unserialize($client->override_notice)) != 'null') {
                        Mail::to(unserialize($client->override_notice))->send(new NoticeComplete($work->id, $work->invoicesPending));
                    } else {
                        $mailto = [];
                        $responsible_user = User::where('id', $work->responsible_user)->first();
                        if ($work->responsible_user && count($responsible_user) > 0) {
                            $mailto[] = $responsible_user->email;
                        } else {
                            $users = $work->job->client->activeusers;
                            foreach ($users as $user) {
                                $mailto[] = $user->email;
                            }
                        }
                        if (count($mailto) > 0) {
                            Mail::to($mailto)->send(new NoticeComplete($work->id, $work->invoicesPending));
                        }
                    }
                    if ($work->job->notify_email) {
                        Mail::to($work->job->notify_email)->send(new NoticeComplete($work->id, $work->invoicesPending));
                    }
                }
            }
        }
        if ($incompleted_work == '') {
            Session::flash('message', 'All Work Orders of this batch has already been completed.');

            return redirect()->route('mailing.index');
        }

        Session::flash('message', 'Work Orders('.$incompleted_work.') of this batch has been completed.');

        return redirect()->route('mailing.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $available_documents = Attachment::query()->where('type', 'generated')->whereNull('printed_at')->with('recipient');

        $available_documents->whereHas('recipient', function ($q) {
            $q->where('mailing_type', '<>', 'other-mail');
        });

        if (session()->has('mailing_filter.job_name')) {
            $atin = WorkOrder::whereHas('job', function ($q) {
                $q->where('name', 'like', '%'.session('mailing_filter.job_name').'%');
            })->pluck('id');

            $available_documents->whereIn('attachable_id', $atin);
        }

        if (session()->has('mailing_filter.mailing_type')) {
            $available_documents->whereHas('recipient', function ($q) {
                $q->where('mailing_type', session('mailing_filter.mailing_type'));
            });
        }

        if (session()->has('mailing_filter.client')) {
            $atin = WorkOrder::whereHas('job', function ($q) {
                $q->where('jobs.client_id', session('mailing_filter.client'));
            })->pluck('id');

            $available_documents->whereIn('attachable_id', $atin);
        }

        if (session()->has('mailing_filter.notice_type')) {
            $atin = WorkOrder::where('work_orders.type', session('mailing_filter.notice_type'))->pluck('id');

            $available_documents->whereIn('attachable_id', $atin);
        }

        //dd($available_documents);
        //$xdoc = $available_documents->first();
        //dd($xdoc->recipient->mailing_type);
        $clients = Client::whereHas('work_orders', function ($q) {
            $q->whereHas('attachments', function ($query) {
                $query->where('type', 'generated')->whereNull('printed_at');
            });
        })->get()->sortBy('company_name')->pluck('company_name', 'id')->prepend('All', 0);

        $mailing_types = [
            'all' => 'All',
            'standard-mail' => 'Regular Mail',
            'certified-green' => 'Certfied Green RR',
            'certified-nongreen' => 'Certfied Non Green',
            'registered-mail' => 'Registered Mail',
            'express-mail' => 'Express Mail',
            'other-mail' => 'eMail',

        ];
        $wo_types = WorkOrderType::all()->sortBy('name')->pluck('name', 'slug')->toArray();

        $data = [
            'available_documents' => $available_documents->paginate(200),
            'clients' => $clients,
            'mailing_types' => $mailing_types,
            'wo_types' => ['all' => 'All'] + $wo_types,
        ];

        return view('admin.mailing.create', $data);
    }

    public function setfilter(Request $request)
    {
        if ($request->has('resetfilter')) {
            if ($request->resetfilter == 'true') {
                session()->forget('mailing_filter');
            }
        }

        if ($request->has('job_name')) {
            if (strlen($request->job_name) == 0) {
                session()->forget('mailing_filter.job_name');
            } else {
                session(['mailing_filter.job_name' => $request->job_name]);
            }
        }

        if ($request->has('client_filter')) {
            if ($request->client_filter == 0) {
                session()->forget('mailing_filter.client');
            } else {
                session(['mailing_filter.client' => $request->client_filter]);
            }
        }

        if ($request->has('mailing_type')) {
            if ($request->mailing_type == 'all') {
                session()->forget('mailing_filter.mailing_type');
            } else {
                session(['mailing_filter.mailing_type' => $request->mailing_type]);
            }
        }

        if ($request->has('notice_type')) {
            if ($request->notice_type == 'all') {
                session()->forget('mailing_filter.notice_type');
            } else {
                session(['mailing_filter.notice_type' => $request->notice_type]);
            }
        }

        return redirect()->route('mailing.create');
    }

    public function resetfilter(Request $request)
    {
        session()->forget('mailing_filter');

        return redirect()->route('mailing.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (count($request->selected) > 0) {
            session()->forget('mailing_filter');
            $batch = new MailingBatch;
            $batch->save();
            foreach ($request->selected as $attachment_id => $val) {
                $detail = new BatchDetail();
                $detail->batch_id = $batch->id;
                $detail->work_order_recipient = $request->work_order_recipient[$attachment_id];
                $detail->attachment_id = $attachment_id;
                $detail->client_id = Attachment::find($attachment_id)->attachable->job->client->id;
                $detail->save();

                $attachment = Attachment::find($attachment_id);
                $attachment->printed_at = \Carbon\Carbon::now();
                $attachment->save();
            }

            $xpdf = $this->createPDF($batch->id);
            //echo $xpdf;return;

            return redirect()->route('mailing.print', $batch->id);
        } else {
            return redirect()->back()->withInput();
        }
    }

    public function createPDF($id)
    {
        //return true;
        // create Batch Summary Report and attach to batch

        $mailing_types = [
            'none' => 'None',
            'standard-mail' => 'Regular Mail',
            'certified-green' => 'CGRR',
            'certified-nongreen' => 'CNG',
            'registered-mail' => 'Registered Mail',
            'express-mail' => 'Express Mail',
            'other-mail' => 'eMail',
        ];

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
        $batch = MailingBatch::findOrFail($id);

        $details = $batch->details->load('attachment', 'recipient', 'recipient.party');

        $table = [];
        $work_orders = [];
        $total_postage = 0;
        foreach ($details as $dt) {
            if (! in_array($dt->recipient->work_order_id, $work_orders)) {
                $work_orders[] = $dt->recipient->work_order_id;
            }

            if (array_key_exists($dt->recipient->mailing_type, $table)) {
            } else {
                $table[$dt->recipient->mailing_type] = ['count' => 0, 'rate' => 0, 'amount' => 0];
                $summary_details[$dt->recipient->mailing_type] = [];
                $summary_totals[$dt->recipient->mailing_type] = 0;
            }
            $xcert_num = (strlen($dt->recipient->barcode) > 0 ? substr($dt->recipient->barcode, -7) : 'N/A');

            $mt = MailingType::where('type', $dt->recipient->mailing_type)->first();
            if ($mt) {
                $postage = $mt->postage;
                $fee = $mt->fee;
            } else {
                $postage = $dt->recipient->postage;
                $fee = $dt->recipient->fee;
            }

            $summary_details[$dt->recipient->mailing_type][] = [
                'attachment_id' => $dt->attachment_id,
                'zip' => $dt->recipient->party->contact->zip,
                'order_id' => $dt->recipient->work_order_id,
                'notice_id' => $dt->recipient->work_order->job->number,
                'copy_type' => $dt->recipient->party_type,
                'cert_num' => $xcert_num,
                'mailing_number' => $dt->recipient->mailing_number,
                'barcode' => preg_replace("/(\d{4})/", '$1 ', $dt->recipient->barcode),
                'sent_to' => $dt->recipient->firm_name,
                'sent_to_address' => nl2br($dt->recipient->address),
                'postage' => $postage,
                'fee' => $fee,
                'batch' => $id,
                'mail_type' => $dt->recipient->mailing_type,
            ];
            $summary_totals[$dt->recipient->mailing_type]++;
            $table[$dt->recipient->mailing_type]['count']++;
            $table[$dt->recipient->mailing_type]['rate'] = $postage + $fee + $dt->recipient->other;
            $table[$dt->recipient->mailing_type]['amount'] = ($postage + $fee) * $table[$dt->recipient->mailing_type]['count'];
            //$total_postage +=  $table[$dt->recipient->mailing_type]['amount'];
            $total_postage += ($postage + $fee + $dt->recipient->other);
        }

        //dd($summary_details);
        $certified_summary = [];
        if (array_key_exists('certified-green', $summary_details)) {
            $certified_summary = $summary_details['certified-green'];
        }
        if (array_key_exists('certified-nongreen', $summary_details)) {
            //dd($summary_details['certified-nongreen']);
            $certified_summary = array_merge($certified_summary, $summary_details['certified-nongreen']);
        }
        //dd($certified_summary);
        //return json_encode($total_postage);
        $company = CompanySetting::first();
        $data = [
            'table' => $table,
            'total_notices' => count($work_orders),
            'total_batch' => count($details),
            'total_postage' => $total_postage,
            'batch_id' => $id,
            'mailing_types' => $mailing_types,
            'summary_details' => $summary_details,
            'summary_totals' => $summary_totals,
            'parties_type' => $parties_type,
            'company_name' => $company->name,
            'company_address' => $company->address,
            'certified_summary' => $certified_summary,
        ];

        //$pdf = view('admin.mailing.files.summary', $data);
        $pdf = PDF::loadView('admin.mailing.files.summary', $data)->setPaper('Letter');
        $o_pdf = $pdf->output();
        $this->attachDocument($id, $o_pdf, 'batch-summary-report');

        // create Batch Detailed Report and attach to batch
        $pdf_detail = PDF::loadView('admin.mailing.files.detail', $data)->setPaper('Letter');
        $o_pdf_detail = $pdf_detail->output();
        $this->attachDocument($id, $o_pdf_detail, 'batch-detail-report');
        // if required create certified mail report andattacht to batch
        $pdf_certified = null;
        if (array_key_exists('certified-green', $table) || array_key_exists('certified-nongreen', $table)) {
            $cs = new Merger;
            $pNumber = 0;
            $pagecount = 0;
            foreach (array_chunk($certified_summary, 13, true) as $chunk) {
                $pagecount++;
            }
            foreach (array_chunk($certified_summary, 13, true) as $chunk) {
                $xdata = $data;
                $pNumber++;
                $xdata['pNumber'] = $pNumber;
                $xdata['pagecount'] = $pagecount;
                $xdata['certified_summary'] = $chunk;

                $pdf_certified = PDF::loadView('admin.mailing.files.certified', $xdata)
                        ->setPaper('Letter')
                        ->setOrientation('landscape')
                        ->setOption('disable-smart-shrinking', true)
                        ->setOption('margin-bottom', 5)
                        ->setOption('margin-top', 5)
                        ->setOption('margin-left', 5)
                        ->setOption('margin-right', 5)
                        ->setOption('dpi', 150);

                $cs->addRaw($pdf_certified->output());
                $this->attachDocument($id, $pdf_certified->output(), 'certified-report');
            }
        }

        // merge all documents and attach to batch.
        $m = new Merger();
        $m->addRaw($o_pdf);
        $m->addRaw($o_pdf_detail);
        if (isset($cs)) {
            $m->addRaw($cs->merge());
        }
        if (array_key_exists('certified-green', $table)) {
            foreach ($summary_details['certified-green'] as $att_id) {
                $attachment = Attachment::findOrFail($att_id['attachment_id']);
                $pdf_attached = Storage::get($attachment->file_path);
                $m->addRaw($pdf_attached);
            }
            //$m->addRaw($pdf_certified);
        }

        if (array_key_exists('certified-nongreen', $table)) {
            foreach ($summary_details['certified-nongreen'] as $att_id) {
                $attachment = Attachment::findOrFail($att_id['attachment_id']);
                $pdf_attached = Storage::get($attachment->file_path);
                $m->addRaw($pdf_attached);
            }
        }
        foreach ($summary_details as $key => $details) {
            if ($key != 'certified-green' && $key != 'certified-nongreen') {
                foreach ($details as $att_id) {
                    $attachment = Attachment::findOrFail($att_id['attachment_id']);
                    $pdf_attached = Storage::get($attachment->file_path);
                    $m->addRaw($pdf_attached);
                }
            }
        }
        $merged = $m->merge();
        $this->attachDocument($id, $merged, 'mailing-final');

        // mark oriignal documents as printed
        //foreach($summary_details as $key => $details) {

        //         foreach($details as $att_id) {
        //            $attachment = Attachment::findOrFail($att_id['attachment_id']);
        //            $attachment->printed_at= \Carbon\Carbon::now();
        //            $attachment->save();
        //         }

        //}
    }

    public function attachDocument($id, $content, $name_type)
    {
        $batch = MailingBatch::findOrFail($id);

        $xpath = 'attachments/mailings/'.$id.'/pdfs/';
        $file_path = $xpath.$name_type.'-'.$id.'.pdf';
        Storage::put($file_path, $content);

        //lets Attach the file to the Work Order
        $attachment = new Attachment();

        $attachment->type = 'mailing-generated';

        $xdescription = 'Automatically generated '.$name_type.' for mailing batch '.$id;

        $attachment->description = strtoupper($xdescription);
        $attachment->original_name = $name_type.'-'.$id.'.pdf';
        $attachment->file_mime = 'application/pdf';
        $attachment->file_size = Storage::size($file_path);
        $attachment->user_id = Auth::user()->id;
        $batch->attachments()->save($attachment);
        $attachment->file_path = $file_path;
        $attachment->save();

        $xblob = Storage::get($file_path);
        $img = new \Imagick();
        $img->readImageBlob($xblob);
        $img->setIteratorIndex(0);
        $img->setImageFormat('png');
        $img->setbackgroundcolor('rgb(64, 64, 64)');
        $img->thumbnailImage(300, 300, true, true);
        Storage::put($xpath.'thumbnail-'.$attachment->id.'.png', $img);
        $attachment->thumb_path = $xpath.'thumbnail-'.$attachment->id.'.png';
        $attachment->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $batch = MailingBatch::findOrFail($id);

        $attachment = $batch->attachments->where('original_name', 'mailing-final-'.$id.'.pdf')->first();

        if ($attachment) {
            $data = [
                'id' => $id,
            ];

            return view('admin.mailing.show', $data);
        } else {
            return redirect()->back();
        }
    }

    public function view($id)
    {
        $batch = MailingBatch::findOrFail($id);
        $attachment = $batch->attachments->where('original_name', 'mailing-final-'.$id.'.pdf')->first();
        $content = Storage::get($attachment->file_path);

        return Response::make($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="mailing-final-'.$id.'.pdf"',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    public function mailingprint($id)
    {
        $batch = MailingBatch::findOrFail($id);
        $ftp_locations = FtpLocation::all()->load('server');
        $data = [
            'batch' => $batch,
            'ftp_locations' => $ftp_locations,
        ];

        return view('admin.mailing.print', $data);
    }

    public function uploadmanifest($id, Request $request)
    {
        if ($request['file'] == null || $request['file'] == '') {
            Session::flash('message', 'File is required.');

            return redirect()->back();
        }

        $batch = MailingBatch::findOrFail($id);
        $f = $request->file('file');

        $max_uploadfileSize = min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
        $max_uploadfileSize = substr($max_uploadfileSize, 0, -1) * 1024 * 1024;
        if ($f->getSize() > $max_uploadfileSize) {
            Session::flash('message', 'This file is too large to upload.');

            return redirect()->back();
        }

        $xfilename = 'manifest-'.$id.'.'.$f->guessExtension();

        $xpath = 'attachments/manifest/';
        $f->storeAs($xpath, $xfilename);
        $batch->manifest_file = $xfilename;
        $batch->file_mime = $f->getMimeType();
        $batch->save();

        Session::flash('message', 'Manifest doc uploaded to Mailing batch #'.$batch->id);

        return redirect()->back();
    }

    public function downloadmanifest($id)
    {
        $batch = MailingBatch::findOrFail($id);
        $xfilename = $batch->manifest_file;
        $xpath = 'attachments/manifest/';
        $contents = Storage::get($xpath.$xfilename);
        $response = Response::make($contents, '200', [
            'Content-Type' => $batch->file_mime,
            'Content-Disposition' => 'attachment; filename="'.$xfilename.'"',
        ]);

        return $response;
    }

    public function deletemanifest($id)
    {
        $batch = MailingBatch::findOrFail($id);
        $xfilename = $batch->manifest_file;
        $xpath = 'attachments/manifest/';
        Storage::delete($xpath.$xfilename);

        $batch->manifest_file = null;
        $batch->file_mime = null;
        $batch->save();

        Session::flash('message', 'Manifest doc of Mailing batch #'.$batch->id.' deleted.');

        return redirect()->back();
    }

    public function process(Request $request, $id)
    {
        if ($request->has('printing_method')) {
            if ($request->printing_method == 'local') {
                //cset dates
                // mark oriignal documents as printed
                $batch = MailingBatch::findOrFail($id);

                foreach ($batch->details as $dt) {
                    $attachment = $dt->attachment;
                    $attachment->printed_at = \Carbon\Carbon::now();
                    $attachment->save();
                }

                $url = route('mailing.view', $id);
                $data = [
                    'url' => $url,
                ];

                return view('admin.mailing.exeprint', $data);
            } else {
                $batch = MailingBatch::findOrFail($id);

                foreach ($batch->details as $dt) {
                    $attachment = $dt->attachment;
                    $attachment->printed_at = \Carbon\Carbon::now();
                    $attachment->save();
                }
                $data = [
                    'batchId' => $id,
                    'location' => $request->printing_method,
                ];
                //dispatch(new FTPUpload($id,$request->printing_method));

                $ftplocation = FtpLocation::findOrFail($request->printing_method);
                $batch = MailingBatch::findOrFail($id);
                $attachment = $batch->attachments->where('original_name', 'mailing-final-'.$id.'.pdf')->first();

                $local_adapter = new LocalAdapter(storage_path());
                $local = new Filesystem($local_adapter);

                // And we want to copy it to our FTP
                $ftp_adapter = new FtpAdapter([
                    'host' => $ftplocation->server->ftp_host,
                    'username' => $ftplocation->server->ftp_user,
                    'password' => $ftplocation->server->ftp_password,
                ]);
                $ftp = new Filesystem($ftp_adapter);

                // Mount the two filesystems
                $mountManager = new MountManager([
                    'local' => $local,
                    'ftp' => $ftp,
                ]);

                // Copy the file from our local disk to the ftp disk
                $mountManager->copy('local://app/'.$attachment->file_path, 'ftp://'.$ftplocation->path.'/'.$attachment->original_name);
                // Copy the file from our local disk to the ftp disk
                //$mountManager->move( 'local://some/file.ext', 'ftp://some/file.ext' );

                //Session::flash('message', 'Mailing batch ' . $batch->id . ' FTP  printed');
                return view('admin.mailing.confirmation');
            }
        } else {
            Session::flash('message', 'ERROR - Mailing batch '.$batch->id.' FTP NOT  printed');

            return redirect()->back();
        }
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $batch = MailingBatch::findOrFail($id);
        foreach ($batch->details as $xedt) {
            if ($xedt->attachment) {
                $xda = $xedt->attachment;
                $xda->printed_at = null;
                $xda->resent_at = null;
                $xda->resent = 0;
                $xda->resent_id = 0;
                $xda->save();
            }
            $xedt->delete();
        }
        foreach ($batch->attachments as $xatt) {
            $xatt->delete();
        }
        $batch->delete();
        Session::flash('message', 'Mailing batch '.$batch->id.' deleted');

        return redirect()->back();
    }

    public function addTrackingNumber(Request $request)
    {
        $attachment = Attachment::where('id', $request->document_id)->first();
        if (empty($attachment)) {
            return 0;
        }
        $recipient = $attachment->recipient;
        $recipient->mailing_number = $request->tracking_number;
        $recipient->save();

        return $attachment->id;
    }
}
