<?php

namespace App\Http\Controllers\Admin;

use App\Attachment;
use App\BatchDetail;
use App\Client;
use App\CompanySetting;
use App\Http\Controllers\Controller;
use App\Job;
use App\Mail\NoticeDelivery;
use App\MailingType;
use App\Template;
use App\WorkOrder;
use App\WorkOrderRecipient;
use App\WorkOrderType;
use Auth;
use iio\libmergepdf\Merger;
use iio\libmergepdf\Pages;
use Illuminate\Http\Request;
use Mail;
use PDF;
use Settings;
use Storage;

class MailingHistoryController extends Controller
{
    public function index()
    {

       // $doc_ids = BatchDetail::all()->pluck('attachment_id')->toArray();
        // $available_documents = Attachment::query()->whereIn('id', $doc_ids)->where('type','generated')->where('resent','!=', 0)->where('resent_at', null)->where('attachable_id','!=', null)->with('recipient');
        $available_documents = Attachment::query()->join('batch_details', 'attachments.id', '=', 'batch_details.attachment_id')->where('attachments.type', 'generated')->where('attachments.resent', '!=', 0)->where('attachments.resent_at', null)->where('attachments.attachable_id', '!=', null)->orderBy('attachments.created_at', 'desc');

        if (session()->has('mailinghistory_filter.mailing_type')) {
            $available_documents->whereHas('recipient', function ($q) {
                $q->where('mailing_type', session('mailinghistory_filter.mailing_type'));
            });
        }

        if (session()->has('mailinghistory_filter.client')) {
            $atin = WorkOrder::whereHas('job', function ($q) {
                $q->where('jobs.client_id', session('mailinghistory_filter.client'));
            })->pluck('id');

            $available_documents->whereIn('attachable_id', $atin);
        }

        $jobs = [0 => 'All'];
        if (session()->has('mailinghistory_filter.job')) {
            $cin = WorkOrder::whereHas('job', function ($q) {
                $q->where('jobs.id', session('mailinghistory_filter.job'));
            })->pluck('id');

            $available_documents->whereIn('attachable_id', $cin);

            $xjob = session('mailinghistory_filter.job');
            $job = Job::where('id', $xjob)->first();
            $jobs = [$xjob => $job ? $job->name : ''];
        }
        if (session()->has('mailinghistory_filter.wo_types')) {
            $win = WorkOrder::where('type', session('mailinghistory_filter.wo_types'))->pluck('id');

            $available_documents->whereIn('attachable_id', $win);
        }

        if (session()->has('mailinghistory_filter.barcode')) {
            $available_documents->whereHas('recipient', function ($q) {
                $q->where('barcode', session('mailinghistory_filter.barcode'));
            });
        }

        if (session()->has('mailinghistory_filter.daterange')) {
            if (session('mailinghistory_filter.daterange') != '') {
                $dates = explode(' - ', session('mailinghistory_filter.daterange'));
                //dd($dates);
                $from_date = \Carbon\Carbon::createFromFormat('m-d-Y', $dates[0]);
                $to_date = \Carbon\Carbon::createFromFormat('m-d-Y', $dates[1]);
                // $available_documents->where([['printed_at','>=',$from_date],['printed_at','<=',$to_date]])->orderBy('printed_at','desc');
                $from = substr($from_date, 0, 10).' 00:00:00';
                $to = substr($to_date, 0, 10).' 23:59:59';
                $available_documents->where([['printed_at', '>=', $from], ['printed_at', '<=', $to]])->orderBy('printed_at', 'desc');
            }
        }

        // $clients = Client::whereHas('work_orders', function($q) use ($doc_ids) {
        //     $q->whereHas ('attachments', function($query) use ($doc_ids){
        //         $query->whereIn('id', $doc_ids)->where('type','generated');
        //     });
        // })->get()->sortBy('company_name')->pluck('company_name', 'id')->prepend('All',0);
        $client_ids = \App\User::where('deleted_at', null)->where('approve_status', 'approved')->pluck('client_id')->toArray();

        $clients = Client::where('status', 4)->whereIn('id', $client_ids)->get()->sortBy('company_name')->pluck('company_name', 'id')->prepend('All', 0);

        $mailing_types = [
            'all' => 'All',
            'standard-mail' => 'Regular Mail',
            'certified-green' => 'Certfied Green RR',
            'certified-nongreen' => 'Certfied Non Green',
            'registered-mail' => 'Registered Mail',
            'express-mail' => 'Express Mail',
            'other-mail' => 'eMail',
        ];

        $wo_types = ['0' => 'All'] + WorkOrderType::all()->pluck('name', 'slug')->toArray();

        $data = [
            'mailings' => $available_documents->paginate(100),
            'clients' => $clients,
            'mailing_types' => $mailing_types,
            'wo_types' => $wo_types,
            'jobs' => $jobs,
        ];

        return view('admin.mailinghistory.index', $data);
    }

    public function sent()
    {
        // $doc_ids = BatchDetail::pluck('attachment_id')->toArray();
        // $available_documents = Attachment::query()->whereIn('id', $doc_ids)->where('type','generated')->orderBy('created_at','desc')->with('recipient');
        $available_documents = Attachment::query()->join('batch_details', 'attachments.id', '=', 'batch_details.attachment_id')->where('attachments.type', 'generated')->orderBy('attachments.created_at', 'desc');

        if (session()->has('mailinghistory_filter.mailing_type')) {
            $available_documents->whereHas('recipient', function ($q) {
                $q->where('mailing_type', session('mailinghistory_filter.mailing_type'));
            });
        }

        if (session()->has('mailinghistory_filter.client')) {
            $atin = WorkOrder::whereHas('job', function ($q) {
                $q->where('jobs.client_id', session('mailinghistory_filter.client'));
            })->pluck('id');

            $available_documents->whereIn('attachable_id', $atin);
        }

        $jobs = [0 => 'All'];
        if (session()->has('mailinghistory_filter.job')) {
            $cin = WorkOrder::whereHas('job', function ($q) {
                $q->where('jobs.id', session('mailinghistory_filter.job'));
            })->pluck('id');

            $available_documents->whereIn('attachable_id', $cin);
            $xjob = session('mailinghistory_filter.job');
            $job = Job::where('id', $xjob)->first();
            $jobs = [$xjob => $job ? $job->name : ''];
        }

        if (session()->has('mailinghistory_filter.wo_types')) {
            $win = WorkOrder::where('type', session('mailinghistory_filter.wo_types'))->pluck('id');

            $available_documents->whereIn('attachable_id', $win);
        }

        // $clients = Client::whereHas('work_orders', function($q) use ($doc_ids) {
        //     $q->whereHas ('attachments', function($query) use ($doc_ids){
        //         $query->whereIn('id', $doc_ids)->where('type','generated');
        //     });
        // })->get()->sortBy('company_name')->pluck('company_name', 'id')->prepend('All',0);
        $client_ids = \App\User::where('deleted_at', null)->where('approve_status', 'approved')->pluck('client_id')->toArray();

        $clients = Client::where('status', 4)->whereIn('id', $client_ids)->get()->sortBy('company_name')->pluck('company_name', 'id')->prepend('All', 0);

        if (session()->has('mailinghistory_filter.barcode')) {
            $available_documents->whereHas('recipient', function ($q) {
                $q->where('barcode', session('mailinghistory_filter.barcode'));
            });
        }

        if (session()->has('mailinghistory_filter.daterange')) {
            if (session('mailinghistory_filter.daterange') != '') {
                $dates = explode(' - ', session('mailinghistory_filter.daterange'));
                //dd($dates);
                $from_date = \Carbon\Carbon::createFromFormat('m-d-Y', $dates[0]);
                $to_date = \Carbon\Carbon::createFromFormat('m-d-Y', $dates[1]);
                $from = substr($from_date, 0, 10).' 00:00:00';
                $to = substr($to_date, 0, 10).' 23:59:59';

                //$available_documents->whereBetween('printed_at',[$from_date,$to_date])->orderBy('printed_at');
                $available_documents->where([['printed_at', '>=', $from], ['printed_at', '<=', $to]])->orderBy('printed_at', 'desc');
            }
        }

        //dd($available_documents);
        //$xdoc = $available_documents->first();
        //dd($xdoc->recipient->mailing_type);

        $mailing_types = [
            'all' => 'All',
            'standard-mail' => 'Regular Mail',
            'certified-green' => 'Certfied Green RR',
            'certified-nongreen' => 'Certfied Non Green',
            'registered-mail' => 'Registered Mail',
            'express-mail' => 'Express Mail',
            'other-mail' => 'eMail',
        ];
        $wo_types = ['0' => 'All'] + WorkOrderType::all()->sortBy('name')->pluck('name', 'slug')->toArray();

        $data = [
            'mailings' => $available_documents->paginate(100),
            'clients' => $clients,
            'mailing_types' => $mailing_types,
            'wo_types' => $wo_types,
            'jobs' => $jobs,
        ];

        return view('admin.mailinghistory.index2', $data);
    }

    public function resend($id)
    {
        $attachment = Attachment::findOrFail($id);
        $mailing_types = [
            'standard-mail' => 'Regular Mail',
            'certified-green' => 'Certfied Green RR',
            'certified-nongreen' => 'Certfied Non Green',
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
        $data = [
            'attachment' => $attachment,
            'mailing_types' => $mailing_types,
            'parties_type' => $parties_type,
        ];

        return view('admin.mailinghistory.mailparties', $data);
    }

    public function resend2($id)
    {
        $attachment = Attachment::findOrFail($id);
        if ($attachment->resent == -1) {
            $attachment->resent = 0;
            $attachment->resent_reason = null;
        } else {
            $attachment->resent = -1;
        }
        $attachment->save();

        return redirect()->route('mailinghistory.index2');
    }

    public function savepdf(Request $request, $attachment_id)
    {
        $mailing_types = [
            'none' => 'None',
            'standard-mail' => 'Regular Mail',
            'certified-green' => 'Certfied Green RR',
            'certified-nongreen' => 'Certfied Non Green',
            'registered-mail' => 'Registered Mail',
            'express-mail' => 'Express Mail',
            'other-mail' => 'eMail',
        ];

        $current_recipient = WorkOrderRecipient::findOrFail($request->recipient_id);
        $work = $current_recipient->work_order;

        $pdf_pages = [];
        $xmailing_type = $request->mailing_type;
        $xfirm_name = $request->firm_name;
        $xparty_type = $request->party_type;
        $xattention_name = $request->attention_name;
        $xaddress = $request->address;
        $xemail = $request->email;
        $xmailing_number = $request->mailing_number;
        $xalso_email = $request->has('also_email') ? $request->also_email : null;
        $xalso_email_value = $request->has('also_email_value') ? $request->also_email_value : null;

        if ($request->has('return_receipt')) {
            $xreturn_receipt = 1;
        } else {
            $xreturn_receipt = 0;
        }
        unset($recipient);

        if ($xmailing_type != 'none') {
            $recipient = new WorkOrderRecipient();
            $recipient->work_order_id = $work->id;
            $recipient->party_id = $request->party_id;
            $recipient->party_type = $xparty_type;
            $recipient->firm_name = $xfirm_name;
            $recipient->attention_name = $xattention_name;
            $recipient->address = $xaddress;
            $recipient->mailing_type = $xmailing_type;
            if ($xmailing_type == 'other-mail') {
                $recipient->email = $xemail;
            } elseif ($xalso_email && $xalso_email_value) {
                $recipient->email = $xalso_email_value;
            }

            $recipient->save();

            if ($xmailing_type == 'certified-green' || $xmailing_type == 'certified-nongreen') {
                $recipient->return_receipt = $xreturn_receipt;
                // $xbc =  "420";
                // $contact = \App\JobParty::find($request->party_id)->contact;
                // if (strlen($contact->zip) == 0 ) {
                //     $xzip = "00000";
                // } else {
                //     if(strlen($contact->zip) > 5) {
                //         $xzip = substr($contact->zip,5);
                //     } else {
                //         $xzip = $contact->zip;
                //     }
                // }
                // $xbc .= $xzip;
                /// 25 CHARS
                $xbc = '';

                ///

                $xbc .= '92';
                $mt = MailingType::where('type', $xmailing_type)->first();
                if ($mt) {
                    if (strlen($mt->stc) == 3) {
                        $xbc .= $mt->stc;
                    } else {
                        $xbc .= '000';
                    }
                } else {
                    $xbc .= '000';
                }

                if (! Settings::has('barcode.serial')) {
                    $serial = 1000000;
                    $srcid = 1;
                    Settings::set('barcode.serial', $serial);
                    Settings::set('barcode.srcid', $srcid);
                } else {
                    $srcid = Settings::get('barcode.srcid');
                    $serial = Settings::get('barcode.serial');
                    if ($serial >= 9999998) {
                        $serial = 1000000;
                        if ($srcid = 99) {
                            $srcid = 1;
                        } else {
                            $srcid++;
                        }
                    } else {
                        $serial = $serial + 7;
                    }
                    Settings::set('barcode.srcid', $srcid);
                    Settings::set('barcode.serial', $serial);
                }

                //$xbc .= sprintf("%02d", $srcid);
                $xbc .= '902222278'; //MID
                $xbc .= $serial;

                $xbc .= $this->LhunMod10($xbc);

                $recipient->barcode = $xbc;
            }
            if ($xmailing_type == 'registered-mail' || $xmailing_type == 'express-mail') {
                $recipient->mailing_number = $xmailing_number;
            }

            $recipient->save();
        }

        $xpath = 'attachments/workorders/'.$work->id.'/pdfs/';
        $file_path = $xpath.'document-'.$work->id.'-'.$request->party_id.'.pdf';
        $pdf_exist = Storage::disk()->exists($file_path);

        if (isset($recipient)) {
            $address_type = $work->job->client->return_address_type;
            $sni_address = nl2br(CompanySetting::first()->name." \n ".CompanySetting::first()->address);
            //$a= ($address_type=='sni') ? $sni_address:$wo->job->client->mailing_address;
            foreach ($work->pdf_pages as $xpage) {
                $data = unserialize($xpage->fields);

                $signature = $work->job->client->signature;

                $data['signature'] = $signature;
                $data['client_mailing_address'] = ($address_type == 'sni') ? $sni_address : $work->job->client->mailing_address;

                $data['barcode'] = $recipient->barcode;
                $data['mailing_number'] = $recipient->mailing_number;
                $data['mailing_type'] = $recipient->mailing_type;
                $xaddress = $recipient->firm_name;
                if ($recipient->attention_name != '') {
                    $xaddress .= '<br />ATTN: '.$recipient->attention_name;
                }
                $xaddress .= '<br />'.nl2br($recipient->address);
                $data['mailing_address'] = $xaddress;
                $data['wo_number'] = $work->id;
                $data['envelope_wording'] = $request->envelope_wording;

                if ($xpage->type == 'notice-to-owner-back') {
                    //  $data['client_mailing_address'] = $work->job->client->mailing_address;
                    // $data['barcode'] = $recipient->barcode;
                    // $xaddress = $recipient->firm_name ;
                    // if ($recipient->attention_name <> '') {
                    //     $xaddress .= '<br />ATTN: ' . $recipient->attention_name;
                    // }
                    // $xaddress .= '<br />' . nl2br($recipient->address);
                    // $data['mailing_address'] = $xaddress;

                    $data['document'] = 'ntoback';
                    $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    //return $pdf->download();
                }

                if ($xpage->type == 'amended-notice-to-owner-back') {
                    $data['document'] = 'antoback';
                    $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                }

                if (! $pdf_exist) {
                    if ($xpage->type == 'notice-to-owner') {
                        $job = $work->job;
                        if (! $job->secret_key) {
                            $job->secret_key = rand(1, 9).rand(1, 9).rand(1, 9).rand(1, 9).rand(1, 9).rand(1, 9).rand(1, 9).rand(1, 9);
                            $job->save();
                        }
                        $data['barcode_id'] = substr('0000000000', 0, -strlen($job->id)).$job->id;
                        $data['barcode_key'] = '00'.$job->secret_key;
                        $data['document'] = 'nto';
                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    }
                    if ($xpage->type == 'amended-notice-to-owner') {
                        $job = $work->job;
                        if (! $job->secret_key) {
                            $job->secret_key = rand(1, 9).rand(1, 9).rand(1, 9).rand(1, 9).rand(1, 9).rand(1, 9).rand(1, 9).rand(1, 9);
                            $job->save();
                        }
                        $data['barcode_id'] = substr('0000000000', 0, -strlen($job->id)).$job->id;
                        $data['barcode_key'] = '00'.$job->secret_key;
                        $data['document'] = 'anto';
                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'amend-claim-of-lien') {
                        $data['document'] = 'acol';
                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'claim-of-lien') {
                        $data['document'] = 'col';
                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'conditional-waiver-and-release-of-lien-upon-final-payment') {
                        $data['document'] = 'cwarolufp';
                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'conditional-waiver-and-release-of-lien-upon-progress-payment') {
                        $data['document'] = 'cwarolupp';
                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'contractors-final-payment-affidavit') {
                        $data['document'] = 'cfpa';
                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'notice-of-bond') {
                        $data['document'] = 'nob';
                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'notice-of-commencement') {
                        $data['document'] = 'noc';
                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    }
                    if ($xpage->type == 'notice-of-termination') {
                        $data['document'] = 'not';
                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'notice-of-contest-of-claim-against-payment-bond') {
                        $data['document'] = 'nococapb';
                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'notice-of-contest-of-lien') {
                        $data['document'] = 'nocol';
                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'notice-of-non-payment') {
                        if (strlen($recipient->barcode) > 0) {
                            // $data['barcode'] = 'XXXX XXXX XXXX '.substr($recipient->barcode,-4);;
                            $data['barcode'] = $recipient->barcode;
                        }
                        $data['document'] = 'nonp';
                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'notice-of-nonpayment-for-bonded-private-jobs-statutes-713') {
                        if (strlen($recipient->barcode) > 0) {
                            $data['barcode'] = $recipient->barcode;
                        }
                        $data['document'] = 'nonp713';
                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    }
                    if ($xpage->type == 'notice-of-nonpayment-for-government-jobs-statutes-255') {
                        if (strlen($recipient->barcode) > 0) {
                            $data['barcode'] = $recipient->barcode;
                        }
                        $data['document'] = 'nonp255';
                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'notice-of-nonpayment-with-intent-to-lien-andor-foreclose') {
                        if (strlen($recipient->barcode) > 0) {
                            //$data['barcode'] = 'XXXX XXXX XXXX '.substr($recipient->barcode,-4);;
                            $data['barcode'] = $recipient->barcode;
                        }
                        $data['document'] = 'nonwitlaf';
                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'partial-satisfaction-of-lien') {
                        $data['document'] = 'psol';
                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'out-of-state-nto-preliminary-notice-of-lien-rights') {
                        $data['document'] = 'pnolr';
                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    }
                    if ($xpage->type == 'rescission-letter') {
                        $data['document'] = 'rl';
                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'satisfaction-of-lien') {
                        $data['document'] = 'sol';
                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    }
                    if ($xpage->type == 'sworn-statement-of-account') {
                        $data['document'] = 'ssoa';
                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'waiver-and-release-of-lien-upon-final-payment') {
                        $data['document'] = 'warolufp';
                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'waiver-and-release-of-lien-upon-progress-payment') {
                        $data['document'] = 'warolupp';
                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    }
                    if ($xpage->type == 'waiver-of-right-to-claim-against-bond-final-payment') {
                        $data['document'] = 'wortcabfp';
                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'waiver-of-right-to-claim-against-bond-progress-payment') {
                        $data['document'] = 'wortcabpp';
                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    }
                }
                if (isset($pdf)) {
                    $pdf_pages[] = $pdf->output();
                    unset($pdf);
                }

                if (! ($xpage->type == 'notice-to-owner' || $xpage->type == 'notice-to-owner-back' || $xpage->type == 'amended-notice-to-owner' || $xpage->type == 'amended-notice-to-owner-back')) {
                    $data['document'] = 'allback';
                    $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    $pdf_pages[] = $pdf->output();
                    unset($pdf);
                }
            }
        }

        $pagecount = 0;
        $m = new Merger();
        if ($pdf_exist) {
            $existPDF = Storage::get($file_path);
            $m_existPDF = new Merger();
            $m_existPDF->addRaw($existPDF);
            $existPDF_withoutLastPage = $m_existPDF->mergeWithoutLastPage();
            $m->addRaw($existPDF_withoutLastPage);
        }

        if (count($pdf_pages) > 0) {
            $pagecount++;

            foreach ($pdf_pages as $xpdf) {
                $m->addRaw($xpdf);
            }

            // $xpath = 'attachments/workorders/' . $work->id . '/pdfs/';
            // $file_path = $xpath . "document-" . $work->id . "-" . $request->party_id . ".pdf";

            // $content = Storage::get($file_path);
            // $m = new Merger();$m->addRaw($content);
            // $content = $m->mergeWithoutLastPage();
            // $m->addRaw($content);
            // $m->addRaw( $pdf_pages[1]);
            // $content = $m->merge();

            Storage::put($file_path, $m->merge());

            //lets Attach the file to the Work Order
            $attachment = new Attachment();

            $attachment->type = 'generated';
            $attachment->generated_id = $recipient->id;
            $xdescription = 'Automatically generated '.$work->order_type->name.' for '.$recipient->firm_name;
            if ($recipient->attention_name != '') {
                $xdescription .= ' ('.$recipient->attention_name.')';
            }
            $attachment->description = strtoupper($xdescription);
            $attachment->original_name = 'document-'.$work->id.'-'.$request->party_id.'.pdf';
            $attachment->file_mime = 'application/pdf';
            $attachment->file_size = Storage::size($file_path);
            $attachment->user_id = Auth::user()->id;
            $work->attachments()->save($attachment);
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

            $old_attachment = Attachment::findOrFail($attachment_id);
            $old_attachment->resent = 1;
            $old_attachment->resent_at = \Carbon\Carbon::now();
            $old_attachment->resent_id = $attachment->id;
            $old_attachment->save();
        } else {
        }

        //Lets create the invoice if it applies
        $work->fresh();
        $attach_path = $file_path;
        $client = $work->job->client;
        if ($recipient->mailing_type == 'other-mail' || $recipient->email) {
            Mail::to($recipient)->send(new NoticeDelivery($recipient, $attach_path, $work->job->client));
            $recipient->email_sent = \Carbon\Carbon::now();
            $recipient->save();
        }

        //dd($client);
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

        $new_lines = [];
        if ($doit) {
            $xrx = $recipient;
            $tlines = $template->lines()->where('template_lines.type', $xrx->mailing_type)->get();
            if ($work->service == 'self') {
                $sslines = $template->lines()->where('template_lines.type', $xrx->mailing_type.'-ss')->get();
                if (count($sslines) > 0) {
                    $tlines = $sslines;
                }
            }
            foreach ($tlines as $xln) {
                if (array_key_exists($xrx->mailing_type.'-'.$xln->description, $new_lines)) {
                    $new_lines[$xrx->mailing_type.'-'.$xln->description]['quantity'] += $xln->quantity;
                } else {
                    $xline['recipient_id'] = $xrx->id;
                    $xline['description'] = $xln->description;
                    $xline['price'] = $xln->price;
                    $xline['quantity'] = $xln->quantity;
                    $xline['mailing_type'] = $xrx->mailing_type;
                    $new_lines[$xrx->mailing_type.'-'.$xln->description] = $xline;
                }
            }
        } else {
        }

        session(['mailinghistory.generated.work_id', $work->id]);
        $data = [
            'client' => $client,
            'work_order_id' => $work->id,
            'work_order_number' => $work->number,
            'recipient' => $recipient,
            'new_lines' => $new_lines,
            'mailing_types' => $mailing_types,
        ];

        return view('admin.mailinghistory.invoice', $data);
    }

    public function setfilter(Request $request)
    {
        if ($request->has('resetfilter')) {
            if ($request->resetfilter == 'true') {
                session()->forget('mailinghistory_filter');
            }
        }

        if ($request->has('client_filter')) {
            if ($request->client_filter == 0) {
                session()->forget('mailinghistory_filter.client');
            } else {
                session(['mailinghistory_filter.client' => $request->client_filter]);
            }
        }

        if ($request->has('mailing_type')) {
            if ($request->mailing_type == 'all') {
                session()->forget('mailinghistory_filter.mailing_type');
            } else {
                session(['mailinghistory_filter.mailing_type' => $request->mailing_type]);
            }
        }

        if ($request->has('job')) {
            if ($request->job == '0') {
                session()->forget('mailinghistory_filter.job');
            } else {
                session(['mailinghistory_filter.job' => $request->job]);
            }
        }

        if ($request->has('barcode')) {
            if ($request->barcode == '') {
                session()->forget('mailinghistory_filter.barcode');
            } else {
                session(['mailinghistory_filter.barcode' => $request->barcode]);
            }
        }

        if ($request->has('wo_types')) {
            if ($request->wo_types == '0') {
                session()->forget('mailinghistory_filter.wo_types');
            } else {
                session(['mailinghistory_filter.wo_types' => $request->wo_types]);
            }
        }

        if ($request->has('daterange')) {
            if ($request->daterange == '') {
                session()->forget('mailinghistory_filter.daterange');
            } else {
                session(['mailinghistory_filter.daterange' => $request->daterange]);
            }
        }

        return redirect()->route('mailinghistory.index');
    }

    public function setfilter2(Request $request)
    {
        if ($request->has('resetfilter')) {
            if ($request->resetfilter == 'true') {
                session()->forget('mailinghistory_filter');
            }
        }

        if ($request->has('client_filter')) {
            if ($request->client_filter == 0) {
                session()->forget('mailinghistory_filter.client');
            } else {
                session(['mailinghistory_filter.client' => $request->client_filter]);
            }
        }

        if ($request->has('mailing_type')) {
            if ($request->mailing_type == 'all') {
                session()->forget('mailinghistory_filter.mailing_type');
            } else {
                session(['mailinghistory_filter.mailing_type' => $request->mailing_type]);
            }
        }

        if ($request->has('job')) {
            if ($request->job == '0') {
                session()->forget('mailinghistory_filter.job');
            } else {
                session(['mailinghistory_filter.job' => $request->job]);
            }
        }

        if ($request->has('barcode')) {
            if ($request->barcode == '') {
                session()->forget('mailinghistory_filter.barcode');
            } else {
                session(['mailinghistory_filter.barcode' => $request->barcode]);
            }
        }

        if ($request->has('wo_types')) {
            if ($request->wo_types == '0') {
                session()->forget('mailinghistory_filter.wo_types');
            } else {
                session(['mailinghistory_filter.wo_types' => $request->wo_types]);
            }
        }

        if ($request->has('daterange')) {
            if ($request->daterange == '') {
                session()->forget('mailinghistory_filter.daterange');
            } else {
                session(['mailinghistory_filter.daterange' => $request->daterange]);
            }
        }

        return redirect()->route('mailinghistory.index2');
    }

    public function resetfilter(Request $request)
    {
        session()->forget('mailinghistory_filter');

        return redirect()->route('mailinghistory.index');
    }

    public function resetfilter2(Request $request)
    {
        session()->forget('mailinghistory_filter');

        return redirect()->route('mailinghistory.index2');
    }

    public function LhunMod10($number)
    {
        $number = preg_replace('/[^0-9]/', '', $number);
        // change order of values to use in foreach
        $vals = array_reverse(str_split($number));
        //$vals = str_split($number);
        // multiply every other value by 2
        $mult = true;
        $even = 0;
        $odd = 0;
        foreach ($vals as $k => $v) {
            if ($mult) {
                $odd += $v;
            } else {
                $even += $v;
            }
            $mult = ! $mult;
        }

        // checks for two digits (>9)

        $total = ($odd * 3) + $even;
        // adds the values

        //gets the mod
        $md = $total % 10;
        if ($md == 0) {
            return 0;
        }

        // checks how much for 10
        $result = 10 - $md;

        // returns the value
        return $result;
    }
}
