<?php

namespace App\Http\Controllers\Researcher;

use App\Attachment;
use App\Http\Controllers\Controller;
use App\Mail\NoticeComplete;
use App\Mail\NoticeDelivery;
use App\MailingType;
use App\PdfPage;
use App\Template;
use App\WorkOrder;
use App\WorkOrderRecipient;
use Auth;
use iio\libmergepdf\Merger;
use iio\libmergepdf\Pages;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Mail;
use PDF;
use Response;
use Session;
use Settings;
use Storage;
use URL;

class PdfPagesController extends Controller
{
    public function deletedocument($id)
    {
        $work = WorkOrder::findOrFail($id);
        $attachments = $work->attachments()->where('type', 'generated')->get();
        $recipients = $work->recipients();
        foreach ($work->pdf_pages as $xpage) {
            $xpage->delete();
        }

        foreach ($attachments as $xattachment) {
            $xattachment->delete();
        }

        foreach ($recipients as $xrecipient) {
            $xrecipient->delete();
        }

        return redirect()->back();
    }

    public function askreset($id)
    {
        $work = WorkOrder::findOrFail($id);
        if (session('from')) {
            $backurl = session('from');
        } else {
            $backurl = '';
        }
        $data = [
            'work' => $work,
            'backurl' => $backurl,
        ];

        return view('researcher.pdf.document.reset', $data);
    }

    public function doreset(Request $request, $id)
    {
        $this->validate($request, [
            'reset' => 'required',
        ]);

        $work = WorkOrder::findOrFail($id);

        if ($request->reset == 'yes') {
            foreach ($work->pdf_pages as $xpage) {
                $xpage->delete();
            }

            foreach ($work->recipients as $xrecipient) {
                $xrecipient->delete();
            }

            return redirect()->route('workorders.document', $id)->with('from', $request->backurl);
        } else {
            if (strlen($request->backurl)) {
                return redirect()->to($request->backurl);
            }

            return redirect()->route('workorders.index');
        }
    }

    public function docancel(Request $request, $id)
    {
        $work = WorkOrder::findOrFail($id);
        foreach ($work->pdf_pages as $xpage) {
            $xpage->delete();
        }

        foreach ($work->recipients as $xrecipient) {
            $xrecipient->delete();
        }

        if (strlen($request->backurl)) {
            if ($request->backurl == url(route('workorders.reset.ask', $id))) {
                return redirect()->route('workorders.index');
            }
            //return redirect()->to($request->backurl);
        }

        return redirect()->route('workorders.index');
    }

    public function kickout()
    {
        Session::flash('message', 'Another user has begun generating this notice and has terminated your session.');
        Session::flash('message-class', 'alert-danger');

        return redirect()->route('workorders.index');
    }

    public function document($id)
    {
        if (session('from')) {
            $backurl = session('from');
        } else {
            $backurl = URL::previous();
        }
        $view = true;
        $work = WorkOrder::findOrFail($id);
        if (count($work->pdf_pages) > 0) {
            return redirect()->route('workorders.reset.ask', $id)->with(['from' => $backurl]);
        }

        $job = $work->job;

        $wo_nto = $job->workorders->where('status', '!=', 'temporary')->where('type', 'notice-to-owner')->first();

        $landowner = $job->parties->where('type', 'landowner')->first();

        $customer = $job->parties->where('type', 'customer')->first();
        //$client = $job->parties->where('type', 'client')->first();

        $client = $job->client;
        $gc = $job->parties->where('type', 'general_contractor')->first();
        $bond = $job->parties->where('type', 'bond')->first();
        $leaseholders = $job->parties->where('type', 'leaseholder');
        $copiers = $job->parties->where('type', 'copy_recipient');
        $copy = $copiers->get(0);
        $copyl = $copiers->get(1);
        if (1 == 0) {
        } else {
            $signature = $job->client->signature;
            $xleaseholders = [];
            foreach ($leaseholders as $ls) {
                $xleaseholders[] = [
                    'full_name' => $ls->contact->entity->firm_name,
                    'address' => $ls->contact->address_no_country,
                    'phone' => $ls->contact->phone,
                    'email' => $ls->contact->email,
                ];
            }
            if (count($xleaseholders) == 0) {
                $xleaseholders[] = [
                    'full_name' => '',
                    'address' => '',
                    'phone' => '',
                    'email' => '',
                ];
            }
            $client_company_name = $job->client->company_name;
            $client_signature = '';
            $client_phone = $job->client->phone;
            $client_fax = $job->client->fax;
            if ($job->started_at) {
                $nto_date = $job->started_at->format('m/d/Y');
            } else {
                $nto_date = '';
            }
            $today = date('m/d/Y');
            $nto_number = $job->number;
            $wo_number = $work->id;
            if ($landowner) {
                $land_owner_firm_name = $landowner->contact->entity->firm_name;

                $land_owner_address = $landowner->contact->address_no_country;
                $land_owner_phone = $landowner->contact->phone;
                $land_owner_email = $landowner->contact->email;
                $deed = $landowner->landowner_deed_number;
            } else {
                $land_owner_firm_name = '';

                $land_owner_address = '';
                $land_owner_phone = '';
                $land_owner_email = '';
                $deed = '';
            }

            if ($gc) {
                $gc_firm_name = $gc->contact->entity->firm_name;
                $gc_name = $gc->contact->full_name;
                $gc_address = $gc->contact->address_no_country;
                $gc_phone = $gc->contact->phone;
            } else {
                $gc_firm_name = '';
                $gc_name = '';
                $gc_address = '';
                $gc_phone = '';
            }

            if ($bond) {
                $bond_firm_name = $bond->contact->entity->firm_name;
                $bond_name = $bond->contact->full_name;
                $bond_address = $bond->contact->address_no_country;
                $bond_phone = $bond->contact->phone;
                $bond_type = $bond->bond_type;
            } else {
                $bond_firm_name = '';
                $bond_name = '';
                $bond_address = '';
                $bond_phone = '';
                $bond_type = '';
            }

            if ($copy) {
                $copy_firm_name = $copy->contact->entity->firm_name;
                $copy_name = $copy->contact->full_name;
                $copy_address = $copy->contact->address_no_country;
                $copy_phone = $copy->contact->phone;
            } else {
                $copy_firm_name = '';
                $copy_name = '';
                $copy_address = '';
                $copy_phone = '';
            }

            if ($copyl) {
                $copyl_firm_name = $copyl->contact->entity->firm_name;
                $copyl_name = $copyl->contact->full_name;
                $copyl_address = $copyl->contact->address_no_country;
                $copyl_phone = $copyl->contact->phone;
            } else {
                $copyl_firm_name = '';
                $copyl_name = '';
                $copyl_address = '';
                $copyl_phone = '';
            }

            $bank_firm_name = '';
            $bank_name = '';
            $bank_address = '';
            $bank_phone = '';
            if ($wo_nto) {
                $notification_date = $wo_nto->created_at->format('m/d/Y');
            } else {
                $notification_date = '';
            }

            if ($client) {
                $client_name = $client->full_name;
                if ($client->gender == 'female') {
                    $client_heshe = 'She';
                } else {
                    $client_heshe = 'He';
                }
                $client_phone = $client->phone;
                $client_email = $client->email;
                $client_address = $client->address_no_country;
                $client_county = $client->county;
                $client_title = $client->title;
            } else {
                $client_name = '';
                $client_heshe = '';
                $client_phone = '';
                $client_email = '';
                $client_address = '';
                $client_county = '';
                $client_title = '';
            }

            $materials = $job->default_materials;
            $job_name = $job->name;
            $job_address = $job->full_address_no_country;
            $job_county = $job->county;
            $job_contract_amount = $job->contract_amount;
            $job_legal_description = $job->legal_description;
            $job_type = $job->type;
            if ($job->started_at) {
                $job_start_date = $job->started_at->format('m/d/Y');
            } else {
                $job_start_date = '';
            }
            if ($job->last_day) {
                $job_last_date = $job->last_day->format('m/d/Y');
            } else {
                $job_last_date = '';
            }
            $folio = $job->folio_number;
            $noc = $job->noc_number;
            $project_number = $job->project_number;
            $legal_description = $job->legal_description;

            $general_contractors = $job->parties()->where('type', '=', 'general_contractor')->orderBy('type')->get();
            $xparties = [];
            foreach ($general_contractors as $pt) {
                $xparties[] = ['type' => $pt->type, 'full_name' => $pt->contact->full_name, 'company_name' => $pt->contact->entity->firm_name];
            }

            $parties = $job->parties()->where('type', '!=', 'general_contractor')->where('type', '!=', 'client')->orderBy('type')->get();
            foreach ($parties as $pt) {
                $xtype = $pt->type;
                if ($xtype == 'bond') {
                    $xtype .= ' - '.$pt->bond_type;
                }
                $key = array_search($pt->contact->entity->firm_name, array_column($xparties, 'company_name'));

                if ($key !== false) {
                    if (strpos($xparties[$key]['type'], $xtype) === false) {
                        $xparties[$key]['type'] = $xparties[$key]['type'].' / '.$xtype;
                    } else {
                    }
                } else {
                    $xparties[] = ['type' => $xtype, 'full_name' => $pt->contact->full_name, 'company_name' => $pt->contact->entity->firm_name];
                }
            }
            $xsureties = $job->parties()->where('type', '=', 'bond')->get();

            $sureties = [];
            foreach ($xsureties as $xt) {
                $sureties[] = [
                    'name' => $xt->contact->entity->firm_name,
                    'address' => $xt->contact->address_no_country,
                ];
            }
            //dd($xparties);

            if ($customer) {
                $customer_name = $customer->contact->entity->firm_name;
                $customer_address = $customer->contact->address_no_country;
            } else {
                $customer_name = '';
                $customer_address = '';
            }

            if ($work->type == 'amend-claim-of-lien') {
                $acolpage = [
                    'type' => $work->type,
                    'client_company_name' => $client_company_name,
                    'client_name' => $client_name,
                    'client_address' => $client_address,
                    'client_county' => $client_county,
                    'client_title' => $client_title,
                    'client_phone' => $client_phone,

                    'client_heshe' => $client_heshe,
                    'customer_name' => $customer_name,
                    'nto_date' => $nto_date,
                    'nto_number' => $nto_number,
                    'land_owner_name' => $land_owner_firm_name,
                    'amend_reason' => '',
                    'month' => '',
                    'year' => date('Y'),
                    'materials' => $materials,
                    'job_start_date' => $job_start_date,
                    'job_last_date' => $job_last_date,
                    'job_name' => $job_name,
                    'job_address' => $job_address,
                    'job_county' => $job_county,
                    'job_contract_amount' => $job_contract_amount,
                    'interest_amount' => 0,
                    'unpaid_balance' => 0,
                    'deed' => $deed,
                    'folio' => $folio,
                    'noc' => $noc,
                    'project_number' => $project_number,
                    'legal_description' => $legal_description,
                    'parties' => $xparties,
                    'leaseholders' => $xleaseholders,
                    'original_or_book' => 'Book ',
                    'original_date' => '',
                ];

                $newpage = new PdfPage();
                $newpage->work_order_id = $work->id;
                $newpage->type = $work->type;
                $newpage->save();

                $acolpage['page_id'] = $newpage->id;
                $newpage->fields = serialize($acolpage);
                $newpage->save();

//                $client_fax = $job->client->fax;
//                $client_email = $job->client->email;
//                $ntobackpage = [
//                    'type' => $work->type . '-back',
//                    'client_company_name' =>$client_company_name,
//                    'client_fax' => $client_fax,
//                    'client_email' => $client_email,
//                    'client_name' => $client_name,
//                    'client_address' => $client_address
//                    ];
//
//                $newpageback = new PdfPage();
//                $newpageback->work_order_id= $work->id;
//                $newpageback->type = $work->type . '-back';
//                $newpageback->save();
//
//                $ntobackpage['page_id'] = $newpageback->id;
//                $newpageback->fields = serialize($ntobackpage);
//                $newpageback->save();
                $pdf_pages[] = $acolpage;
                // $pdf_pages[] =  $ntobackpage;
            }

            if ($work->type == 'claim-of-lien') {
                $colpage = [
                    'type' => $work->type,
                    'client_company_name' => $client_company_name,
                    'client_name' => $client_name,
                    'client_address' => $client_address,
                    'client_county' => $client_county,
                    'client_title' => $client_title,
                    'client_phone' => $client_phone,
                    'client_heshe' => $client_heshe,
                    'customer_name' => $customer_name,

                    'nto_date' => $notification_date,
                    'nto_number' => $nto_number,
                    'land_owner_name' => $land_owner_firm_name,
                    'month' => '',
                    'year' => date('Y'),
                    'materials' => $materials,
                    'job_start_date' => $job_start_date,
                    'job_last_date' => $job_last_date,
                    'job_name' => $job_name,
                    'job_address' => $job_address,
                    'job_county' => $job_county,
                    'job_contract_amount' => $job_contract_amount,
                    'interest_amount' => 0,
                    'unpaid_balance' => 0,
                    'deed' => $deed,
                    'folio' => $folio,
                    'noc' => $noc,
                    'project_number' => $project_number,
                    'legal_description' => $legal_description,
                    'parties' => $xparties,
                    'leaseholders' => $xleaseholders,
                ];

                $newpage = new PdfPage();
                $newpage->work_order_id = $work->id;
                $newpage->type = $work->type;
                $newpage->save();

                $colpage['page_id'] = $newpage->id;
                $newpage->fields = serialize($colpage);
                $newpage->save();

//                $client_fax = $job->client->fax;
//                $client_email = $job->client->email;
//                $ntobackpage = [
//                    'type' => $work->type . '-back',
//                    'client_company_name' =>$client_company_name,
//                    'client_fax' => $client_fax,
//                    'client_email' => $client_email,
//                    'client_name' => $client_name,
//                    'client_address' => $client_address
//                    ];
//
//                $newpageback = new PdfPage();
//                $newpageback->work_order_id= $work->id;
//                $newpageback->type = $work->type . '-back';
//                $newpageback->save();
//
//                $ntobackpage['page_id'] = $newpageback->id;
//                $newpageback->fields = serialize($ntobackpage);
//                $newpageback->save();
                $pdf_pages[] = $colpage;
                // $pdf_pages[] =  $ntobackpage;
            }

            if ($work->type == 'conditional-waiver-and-release-of-lien-upon-final-payment') {
                $cwarolufppage = [
                    'type' => $work->type,
                    'client_company_name' => $client_company_name,
                    'client_name' => $client_name,
                    'client_address' => $client_address,
                    'client_county' => $client_county,
                    'client_title' => $client_title,
                    'customer_name' => $customer_name,
                    'client_email' => $client_email,
                    'dated_on' => date('m/d/Y'),
                    'amount' => 0,
                    'land_owner_firm_name' => $land_owner_firm_name,

                    'job_name' => $job_name,
                    'job_address' => $job_address,
                    'job_county' => $job_county,

                ];

                $newpage = new PdfPage();
                $newpage->work_order_id = $work->id;
                $newpage->type = $work->type;
                $newpage->save();

                $cwarolufppage['page_id'] = $newpage->id;
                $newpage->fields = serialize($cwarolufppage);
                $newpage->save();

                $pdf_pages[] = $cwarolufppage;
            }

            if ($work->type == 'conditional-waiver-and-release-of-lien-upon-progress-payment') {
                $cwarolupppage = [
                    'type' => $work->type,
                    'client_company_name' => $client_company_name,
                    'client_name' => $client_name,
                    'client_address' => $client_address,
                    'client_county' => $client_county,
                    'client_title' => $client_title,
                    'customer_name' => $customer_name,
                    'client_email' => $client_email,
                    'dated_on' => date('m/d/Y'),
                    'date_paid' => '',
                    'amount' => 0,
                    'land_owner_firm_name' => $land_owner_firm_name,

                    'job_name' => $job_name,
                    'job_address' => $job_address,
                    'job_county' => $job_county,

                ];

                $newpage = new PdfPage();
                $newpage->work_order_id = $work->id;
                $newpage->type = $work->type;
                $newpage->save();

                $cwarolupppage['page_id'] = $newpage->id;
                $newpage->fields = serialize($cwarolupppage);
                $newpage->save();

                $pdf_pages[] = $cwarolupppage;
            }

            if ($work->type == 'contractors-final-payment-affidavit') {
                $cfpapage = [
                    'type' => $work->type,
                    'client_company_name' => $client_company_name,
                    'client_name' => $client_name,
                    'client_address' => $client_address,
                    'client_county' => $client_county,
                    'client_title' => $client_title,
                    'client_heshe' => $client_heshe,
                    'land_owner_firm_name' => $land_owner_firm_name,
                    'xleaseholders' => $xleaseholders,
                    'unpaid_balance' => 0,
                    'lienors' => [0 => ['name' => '', 'amount' => '']],
                    'client_email' => $client_email,
                    'signed_at' => '',

                ];

                $newpage = new PdfPage();
                $newpage->work_order_id = $work->id;
                $newpage->type = $work->type;
                $newpage->save();

                $cfpapage['page_id'] = $newpage->id;
                $newpage->fields = serialize($cfpapage);
                $newpage->save();

                $pdf_pages[] = $cfpapage;
            }

            if ($work->type == 'notice-of-bond') {
                $nobpage = [
                    'type' => $work->type,
                    'client_company_name' => $client_company_name,
                    'client_name' => $client_name,
                    'client_address' => $client_address,
                    'client_county' => $client_county,
                    'client_title' => $client_title,
                    'client_phone' => $client_phone,

                    'month' => strtolower(date('F')),
                    'year' => date('Y'),
                    'lienor_name' => '',
                    'lien_date' => '',
                    'lienor_address' => '',
                    'field_book_page_number' => 'Book at page',

                    'nto_number' => $nto_number,

                    'job_name' => $job_name,
                    'job_address' => $job_address,
                    'job_county' => $job_county,

                    'legal_description' => $legal_description,

                ];

                $newpage = new PdfPage();
                $newpage->work_order_id = $work->id;
                $newpage->type = $work->type;
                $newpage->save();

                $nobpage['page_id'] = $newpage->id;
                $newpage->fields = serialize($nobpage);
                $newpage->save();

//                $client_fax = $job->client->fax;
//                $client_email = $job->client->email;
//                $ntobackpage = [
//                    'type' => $work->type . '-back',
//                    'client_company_name' =>$client_company_name,
//                    'client_fax' => $client_fax,
//                    'client_email' => $client_email,
//                    'client_name' => $client_name,
//                    'client_address' => $client_address
//                    ];
//
//                $newpageback = new PdfPage();
//                $newpageback->work_order_id= $work->id;
//                $newpageback->type = $work->type . '-back';
//                $newpageback->save();
//
//                $ntobackpage['page_id'] = $newpageback->id;
//                $newpageback->fields = serialize($ntobackpage);
//                $newpageback->save();
                $pdf_pages[] = $nobpage;
                // $pdf_pages[] =  $ntobackpage;
            }

            if ($work->type == 'notice-of-commencement') {
                $nocpage = [
                    'type' => $work->type,
                    'folio' => $folio,
                    'noc' => $noc,
                    'project_number' => $project_number,
                    'improvements' => '',
                    'client_company_name' => $client_company_name,
                    'client_name' => $client_name,
                    'client_address' => $client_address,
                    'client_county' => $client_county,
                    'client_phone' => $client_phone,
                    'job_name' => $job_name,
                    'job_address' => $job_address,
                    'job_county' => $job_county,
                    'land_owner_firm_name' => $land_owner_firm_name,
                    'land_owner_address' => $land_owner_address,
                    'land_owner_name' => $land_owner_firm_name,
                    'gc_firm_name' => $gc_firm_name,
                    'gc_address' => $gc_address,
                    'gc_phone' => $gc_phone,
                    'gc_name' => $gc_name,
                    'bond_firm_name' => $bond_firm_name,
                    'bond_address' => $bond_address,
                    'bond_name' => $bond_name,
                    'bond_phone' => $bond_phone,
                    'bank_firm_name' => $bank_firm_name,
                    'bank_address' => $bank_address,
                    'bank_name' => $bank_name,
                    'bank_phone' => $bank_phone,
                    'copy_firm_name' => $copy_firm_name,
                    'copy_address' => $copy_address,
                    'copy_name' => $copy_name,
                    'copy_phone' => $copy_phone,
                    'copyl_firm_name' => $copyl_firm_name,
                    'copyl_address' => $copyl_address,
                    'copyl_name' => $copyl_name,
                    'copyl_phone' => $copyl_phone,

                ];

                $newpage = new PdfPage();
                $newpage->work_order_id = $work->id;
                $newpage->type = $work->type;
                $newpage->save();

                $nocpage['page_id'] = $newpage->id;
                $newpage->fields = serialize($nocpage);
                $newpage->save();

//                $client_fax = $job->client->fax;
//                $client_email = $job->client->email;
//                $ntobackpage = [
//                    'type' => $work->type . '-back',
//                    'client_company_name' =>$client_company_name,
//                    'client_fax' => $client_fax,
//                    'client_email' => $client_email,
//                    'client_name' => $client_name,
//                    'client_address' => $client_address
//                    ];
//
//                $newpageback = new PdfPage();
//                $newpageback->work_order_id= $work->id;
//                $newpageback->type = $work->type . '-back';
//                $newpageback->save();
//
//                $ntobackpage['page_id'] = $newpageback->id;
//                $newpageback->fields = serialize($ntobackpage);
//                $newpageback->save();
                $pdf_pages[] = $nocpage;
                // $pdf_pages[] =  $ntobackpage;
            }

            if ($work->type == 'notice-to-owner') {
                $ntopage = [
                    'type' => $work->type,
                    'client_company_name' => $client_company_name,
                    'nto_date' => $today,
                    'wo_number' => $wo_number,
                    'land_owner_firm_name' => $land_owner_firm_name,
                    'land_owner_phone' => $land_owner_phone,
                    'land_owner_email' => $land_owner_email,
                    'land_owner_address' => $land_owner_address,
                    'client_name' => $client_name,
                    'materials' => $materials,
                    'job_name' => $job_name,
                    'job_address' => $job_address,
                    'job_county' => $job_county,
                    'deed' => $deed,
                    'folio' => $folio,
                    'noc' => $noc,
                    'project_number' => $project_number,
                    'legal_description' => $legal_description,
                    'parties' => $xparties,
                    'client_phone' => $client_phone,
                    'client_fax' => $client_fax,
                    'client_email' => $client_email,
                    'client_address' => $client_address,
                    'client_title' => $client_title,
                    'customer_name' => $customer_name,
                    'leaseholders' => $xleaseholders,
                ];

                $newpage = new PdfPage();
                $newpage->work_order_id = $work->id;
                $newpage->type = $work->type;
                $newpage->save();

                $ntopage['page_id'] = $newpage->id;
                $newpage->fields = serialize($ntopage);
                $newpage->save();

                $client_fax = $job->client->fax;
                $client_email = $job->client->email;
                $ntobackpage = [
                    'type' => $work->type.'-back',
                    'barcode' => '',
                    'mailing_address' => '',
                    'client_company_name' => $client_company_name,
                    'client_signature' => $client_signature,
                    'client_fax' => $client_fax,
                    'client_phone' => $client_phone,
                    'client_email' => $client_email,
                    'client_name' => $client_name,
                    'client_address' => $client_address,
                ];

                $newpageback = new PdfPage();
                $newpageback->work_order_id = $work->id;
                $newpageback->type = $work->type.'-back';
                $newpageback->save();

                $ntobackpage['page_id'] = $newpageback->id;
                $newpageback->fields = serialize($ntobackpage);
                $newpageback->save();

                $pdf_pages[] = $ntopage;
                $pdf_pages[] = $ntobackpage;
            }

            if ($work->type == 'notice-of-contest-of-claim-against-payment-bond') {
                $nococapbpage = [
                    'type' => $work->type,
                    'client_company_name' => $client_company_name,
                    'client_name' => $client_name,
                    'client_address' => $client_address,
                    'client_county' => $client_county,
                    'client_phone' => $client_phone,
                    'served_date' => '',
                    'notice_date' => '',
                    'dated_on' => date('m/d/Y'),
                    'lienor_company_name' => '',
                    'lienor_address' => '',

                ];

                $newpage = new PdfPage();
                $newpage->work_order_id = $work->id;
                $newpage->type = $work->type;
                $newpage->save();

                $nococapbpage['page_id'] = $newpage->id;
                $newpage->fields = serialize($nococapbpage);
                $newpage->save();

                $pdf_pages[] = $nococapbpage;
            }

            if ($work->type == 'notice-of-contest-of-lien') {
                $nocolpage = [
                    'type' => $work->type,
                    'client_company_name' => $client_company_name,
                    'client_name' => $client_name,
                    'client_address' => $client_address,
                    'client_county' => $client_county,

                    'client_phone' => $client_phone,
                    'lienor_company_name' => '',
                    'lienor_address' => '',

                    'land_owner_firm_name' => $land_owner_firm_name,
                    'land_owner_phone' => $land_owner_phone,
                    'land_owner_email' => $land_owner_email,
                    'land_owner_address' => $land_owner_address,
                    'official_record_book' => 'Book ',
                    'job_county' => $job_county,
                    'lien_date' => '',
                    'dated_on' => date('m/d/Y'),
                ];

                $newpage = new PdfPage();
                $newpage->work_order_id = $work->id;
                $newpage->type = $work->type;
                $newpage->save();

                $nocolpage['page_id'] = $newpage->id;
                $newpage->fields = serialize($nocolpage);
                $newpage->save();

                $pdf_pages[] = $nocolpage;
            }

            if ($work->type == 'notice-of-non-payment') {
                $nonppage = [
                    'type' => $work->type,
                    'job_type' => $job_type,
                    'barcode' => '',
                    'gc_firm_name' => $gc_firm_name,
                    'gc_address' => $gc_address,
                    'job_name' => $job_name,
                    'bond_number' => '',
                    'materials' => $materials,
                    'job_address' => $job_address,
                    'customer_name' => $customer_name,
                    'notice_date' => $notification_date,
                    'unpaid_balance' => 0,
                    'client_company_name' => $client_company_name,
                    'client_address' => $client_address,
                    'client_name' => $client_name,
                    'client_phone' => $client_phone,
                    'client_email' => $client_email,
                    'parties' => $xparties,
                    'dated_on' => date('m/d/Y'),
                    'sureties' => $sureties,
                ];

                $newpage = new PdfPage();
                $newpage->work_order_id = $work->id;
                $newpage->type = $work->type;
                $newpage->save();

                $nonppage['page_id'] = $newpage->id;
                $newpage->fields = serialize($nonppage);
                $newpage->save();

                $pdf_pages[] = $nonppage;
            }

            if ($work->type == 'notice-of-nonpayment-with-intent-to-lien-andor-foreclose') {
                $nonwitlafpage = [
                    'type' => $work->type,
                    'job_type' => $job_type,
                    'barcode' => '',
                    'land_owner_firm_name' => $land_owner_firm_name,
                    'land_owner_address' => $land_owner_address,
                    'job_name' => $job_name,
                    'nto_number' => $nto_number,
                    'materials' => $materials,
                    'job_address' => $job_address,
                    'customer_name' => $customer_name,
                    'unpaid_balance' => 0,
                    'client_company_name' => $client_company_name,
                    'client_address' => $client_address,
                    'client_phone' => $client_phone,
                    'client_name' => $client_name,
                    'parties' => $xparties,
                    'dated_on' => date('m/d/Y'),
                ];

                $newpage = new PdfPage();
                $newpage->work_order_id = $work->id;
                $newpage->type = $work->type;
                $newpage->save();

                $nonwitlafpage['page_id'] = $newpage->id;
                $newpage->fields = serialize($nonwitlafpage);
                $newpage->save();

                $pdf_pages[] = $nonwitlafpage;
            }

            if ($work->type == 'partial-satisfaction-of-lien') {
                $psolpage = [
                    'type' => $work->type,
                    'client_company_name' => $client_company_name,
                    'client_name' => $client_name,
                    'client_address' => $client_address,
                    'client_county' => $client_county,
                    'client_title' => $client_title,
                    'client_phone' => $client_phone,
                    'client_email' => $client_email,
                    'client_heshe' => $client_heshe,
                    'month' => '',
                    'year' => date('Y'),
                    'lien_date' => '',
                    'field_book_page_number' => 'Book',

                    'nto_number' => $nto_number,
                    'land_owner_name' => $land_owner_firm_name,
                    'job_name' => $job_name,
                    'job_address' => $job_address,
                    'job_county' => $job_county,
                    'job_contract_amount' => $job_contract_amount,
                    'legal_description' => $legal_description,
                    'pp_amount' => 0,
                    'pp_outstanding' => 0,
                ];

                $newpage = new PdfPage();
                $newpage->work_order_id = $work->id;
                $newpage->type = $work->type;
                $newpage->save();

                $psolpage['page_id'] = $newpage->id;
                $newpage->fields = serialize($psolpage);
                $newpage->save();

//                $client_fax = $job->client->fax;
//                $client_email = $job->client->email;
//                $ntobackpage = [
//                    'type' => $work->type . '-back',
//                    'client_company_name' =>$client_company_name,
//                    'client_fax' => $client_fax,
//                    'client_email' => $client_email,
//                    'client_name' => $client_name,
//                    'client_address' => $client_address
//                    ];
//
//                $newpageback = new PdfPage();
//                $newpageback->work_order_id= $work->id;
//                $newpageback->type = $work->type . '-back';
//                $newpageback->save();
//
//                $ntobackpage['page_id'] = $newpageback->id;
//                $newpageback->fields = serialize($ntobackpage);
//                $newpageback->save();
                $pdf_pages[] = $psolpage;
                // $pdf_pages[] =  $ntobackpage;
            }

            if ($work->type == 'satisfaction-of-lien') {
                $solpage = [
                    'type' => $work->type,
                    'client_company_name' => $client_company_name,
                    'client_name' => $client_name,
                    'client_address' => $client_address,
                    'client_county' => $client_county,
                    'client_title' => $client_title,
                    'client_phone' => $client_phone,
                    'client_email' => $client_email,
                    'month' => '',
                    'year' => date('Y'),
                    'lien_date' => '',
                    'field_book_page_number' => 'Book at page',

                    'nto_number' => $nto_number,
                    'land_owner_name' => $land_owner_firm_name,
                    'job_name' => $job_name,
                    'job_address' => $job_address,
                    'job_county' => $job_county,
                    'job_contract_amount' => $job_contract_amount,
                    'legal_description' => $legal_description,

                ];

                $newpage = new PdfPage();
                $newpage->work_order_id = $work->id;
                $newpage->type = $work->type;
                $newpage->save();

                $solpage['page_id'] = $newpage->id;
                $newpage->fields = serialize($solpage);
                $newpage->save();

                $pdf_pages[] = $solpage;
            }

            if ($work->type == 'waiver-and-release-of-lien-upon-final-payment') {
                $warolufppage = [
                    'type' => $work->type,
                    'client_company_name' => $client_company_name,
                    'client_name' => $client_name,
                    'client_address' => $client_address,
                    'client_county' => $client_county,
                    'client_title' => $client_title,
                    'customer_name' => $customer_name,
                    'client_email' => $client_email,
                    'dated_on' => date('m/d/Y'),
                    'amount' => 0,
                    'land_owner_firm_name' => $land_owner_firm_name,
                    'nto_number' => $nto_number,
                    'job_name' => $job_name,
                    'job_address' => $job_address,
                    'job_county' => $job_county,

                ];

                $newpage = new PdfPage();
                $newpage->work_order_id = $work->id;
                $newpage->type = $work->type;
                $newpage->save();

                $warolufppage['page_id'] = $newpage->id;
                $newpage->fields = serialize($warolufppage);
                $newpage->save();

                $pdf_pages[] = $warolufppage;
            }

            if ($work->type == 'waiver-and-release-of-lien-upon-progress-payment') {
                $warolupppage = [
                    'type' => $work->type,
                    'client_company_name' => $client_company_name,
                    'client_name' => $client_name,
                    'client_address' => $client_address,
                    'client_county' => $client_county,
                    'client_title' => $client_title,
                    'customer_name' => $customer_name,
                    'client_email' => $client_email,
                    'dated_on' => date('m/d/Y'),
                    'date_paid' => '',
                    'amount' => 0,
                    'land_owner_firm_name' => $land_owner_firm_name,
                    'nto_number' => $nto_number,
                    'job_name' => $job_name,
                    'job_address' => $job_address,
                    'job_county' => $job_county,

                ];

                $newpage = new PdfPage();
                $newpage->work_order_id = $work->id;
                $newpage->type = $work->type;
                $newpage->save();

                $warolupppage['page_id'] = $newpage->id;
                $newpage->fields = serialize($warolupppage);
                $newpage->save();

                $pdf_pages[] = $warolupppage;
            }

            if ($work->type == 'waiver-of-right-to-claim-against-bond-final-payment') {
                $wortcabfppage = [
                    'type' => $work->type,
                    'client_name' => $client_name,
                    'client_email' => $client_email,
                    'client_title' => $client_title,
                    'customer_name' => $customer_name,
                    'dated_on' => date('m/d/Y'),
                    'amount' => 0,
                    'land_owner_firm_name' => $land_owner_firm_name,
                    'nto_number' => $nto_number,
                    'job_name' => $job_name,
                    'job_address' => $job_address,
                    'job_county' => $job_county,

                ];

                $newpage = new PdfPage();
                $newpage->work_order_id = $work->id;
                $newpage->type = $work->type;
                $newpage->save();

                $wortcabfppage['page_id'] = $newpage->id;
                $newpage->fields = serialize($wortcabfppage);
                $newpage->save();

                $pdf_pages[] = $wortcabfppage;
            }

            if ($work->type == 'waiver-of-right-to-claim-against-bond-progress-payment') {
                $wortcabpppage = [
                    'type' => $work->type,
                    'client_name' => $client_name,
                    'client_company_name' => $client_company_name,
                    'client_title' => $client_title,
                    'customer_name' => $customer_name,
                    'waiver_date' => '',
                    'signed_at' => '',
                    'sworn_at' => '',
                    'expiration_date' => '',
                    'dated_on' => date('m/d/Y'),
                    'amount' => 0,
                    'name_notary' => '',
                    'land_owner_firm_name' => $land_owner_firm_name,
                    'nto_number' => $nto_number,
                    'job_name' => $job_name,
                    'job_address' => $job_address,
                    'job_county' => $job_county,

                ];

                $newpage = new PdfPage();
                $newpage->work_order_id = $work->id;
                $newpage->type = $work->type;
                $newpage->save();

                $wortcabpppage['page_id'] = $newpage->id;
                $newpage->fields = serialize($wortcabpppage);
                $newpage->save();

                $pdf_pages[] = $wortcabpppage;
            }
        }

        $data = [
            'work_order_id' => $work->id,
            'pdf_pages' => $pdf_pages,
            'view' => $view,
            'signature' => $signature,
            'backurl' => $backurl,
        ];

        return view('researcher.pdf.document', $data);
    }

    public function update(Request $request, $id)
    {
        $fields = array_except($request->all(), ['_token']);

        try {
            $page = PdfPage::findOrFail($id);
        } catch (ModelNotFoundException $ex) {
            return 'kicked';
        }
        $xfields = $this->replace_on_array(chr(10), '', $fields);
        $xfields = $this->replace_on_array(chr(13), '<br>', $xfields);
        $xfields = serialize($xfields);
        //$xfields = serialize($fields);
        $page->fields = $xfields;
        $page->save();

        if ($fields['type'] == 'amend-claim-of-lien') {
            return view('researcher.pdf.acol', unserialize($page->fields));
        }
        if ($fields['type'] == 'claim-of-lien') {
            return view('researcher.pdf.col', unserialize($page->fields));
        }
        if ($fields['type'] == 'conditional-waiver-and-release-of-lien-upon-final-payment') {
            return view('researcher.pdf.cwarolufp', unserialize($page->fields));
        }

        if ($fields['type'] == 'conditional-waiver-and-release-of-lien-upon-progress-payment') {
            return view('researcher.pdf.cwarolupp', unserialize($page->fields));
        }

        if ($fields['type'] == 'contractors-final-payment-affidavit') {
            return view('researcher.pdf.cfpa', unserialize($page->fields));
        }

        if ($fields['type'] == 'notice-of-bond') {
            return view('researcher.pdf.nob', unserialize($page->fields));
        }
        if ($fields['type'] == 'notice-of-commencement') {
            return view('researcher.pdf.noc', unserialize($page->fields));
        }
        if ($fields['type'] == 'notice-to-owner') {
            return view('researcher.pdf.nto', unserialize($page->fields));
        }
        if ($fields['type'] == 'notice-to-owner-back') {
            return view('researcher.pdf.ntoback', unserialize($page->fields));
        }
        if ($fields['type'] == 'notice-of-contest-of-claim-against-payment-bond') {
            return view('researcher.pdf.nococapb', unserialize($page->fields));
        }

        if ($fields['type'] == 'notice-of-contest-of-lien') {
            return view('researcher.pdf.nocol', unserialize($page->fields));
        }
        if ($fields['type'] == 'notice-of-non-payment') {
            return view('researcher.pdf.nonp', unserialize($page->fields));
        }

        if ($fields['type'] == 'notice-of-nonpayment-with-intent-to-lien-andor-foreclose') {
            return view('researcher.pdf.nonwitlaf', unserialize($page->fields));
        }

        if ($fields['type'] == 'partial-satisfaction-of-lien') {
            return view('researcher.pdf.psol', unserialize($page->fields));
        }
        if ($fields['type'] == 'satisfaction-of-lien') {
            return view('researcher.pdf.sol', unserialize($page->fields));
        }

        if ($fields['type'] == 'waiver-and-release-of-lien-upon-final-payment') {
            return view('researcher.pdf.warolufp', unserialize($page->fields));
        }

        if ($fields['type'] == 'waiver-and-release-of-lien-upon-progress-payment') {
            return view('researcher.pdf.warolupp', unserialize($page->fields));
        }

        if ($fields['type'] == 'waiver-of-right-to-claim-against-bond-final-payment') {
            return view('researcher.pdf.wortcabfp', unserialize($page->fields));
        }

        if ($fields['type'] == 'waiver-of-right-to-claim-against-bond-progress-payment') {
            return view('researcher.pdf.wortcabpp', unserialize($page->fields));
        }
    }

    public function preview(Request $request, $id)
    {
        $wo = WorkOrder::findOrFail($id);

        if ($request->generate == 'preview') {
            foreach ($wo->pdf_pages as $xpage) {
                $data = unserialize($xpage->fields);

                $signature = $wo->job->client->signature;

                $data['signature'] = $signature;
                $data['client_mailing_address'] = $wo->job->client->mailing_address;
                $data['mailing_address'] = 'John Doe<br>1600 Pennsylvania Ave NW<br> Washington DC 20500';
                $data['barcode'] = '42033187291001902222278100001050';
                $data['document'] = 'ntoback';

                if ($xpage->type == 'notice-to-owner') {
                    $data['document'] = 'nto';
                    $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                }

                if ($xpage->type == 'notice-to-owner-back') {
                    $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                    //return $pdf->download();
                }

                if ($xpage->type == 'amend-claim-of-lien') {
                    $data['document'] = 'acol';
                    $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                }

                if ($xpage->type == 'claim-of-lien') {
                    $data['document'] = 'col';
                    $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                }

                if ($xpage->type == 'conditional-waiver-and-release-of-lien-upon-final-payment') {
                    $data['document'] = 'cwarolufp';
                    $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                }

                if ($xpage->type == 'conditional-waiver-and-release-of-lien-upon-progress-payment') {
                    $data['document'] = 'cwarolupp';
                    $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                }

                if ($xpage->type == 'contractors-final-payment-affidavit') {
                    $data['document'] = 'cfpa';
                    $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                }

                if ($xpage->type == 'notice-of-bond') {
                    $data['document'] = 'nob';
                    $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                }

                if ($xpage->type == 'notice-of-commencement') {
                    $data['document'] = 'noc';
                    $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                }

                if ($xpage->type == 'notice-of-contest-of-claim-against-payment-bond') {
                    $data['document'] = 'nococapb';
                    $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                }

                if ($xpage->type == 'notice-of-contest-of-lien') {
                    $data['document'] = 'nocol';
                    $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                }

                if ($xpage->type == 'notice-of-non-payment') {
                    $data['document'] = 'nonp';
                    $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                }

                if ($xpage->type == 'notice-of-nonpayment-with-intent-to-lien-andor-foreclose') {
                    $data['document'] = 'nonwitlaf';
                    $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                }

                if ($xpage->type == 'partial-satisfaction-of-lien') {
                    $data['document'] = 'psol';
                    $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                }

                if ($xpage->type == 'satisfaction-of-lien') {
                    $data['document'] = 'sol';
                    $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                }

                if ($xpage->type == 'waiver-and-release-of-lien-upon-final-payment') {
                    $data['document'] = 'warolufp';
                    $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                }

                if ($xpage->type == 'waiver-and-release-of-lien-upon-progress-payment') {
                    $data['document'] = 'warolupp';
                    $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                }
                if ($xpage->type == 'waiver-of-right-to-claim-against-bond-final-payment') {
                    $data['document'] = 'wortcabfp';
                    $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                }

                if ($xpage->type == 'waiver-of-right-to-claim-against-bond-progress-payment') {
                    $data['document'] = 'wortcabpp';
                    $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                }

                $pdf_pages[] = $pdf->output();
                if (! ($xpage->type == 'notice-to-owner' || $xpage->type == 'notice-to-owner-back')) {
                    $data['document'] = 'allback';
                    $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                    $pdf_pages[] = $pdf->output();
                    unset($pdf);
                }
            }
            unset($pdf);
            $pagecount = 0;

            if (count($pdf_pages) > 0) {
                $pagecount++;
                $m = new Merger();
                foreach ($pdf_pages as $xpdf) {
                    $m->addRaw($xpdf);
                }
            }
            $pdf_file = $m->merge();

            return Response::make($pdf_file, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="preview.pdf"',
            ]);
        }
    }

    public function generate(Request $request, $id)
    {
        $wo = WorkOrder::findOrFail($id);

        $mailing_types = [
            'none' => 'None',
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
            'bond' => 'Bond',
            'landowner' => 'Property Owner',
            'leaseholder' => 'Lease Holder',
            'lender' => 'Lender',
            'copy_recipient' => 'Copy Recipient',
            'sub_contractor' => 'Sub Contractor',
            'sub_sub' => 'Sub-Sub Contractor',

        ];

        $parties_weight = [
            'customer' => 80,
            'landowner' => 70,
            'leaseholder' => 60,
            'general_contractor' => 50,
            'sub_contractor' => 40,
            'sub_sub' => 30,
            'lender' => 20,
            'copy_recipient' => 10,
            'bond' => 0,
        ];

        arsort($parties_weight);

        $all_parties = $wo->job->parties->where('type', '!=', 'client');

        $groupped_parties = $all_parties->groupBy('contact_id');

        $parties = collect([]);

        foreach ($groupped_parties as $gp) {
            $party = $gp[0];
            if (count($gp) == 0) {
                $party_type = $party->type;
            } else {
                //get array of types

                $types = array_flip($gp->map(function ($p) {
                    return collect($p->toArray())
                        ->only(['type'])
                        ->all();
                })->flatten()->toArray());

                $is_gc = false;

                if (array_keys_exists($types, ['customer', 'general_contractor', 'sub_contractor', 'sub_sub'])) {
                    $is_gc = true;
                }

                $xi = array_intersect_key($parties_weight, $types);
                reset($xi);
                $party_type = key($xi);
            }
            $xparty['id'] = $party->id;
            $xparty['contact_id'] = $party->contact_id;
            $xparty['firm_name'] = $party->contact->entity->firm_name;
            $xparty['type'] = $party_type;
            $xparty['bond_type'] = $party->bond_type;
            $xparty['attention_name'] = $party->contact->full_name;
            $xparty['address'] = $party->contact->address_no_country;
            $xparty['email'] = $party->contact->email;
            switch ($party_type) {
                           case 'customer':
                               if ($is_gc) {
                                   $dmt = 'standard-mail';
                               } else {
                                   $dmt = 'certified-nongreen';
                               }
                               if (strlen($xparty['email']) > 0) {
                                   $dmt = 'other-mail';
                               }
                               break;
                           case 'landowner':
                               $dmt = 'certified-nongreen';
                               break;
                           case 'general_contractor':
                               $dmt = 'certified-nongreen';
                               break;
                            case 'bond':
                               $dmt = 'standard-mail';
                               break;
                            case 'client':
                               $dmt = 'none';
                               break;
                           default:
                                $dmt = 'certified-nongreen';
                       }
            $xparty['mailing_type'] = $dmt;
            $parties->push((object) $xparty);
        }

        $data = [
            'work_order' => $wo,
            'mailing_types' => $mailing_types,
            'parties_type' => $parties_type,
            'parties' => $parties,
        ];

        return view('researcher.pdf.mailparties', $data);
    }

    public function save(Request $request, $id)
    {
        $this->validate($request, [
            'email.*' => 'required|email',
        ]);
        $mailing_types = [
            'none' => 'None',
            'standard-mail' => 'Regular Mail',
            'certified-green' => 'Certfied Green RR',
            'certified-nongreen' => 'Certfied Non Green',
            'registered-mail' => 'Registered Mail',
            'express-mail' => 'Express Mail',
            'other-mail' => 'eMail',
        ];

        $work = WorkOrder::findOrFail($id);
        $attachments = Attachment::where('attachable_type', \App\WorkOrder::class)->where('attachable_id', $work->id)->where('type', 'generated')->get();
        foreach ($attachments as $attch) {
            Storage::delete($attch->file_path);
            Storage::delete($attch->thumb_path);
            $attch->delete();
        }
        $recipients = WorkOrderRecipient::where('work_order_id', $id)->whereIN('party_id', $request->party_id)->get();
        foreach ($recipients as $rp) {
            $rp->delete();
        }

        foreach ($request->party_id as $xid) {
            $pdf_pages = [];
            $xmailing_type = $request->mailing_type;
            $xfirm_name = $request->firm_name;
            $xparty_type = $request->party_type;
            $xattention_name = $request->attention_name;
            $xaddress = $request->address;
            $xemails = $request->email;
            if ($request->has('barcode')) {
                $xbarcode = $request->barcode;
            } else {
                $xbarcode = [];
            }
            if ($request->has('return_receipt')) {
                $xreturn_receipt = $request->return_receipt;
            } else {
                $xreturn_receipt = [];
            }
            unset($recipient);

            if ($xmailing_type[$xid] != 'none') {
                $recipient = new WorkOrderRecipient();
                $recipient->work_order_id = $id;
                $recipient->party_id = $xid;
                $recipient->party_type = $xparty_type[$xid];
                $recipient->firm_name = $xfirm_name[$xid];
                $recipient->attention_name = $xattention_name[$xid];
                $recipient->address = $xaddress[$xid];
                $recipient->mailing_type = $xmailing_type[$xid];
                if ($xmailing_type[$xid] == 'other-mail') {
                    $recipient->email = $xemails[$xid];
                }
                $recipient->save();
                if ($xmailing_type[$xid] == 'certified-green' || $xmailing_type[$xid] == 'certified-nongreen') {
                    $xbc = '420';
                    $contact = \App\JobParty::find($xid)->contact;
                    if (strlen($contact->zip) == 0) {
                        $xzip = '00000';
                    } else {
                        if (strlen($contact->zip) > 5) {
                            $xzip = substr($contact->zip, 5);
                        } else {
                            $xzip = $contact->zip;
                        }
                    }
                    $xbc .= $xzip;
                    /// 25 CHARS
                    $xbc = '';

                    ///

                    $xbc .= '92';
                    $mt = MailingType::where('type', $xmailing_type[$xid])->first();
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

                    if (array_key_exists($xid, $xreturn_receipt)) {
                        $recipient->return_receipt = 1;
                    } else {
                        $recipient->return_receipt = 0;
                    }
                }
                $recipient->save();
            }

            if (isset($recipient)) {
                foreach ($work->pdf_pages as $xpage) {
                    $data = unserialize($xpage->fields);

                    $signature = $work->job->client->signature;

                    $data['signature'] = $signature;
                    $data['client_mailing_address'] = $work->job->client->mailing_address;
                    $data['barcode'] = $recipient->barcode;
                    $xaddress = $recipient->firm_name;
                    if ($recipient->attention_name != '') {
                        $xaddress .= '<br />ATTN: '.$recipient->attention_name;
                    }
                    $xaddress .= '<br />'.nl2br($recipient->address);
                    $data['mailing_address'] = $xaddress;

                    if ($xpage->type == 'notice-to-owner') {
                        $data['document'] = 'nto';
                        $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'notice-to-owner-back') {
                        if ($recipient->mailing_type == 'other-mail') {
                        } else {
                            $data['document'] = 'ntoback';
                            $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                        }
                        //return $pdf->download();
                    }

                    if ($xpage->type == 'amend-claim-of-lien') {
                        $data['document'] = 'acol';
                        $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'claim-of-lien') {
                        $data['document'] = 'col';
                        $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'conditional-waiver-and-release-of-lien-upon-final-payment') {
                        $data['document'] = 'cwarolufp';
                        $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'conditional-waiver-and-release-of-lien-upon-progress-payment') {
                        $data['document'] = 'cwarolupp';
                        $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'contractors-final-payment-affidavit') {
                        $data['document'] = 'cfpa';
                        $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'notice-of-bond') {
                        $data['document'] = 'nob';
                        $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'notice-of-commencement') {
                        $data['document'] = 'noc';
                        $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'notice-of-contest-of-claim-against-payment-bond') {
                        $data['document'] = 'nococapb';
                        $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'notice-of-contest-of-lien') {
                        $data['document'] = 'nocol';
                        $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'notice-of-non-payment') {
                        if (strlen($recipient->barcode) > 0) {
                            // $data['barcode'] = 'XXXX XXXX XXXX '.substr($recipient->barcode,-4);;
                            $data['barcode'] = $recipient->barcode;
                        }
                        $data['document'] = 'nonp';
                        $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'notice-of-nonpayment-with-intent-to-lien-andor-foreclose') {
                        if (strlen($recipient->barcode) > 0) {
                            // $data['barcode'] = 'XXXX XXXX XXXX '.substr($recipient->barcode,-4);;
                            $data['barcode'] = $recipient->barcode;
                        }
                        $data['document'] = 'nonwitlaf';
                        $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'partial-satisfaction-of-lien') {
                        $data['document'] = 'psol';
                        $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'satisfaction-of-lien') {
                        $data['document'] = 'sol';
                        $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'waiver-and-release-of-lien-upon-final-payment') {
                        $data['document'] = 'warolufp';
                        $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'waiver-and-release-of-lien-upon-progress-payment') {
                        $data['document'] = 'warolupp';
                        $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                    }
                    if ($xpage->type == 'waiver-of-right-to-claim-against-bond-final-payment') {
                        $data['document'] = 'wortcabfp';
                        $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if ($xpage->type == 'waiver-of-right-to-claim-against-bond-progress-payment') {
                        $data['document'] = 'wortcabpp';
                        $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');
                    }

                    if (isset($pdf)) {
                        $pdf_pages[] = $pdf->output();
                        unset($pdf);
                    }

                    if (! ($xpage->type == 'notice-to-owner' || $xpage->type == 'notice-to-owner-back')) {
                        if ($recipient->mailing_type == 'other-mail') {
                        } else {
                            $data['document'] = 'allback';

                            $pdf = PDF::loadView('researcher.pdf.pdf-document', $data)->setPaper('Letter');

                            $pdf_pages[] = $pdf->output();
                            unset($pdf);
                        }
                    }
                }
            }

            $pagecount = 0;

            if (count($pdf_pages) > 0) {
                //dd(count($pdf_pages));
                $pagecount++;
                $m = new Merger();
                foreach ($pdf_pages as $xpdf) {
                    $m->addRaw($xpdf);
                }

                $xpath = 'attachments/workorders/'.$id.'/pdfs/';
                $file_path = $xpath.'document-'.$id.'-'.$xid.'.pdf';
                $attach_path = $file_path;

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
                $attachment->original_name = 'document-'.$id.'-'.$xid.'.pdf';
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

                if ($recipient->mailing_type == 'other-mail') {
                    Mail::to($recipient)->send(new NoticeDelivery($recipient, $attach_path, $work->job->client->company_name));
                }
            } else {
            }
        }

        //Lets create the invoice if it applies
        $work->fresh();

        $client = $work->job->client;

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
            foreach ($work->recipients as $xrx) {
                $tlines = $template->lines()->where('template_lines.type', $xrx->mailing_type)->get();
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
            }
            //dd($request->all());
        } else {
        }

        $client = $work->job->client;

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
            foreach ($work->recipients as $xrx) {
                $tlines = $template->lines()->where('template_lines.type', $xrx->mailing_type)->get();
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
            }
            //dd($request->all());
        } else {
        }

        //$invoices = $work->invoicesPaid()->get();
        $invoices = $work->invoices()->get();

        $paid_amount = 0;
        foreach ($invoices as $inv) {
            $paid_amount += $inv->PostageLines()->sum('amount');
        }

        if ($paid_amount > 0) {
            $xline['recipient_id'] = 0;
            $xline['description'] = 'PREVIOUS PAYMENT CREDIT';
            $xline['price'] = $paid_amount * -1;
            $xline['quantity'] = 1;
            $xline['mailing_type'] = 'other';
            $new_lines['other-previous-credit'] = $xline;
        }

        session(['mailing.generated.work_id', $id]);
        $data = [
            'client' => $client,
            'work_order_id' => $work->id,
            'work_order_number' => $work->number,
            'recipients' => $work->recipients,
            'new_lines' => $new_lines,
            'mailing_types' => $mailing_types,
        ];

        return view('researcher.pdf.invoice', $data);
    }

    public function replace_on_array($search, $replace, $array)
    {
        $array = array_map(
            function ($str) use ($search, $replace) {
                if (is_array($str)) {
                    return $this->replace_on_array($search, $replace, $str);
                } else {
                    return str_replace($search, $replace, $str);
                }
            }, $array);

        return $array;
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

    public function complete(Request $request)
    {
        if ($request->has('complete')) {
            $work = WorkOrder::findOrFail($request->work_order_id);
            $job = $work->job;
            if ($request->complete == 'yes') {
                $work->status = 'completed';
                $work->save();

                $mailto = [];
                $users = $work->job->client->users;
                foreach ($users as $user) {
                    $mailto[] = $user->email;
                }
                if (count($mailto) > 0) {
                    Mail::to($mailto)->send(new NoticeComplete($work->id, $work->invoicesPending));
                }
            }

            return redirect()->route('jobs.edit', $job->id);
        }
    }
}
