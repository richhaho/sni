<?php

namespace App\Http\Controllers\Admin;

use App\Attachment;
use App\Barcode;
use App\BatchDetail;
use App\CompanySetting;
use App\Http\Controllers\Controller;
use App\Job;
use App\JobParty;
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

        $recipients_ids = $work->recipients()->pluck('id')->toArray();
        $batches_ids = BatchDetail::whereIn('work_order_recipient', $recipients_ids)->orderBy('batch_id')->get()->pluck('batch_id')->toArray();

        $batches_ids = array_values(array_unique($batches_ids));

        //echo json_encode($batches_ids);return;

        if (count($batches_ids) > 0) {
            Session::flash('message', 'One or more of these PDFs have been used in the below batches. You must first delete these batches before you can delete the PDFs. Please Remove Batch number '.json_encode($batches_ids));

            return redirect()->back();
        }

        $attachments = $work->attachments()->where('type', 'generated')->get();
        //$recipients = $work->recipients();
        $recipients = $work->recipients;

        foreach ($work->pdf_pages as $xpage) {
            $xpage->delete();
        }

        foreach ($attachments as $xattachment) {
            $xattachment->delete();
        }

        foreach ($recipients as $xrecipient) {
            $xrecipient->delete();
        }

        Session::flash('message', 'Attachments(PDF) were deleted.');

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

        return view('admin.pdf.document.reset', $data);
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

        $generated_att = 0;
        foreach ($work->attachments as $attach) {
            if ($attach->type == 'generated') {
                $generated_att = 1;
                break;
            }
        }
        if ($generated_att > 0) {
            return redirect()->route('workorders.index');
        }

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

    public function document(Request $request, $id)
    {
        if (session('from')) {
            $backurl = session('from');
        } else {
            $backurl = URL::previous();
        }
        $view = true;
        $work = WorkOrder::findOrFail($id);
        if (! isset($request['fromAttachPDF'])) {
            if (count($work->pdf_pages) > 0) {
                return redirect()->route('workorders.reset.ask', $id)->with(['from' => $backurl]);
            }
        }

        $job = $work->job;

        $wo_nto = $job->workorders->where('type', 'notice-to-owner')->where('status', '!=', 'temporary')->first();

        $landowner = $job->parties->where('type', 'landowner')->first();

        $customer = $job->parties->where('type', 'customer')->first();
        //$client = $job->parties->where('type', 'client')->first();

        $client = $job->client;
        $gc = $job->parties->where('type', 'general_contractor')->first();
        $bond = $job->parties->where('type', 'bond')->first();
        if ($bond) {
            $bond_number = $bond->id;
        } else {
            $bond_number = '';
        }
        $leaseholders = $job->parties->where('type', 'leaseholder');
        $copiers = $job->parties->where('type', 'copy_recipient');
        $copy = $copiers->get(0);
        $copyl = $copiers->get(1);

        //============ For NOC ============
        if ($work->type == 'notice-of-commencement' || $work->type == 'notice-of-termination') {
            $landowners = $job->parties->where('type', 'landowner');

            $OwnerLessee_NameAddress = '';
            $Interest_Property = '';
            $Simple_Titleholder = '';

            if ($customer) {
                foreach ($leaseholders as $lease) {
                    if ($customer->contact->id == $lease->contact->id) {
                        $OwnerLessee_NameAddress = $customer->contact->entity->firm_name.' , '.$customer->contact->address_no_country;
                        $Interest_Property = 'LESSEE';
                    }
                }

                if ($Interest_Property == '') {
                    foreach ($landowners as $landown) {
                        if ($customer->contact->id == $landown->contact->id) {
                            $OwnerLessee_NameAddress = $customer->contact->entity->firm_name.' , '.$customer->contact->address_no_country;
                            $Interest_Property = 'LANDOWNER/FEE SIMPLE TITLEHOLDER';
                        }
                    }
                }

                if ($landowner) {
                    $Simple_Titleholder = $landowner->contact->entity->firm_name.' , '.$landowner->contact->address_no_country;
                }
            }

            $xsureties = $job->parties()->where('type', '=', 'bond')->get();

            $noc_sureties = [];
            foreach ($xsureties as $xt) {
                $noc_sureties[] = [
                    'name_address' => $xt->contact->entity->firm_name.' - Bond #:'.$xt->bond_bookpage_number.' , '.$xt->contact->address_no_country,
                    'phone' => $xt->contact->phone,
                    'amount' => $xt->bond_amount,
                ];
            }

            $xlenders = $job->parties()->where('type', '=', 'lender')->get();

            $noc_lenders = [];
            foreach ($xlenders as $xt) {
                $noc_lenders[] = [
                    'name_address' => $xt->contact->entity->firm_name.' , '.$xt->contact->address_no_country,
                    'phone' => $xt->contact->phone,
                ];
            }

            $xcopiersDesignated = $job->parties()->where('type', 'copy_recipient')->where('copy_type', 'owner designated')->get();

            $copiers_designated = [];
            foreach ($xcopiersDesignated as $xt) {
                $copiers_designated[] = [
                    'name_address' => $xt->contact->entity->firm_name.' , '.$xt->contact->address_no_country,
                    'phone' => $xt->contact->phone,
                ];
            }

            $xothercopiersDesignated = $job->parties()->where('type', 'copy_recipient')->where('copy_type', '!=', 'owner designated')->get();
            $othercopiers_designated = [];
            foreach ($xothercopiersDesignated as $xt) {
                $othercopiers_designated[] = [
                    'name_address' => $xt->contact->entity->firm_name.' , '.$xt->contact->address_no_country,
                    'phone' => $xt->contact->phone,
                ];
            }

            $expiration_date = '';
            $permit = '';

            $answers = \App\WorkOrderAnswers::where('work_order_id', $work->id)->where('deleted_at', null)->get();
            //return json_encode($answers);

            foreach ($answers as $answer) {
                $field = \App\WorkOrderFields::where('id', $answer->work_order_field_id)->first();
                if ($field) {
                    if (strpos($field->field_label, 'expire') || strpos($field->field_label, 'expiration')) {
                        $expiration_date = $answer->answer;
                    }
                    if (strpos($field->field_label, 'permit')) {
                        $permit = $answer->answer;
                    }
                }
            }
        }

        $noc = \App\JobNoc::where('job_id', $job->id)->where('noc_number', $job->noc_number)->first();
        if ($noc) {
            if ($noc->expired_at) {
                $expiration_date = date('m/d/Y', strtotime($noc->expired_at));
            }
        }

        //return $expiration_date;

        //return ($landowner->contact->address_no_country);
        //=================================

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
            //////////////////////////// to prod
            $nto_workonjob = $job->workorders()->where('status', '!=', 'temporary')->where([['type', 'notice-to-owner'], ['deleted_at', null]])->orderBy('updated_at', 'desc')->first();

            if ($nto_workonjob) {
                $nto_last_date = $nto_workonjob->updated_at->format('m/d/Y');
            } else {
                $nto_last_date = $job->updated_at->format('m/d/Y');
            }
            ///////////////////////////////////////////////

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
                //$notification_date = $wo_nto->created_at->format('m/d/Y');
                $notification_date = $wo_nto->updated_at->format('m/d/Y');
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
            if (count($job->changes) > 0) {
                $job_changeorder_amount = $job->changes->sum('amount');
            } else {
                $job_changeorder_amount = 0;
            }

            if (count($job->payments) > 0) {
                $job_payment = $job->payments->sum('amount');
            } else {
                $job_payment = 0;
            }

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
            // $types_x=array();
            // foreach($general_contractors as $pt) {
            //     if (!in_array($pt->contact->entity->firm_name,$types_x)){
            //       $xparties[] = ['type'=> $pt->type, 'full_name'=> $pt->contact->full_name,'company_name'=> $pt->contact->entity->firm_name ];
            //       $types_x[]=$pt->contact->entity->firm_name;
            //     }
            // }

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

            $xparties_temp = [];
            $types_x = [];
            foreach ($xparties as $pt) {
                if (! in_array($pt['company_name'], $types_x)) {
                    $xparties_temp[] = $pt;
                    $types_x[] = $pt['company_name'];
                }
            }
            $xparties = $xparties_temp;

            $xsureties = $job->parties()->where('type', '=', 'bond')->get();

            $sureties = [];
            foreach ($xsureties as $xt) {
                $sureties[] = [
                    'name' => $xt->contact->entity->firm_name.' - Bond #:'.$xt->bond_bookpage_number,
                    'address' => $xt->contact->address_no_country,
                    //'bond_number' => $xt->bond_bookpage_number
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
                    'month' => strtolower(date('F')),
                    'year' => date('Y'),
                    'materials' => $materials,
                    'job_start_date' => $job_start_date,
                    'job_last_date' => $job_last_date,
                    'thru_date' => '',
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
                    'thru_date' => '',
                    'land_owner_name' => $land_owner_firm_name,
                    'month' => strtolower(date('F')),
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
                    'nto_number' => $nto_number,
                    'job_name' => $job_name,
                    'job_address' => $job_address,
                    'job_county' => $job_county,
                    'sworn_signed_at' => '',

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
                    'nto_number' => $nto_number,
                    'job_name' => $job_name,
                    'job_address' => $job_address,
                    'job_county' => $job_county,
                    'sworn_signed_at' => '',

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
                    'improvements' => $materials,
                    'client_company_name' => $client_company_name,
                    'client_name' => $client_name,
                    'client_address' => $client_address,
                    'client_county' => $client_county,
                    'client_title' => $client_title,
                    'client_phone' => $client_phone,
                    'client_heshe' => $client_heshe,
                    'job_name' => $job_name,
                    'job_address' => $job_address,
                    'job_county' => $job_county,

                    'gc_firm_name' => $client_company_name,
                    'gc_address' => $client_address,
                    'gc_phone' => $client_phone,
                    'gc_name' => $client_name,

                    'job_legal_description' => $job_legal_description,
                    'OwnerLessee_NameAddress' => $OwnerLessee_NameAddress,
                    'Interest_Property' => $Interest_Property,
                    'Simple_Titleholder' => $Simple_Titleholder,

                    'expiration_date' => $expiration_date,
                    'permit' => $permit,
                    'noc_sureties' => $noc_sureties,
                    'noc_lenders' => $noc_lenders,
                    'copiers_designated' => $copiers_designated,
                    'othercopiers_designated' => $othercopiers_designated,

                    'land_owner_firm_name' => $land_owner_firm_name,
                    'land_owner_address' => $land_owner_address,
                    'land_owner_name' => $land_owner_firm_name,
                    // 'gc_firm_name' => $gc_firm_name,
                    // 'gc_address' => $gc_address,
                    // 'gc_phone' => $gc_phone,
                    // 'gc_name' => $gc_name,

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

            if ($work->type == 'notice-of-termination') {
                $notpage = [
                    'type' => $work->type,
                    'folio' => $folio,
                    'noc' => $noc,
                    'project_number' => $project_number,
                    'improvements' => $materials,
                    'client_company_name' => $client_company_name,
                    'client_name' => $client_name,
                    'client_address' => $client_address,
                    'client_county' => $client_county,
                    'client_phone' => $client_phone,
                    'job_name' => $job_name,
                    'job_address' => $job_address,
                    'job_county' => $job_county,

                    'book_page' => 'Book ____ at Page ___ ',
                    'noc_recorded' => '',

                    'gc_firm_name' => $client_company_name,
                    'gc_address' => $client_address,
                    'gc_phone' => $client_phone,
                    'gc_name' => $client_name,

                    'job_legal_description' => $job_legal_description,
                    'OwnerLessee_NameAddress' => $OwnerLessee_NameAddress,
                    'Interest_Property' => $Interest_Property,
                    'Simple_Titleholder' => $Simple_Titleholder,

                    'expiration_date' => $expiration_date,
                    'termination_date' => '',
                    'exempt_real_property' => '',

                    'permit' => $permit,
                    'noc_sureties' => $noc_sureties,
                    'noc_lenders' => $noc_lenders,
                    'copiers_designated' => $copiers_designated,
                    'othercopiers_designated' => $othercopiers_designated,

                    'land_owner_firm_name' => $land_owner_firm_name,
                    'land_owner_address' => $land_owner_address,
                    'land_owner_name' => $land_owner_firm_name,

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

                $notpage['page_id'] = $newpage->id;
                $newpage->fields = serialize($notpage);
                $newpage->save();

                $pdf_pages[] = $notpage;
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
                    'nto_number' => $nto_number,
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
                    'barcode_id' => '',
                    'barcode_key' => '',
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
                    'wo_number' => $work->id,
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

            if ($work->type == 'amended-notice-to-owner') {
                if (isset($wo_nto->id)) {
                    $wo_nto_number = $wo_nto->id;
                } else {
                    $wo_nto_number = '';
                }
                $antopage = [
                    'type' => $work->type,
                    'client_company_name' => $client_company_name,
                    'nto_date' => $today,
                    'wo_number' => $wo_number,
                    'wo_nto_number' => $wo_nto_number,
                    'land_owner_firm_name' => $land_owner_firm_name,
                    'land_owner_phone' => $land_owner_phone,
                    'land_owner_email' => $land_owner_email,
                    'land_owner_address' => $land_owner_address,
                    'client_name' => $client_name,
                    'materials' => $materials,
                    'nto_number' => $nto_number,
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
                    'barcode_id' => '',
                    'barcode_key' => '',
                ];

                $newpage = new PdfPage();
                $newpage->work_order_id = $work->id;
                $newpage->type = $work->type;
                $newpage->save();

                $antopage['page_id'] = $newpage->id;
                $newpage->fields = serialize($antopage);
                $newpage->save();

                $client_fax = $job->client->fax;
                $client_email = $job->client->email;
                $antobackpage = [
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
                    'wo_number' => $work->id,
                ];

                $newpageback = new PdfPage();
                $newpageback->work_order_id = $work->id;
                $newpageback->type = $work->type.'-back';
                $newpageback->save();

                $antobackpage['page_id'] = $newpageback->id;
                $newpageback->fields = serialize($antobackpage);
                $newpageback->save();

                $pdf_pages[] = $antopage;
                $pdf_pages[] = $antobackpage;
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
                $client_gender = $job->client->gender;
                $nonppage = [
                    'type' => $work->type,
                    'job_type' => $job_type,
                    'barcode' => '',
                    'gc_firm_name' => $gc_firm_name,
                    'gc_address' => $gc_address,
                    'job_name' => $job_name,
                    'nto_number' => $nto_number,
                    'bond_number' => $bond_number,
                    'materials' => $materials,
                    'job_address' => $job_address,
                    'customer_name' => $customer_name,
                    'force_date' => date('m/d/Y', strtotime('+2weeks')),
                    'unpaid_balance' => 0,
                    'client_company_name' => $client_company_name,
                    'client_address' => $client_address,
                    'client_name' => $client_name,
                    'client_gender' => $client_gender,
                    'client_phone' => $client_phone,
                    'client_email' => $client_email,
                    'parties' => $xparties,
                    'dated_on' => date('m/d/Y'),
                    'sureties' => $sureties,
                    'additional_text' => '',
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
            if ($work->type == 'notice-of-nonpayment-for-bonded-private-jobs-statutes-713') {
                $client_gender = $job->client->gender;
                $nonp713page = [
                    'type' => $work->type,
                    'job_type' => $job_type,
                    'barcode' => '',
                    'gc_firm_name' => $gc_firm_name,
                    'gc_address' => $gc_address,
                    'sureties' => $sureties,
                    'bond_number' => $bond_number,
                    'materials' => $materials,
                    'nto_number' => $nto_number,
                    'job_name' => $job_name,
                    'job_address' => $job_address,
                    'noc' => $noc,
                    'amount_due' => 0,
                    'amount_paid' => 0,
                    'retainage' => 0,
                    'interest_amount_due' => 0,
                    'last_day_on_job' => $job_last_date,
                    'continue_furnishing' => 'Yes',
                    'expected_amount_due' => '',
                    'include_soa_line_items' => 'Yes',
                    'notice_date' => $notification_date,
                    'certified_number' => '',
                    'dated_on' => date('m/d/Y'),
                    'sworn_at' => '',
                    'client_name' => $client_name,
                    'client_title' => $client_title,
                    'client_company_name' => $client_company_name,
                    'client_address' => $client_address,
                    'client_gender' => $client_gender,
                    'client_phone' => $client_phone,
                    'client_email' => $client_email,
                    'parties' => $xparties,
                    'customer_working_for_contract' => 'Yes',
                    'documents_list' => [],
                    'total_contract_amount' => $job_contract_amount,
                    'total_changeorder_amount' => $job_changeorder_amount,
                    'total_invoice_amount' => 0,
                    'total_paid_date' => $job_payment,
                    'has_recorded_col' => 'Yes',
                    'has_amended' => null,
                ];

                $newpage = new PdfPage();
                $newpage->work_order_id = $work->id;
                $newpage->type = $work->type;
                $newpage->save();
                $nonp713page['page_id'] = $newpage->id;
                $newpage->fields = serialize($nonp713page);
                $newpage->save();
                $pdf_pages[] = $nonp713page;
            }
            if ($work->type == 'notice-of-nonpayment-for-government-jobs-statutes-255') {
                $client_gender = $job->client->gender;
                $nonp255page = [
                    'type' => $work->type,
                    'job_type' => $job_type,
                    'barcode' => '',
                    'gc_firm_name' => $gc_firm_name,
                    'gc_address' => $gc_address,
                    'sureties' => $sureties,
                    'bond_number' => $bond_number,
                    'materials' => $materials,
                    'nto_number' => $nto_number,
                    'job_name' => $job_name,
                    'job_address' => $job_address,
                    'project_number' => $project_number,
                    'amount_due' => 0,
                    'amount_paid' => 0,
                    'retainage' => 0,
                    'interest_amount_due' => 0,
                    'last_day_on_job' => $job_last_date,
                    'continue_furnishing' => 'Yes',
                    'expected_amount_due' => '',
                    'include_soa_line_items' => 'Yes',
                    'notice_date' => $notification_date,
                    'certified_number' => '',
                    'dated_on' => date('m/d/Y'),
                    'sworn_at' => '',
                    'client_name' => $client_name,
                    'client_title' => $client_title,
                    'client_company_name' => $client_company_name,
                    'client_address' => $client_address,
                    'client_gender' => $client_gender,
                    'client_phone' => $client_phone,
                    'client_email' => $client_email,
                    'parties' => $xparties,
                    'customer_working_for_contract' => 'Yes',
                    'documents_list' => [],
                    'total_contract_amount' => $job_contract_amount,
                    'total_changeorder_amount' => $job_changeorder_amount,
                    'total_invoice_amount' => 0,
                    'total_paid_date' => $job_payment,
                    'has_amended' => null,
                ];

                $newpage = new PdfPage();
                $newpage->work_order_id = $work->id;
                $newpage->type = $work->type;
                $newpage->save();
                $nonp255page['page_id'] = $newpage->id;
                $newpage->fields = serialize($nonp255page);
                $newpage->save();
                $pdf_pages[] = $nonp255page;
            }

            if ($work->type == 'out-of-state-nto-preliminary-notice-of-lien-rights') {
                $pnolrpage = [
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

                $pnolrpage['page_id'] = $newpage->id;
                $newpage->fields = serialize($pnolrpage);
                $newpage->save();

                $pdf_pages[] = $pnolrpage;
            }

            if ($work->type == 'rescission-letter') {
                if (isset($wo_nto->number)) {
                    $wo_nto_number = $wo_nto->number;
                } else {
                    $wo_nto_number = '';
                }
                $rlpage = [
                    'type' => $work->type,
                    'job_type' => $job_type,
                    'barcode' => '',
                    'land_owner_firm_name' => $land_owner_firm_name,
                    'land_owner_address' => $land_owner_address,
                    'job_name' => $job_name,
                    'nto_number' => $wo_nto_number,
                    'nto_date' => $nto_date,
                    'text_content' => 'Appears it was sent to you in error.',
                    'job_address' => $job_address,
                    'customer_name' => $customer_name,
                    'unpaid_balance' => 0,
                    'client_company_name' => $client_company_name,
                    'client_address' => $client_address,
                    'client_phone' => $client_phone,
                    'client_name' => $client_name,
                    'parties' => $xparties,
                    'dated_on' => date('m/d/Y'),

                    'nto_last_date' => $nto_last_date, //to prod
                ];

                $newpage = new PdfPage();
                $newpage->work_order_id = $work->id;
                $newpage->type = $work->type;
                $newpage->save();

                $rlpage['page_id'] = $newpage->id;
                $newpage->fields = serialize($rlpage);
                $newpage->save();

                $pdf_pages[] = $rlpage;
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
            $months = ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'];

            $month = $months[date('m') - 1];
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
                    'month' => $month,
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
                    'month' => $month,
                    'year' => date('Y'),
                    'lien_date' => '',
                    'thru_date' => '',
                    'job_last_date' => $job_last_date,
                    'field_book_page_number' => 'Book at page',

                    'nto_number' => $nto_number,
                    'land_owner_name' => $land_owner_firm_name,
                    'job_name' => $job_name,
                    'job_address' => $job_address,
                    'job_county' => $job_county,
                    'job_contract_amount' => $job_contract_amount,
                    'legal_description' => $legal_description,
                    'land_owner_name' => $land_owner_firm_name,

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
            if ($work->type == 'sworn-statement-of-account') {
                $ssoapage = [
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
                    'land_owner_address' => $land_owner_address,
                    'land_owner_email' => $land_owner_email,
                    'nto_number' => $nto_number,
                    'job_name' => $job_name,
                    'job_address' => $job_address,
                    'job_county' => $job_county,
                    'sworn_signed_at' => '',
                    'demand_date' => '',

                    'services_performed' => $materials,
                    'total_contract_amount' => $job_contract_amount,
                    'total_changeorder_amount' => $job_changeorder_amount,

                    'total_invoice_amount' => 0,
                    'total_paid_date' => $job_payment,
                    'construction_text' => '',

                ];

                $newpage = new PdfPage();
                $newpage->work_order_id = $work->id;
                $newpage->type = $work->type;
                $newpage->save();

                $ssoapage['page_id'] = $newpage->id;
                $newpage->fields = serialize($ssoapage);
                $newpage->save();

                $pdf_pages[] = $ssoapage;
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
                    'sworn_signed_at' => '',

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
                    'sworn_signed_at' => '',

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
                    'sworn_at' => '',

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

        return view('admin.pdf.document', $data);
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
            return view('admin.pdf.acol', unserialize($page->fields));
        }
        if ($fields['type'] == 'amended-notice-to-owner') {
            return view('admin.pdf.anto', unserialize($page->fields));
        }
        if ($fields['type'] == 'amended-notice-to-owner-back') {
            return view('admin.pdf.antoback', unserialize($page->fields));
        }

        if ($fields['type'] == 'claim-of-lien') {
            return view('admin.pdf.col', unserialize($page->fields));
        }
        if ($fields['type'] == 'conditional-waiver-and-release-of-lien-upon-final-payment') {
            return view('admin.pdf.cwarolufp', unserialize($page->fields));
        }

        if ($fields['type'] == 'conditional-waiver-and-release-of-lien-upon-progress-payment') {
            return view('admin.pdf.cwarolupp', unserialize($page->fields));
        }

        if ($fields['type'] == 'contractors-final-payment-affidavit') {
            return view('admin.pdf.cfpa', unserialize($page->fields));
        }

        if ($fields['type'] == 'notice-of-bond') {
            return view('admin.pdf.nob', unserialize($page->fields));
        }
        if ($fields['type'] == 'notice-of-commencement') {
            //return json_encode($page->fields);
            return view('admin.pdf.noc', unserialize($page->fields));
        }
        if ($fields['type'] == 'notice-of-termination') {
            return view('admin.pdf.not', unserialize($page->fields));
        }
        if ($fields['type'] == 'notice-to-owner') {
            return view('admin.pdf.nto', unserialize($page->fields));
        }
        if ($fields['type'] == 'notice-to-owner-back') {
            return view('admin.pdf.ntoback', unserialize($page->fields));
        }
        if ($fields['type'] == 'notice-of-contest-of-claim-against-payment-bond') {
            return view('admin.pdf.nococapb', unserialize($page->fields));
        }

        if ($fields['type'] == 'notice-of-contest-of-lien') {
            return view('admin.pdf.nocol', unserialize($page->fields));
        }
        if ($fields['type'] == 'notice-of-non-payment') {
            return view('admin.pdf.nonp', unserialize($page->fields));
        }
        if ($fields['type'] == 'notice-of-nonpayment-for-bonded-private-jobs-statutes-713') {
            return view('admin.pdf.nonp713', unserialize($page->fields));
        }
        if ($fields['type'] == 'notice-of-nonpayment-for-government-jobs-statutes-255') {
            return view('admin.pdf.nonp255', unserialize($page->fields));
        }
        if ($fields['type'] == 'out-of-state-nto-preliminary-notice-of-lien-rights') {
            return view('admin.pdf.pnolr', unserialize($page->fields));
        }
        if ($fields['type'] == 'rescission-letter') {
            return view('admin.pdf.rl', unserialize($page->fields));
        }

        if ($fields['type'] == 'notice-of-nonpayment-with-intent-to-lien-andor-foreclose') {
            return view('admin.pdf.nonwitlaf', unserialize($page->fields));
        }

        if ($fields['type'] == 'partial-satisfaction-of-lien') {
            return view('admin.pdf.psol', unserialize($page->fields));
        }
        if ($fields['type'] == 'satisfaction-of-lien') {
            return view('admin.pdf.sol', unserialize($page->fields));
        }
        if ($fields['type'] == 'sworn-statement-of-account') {
            return view('admin.pdf.ssoa', unserialize($page->fields));
        }

        if ($fields['type'] == 'waiver-and-release-of-lien-upon-final-payment') {
            return view('admin.pdf.warolufp', unserialize($page->fields));
        }

        if ($fields['type'] == 'waiver-and-release-of-lien-upon-progress-payment') {
            return view('admin.pdf.warolupp', unserialize($page->fields));
        }

        if ($fields['type'] == 'waiver-of-right-to-claim-against-bond-final-payment') {
            return view('admin.pdf.wortcabfp', unserialize($page->fields));
        }

        if ($fields['type'] == 'waiver-of-right-to-claim-against-bond-progress-payment') {
            return view('admin.pdf.wortcabpp', unserialize($page->fields));
        }
    }

    public function preview(Request $request, $id)
    {
        $wo = WorkOrder::findOrFail($id);
        $address_type = $wo->job->client->return_address_type;
        $sni_address = nl2br(CompanySetting::first()->name."\n".CompanySetting::first()->address);

        if ($request->generate == 'preview') {
            foreach ($wo->pdf_pages as $xpage) {
                $data = unserialize($xpage->fields);

                $signature = $wo->job->client->signature;

                $data['signature'] = $signature;

                $data['client_mailing_address'] = ($address_type == 'sni') ? $sni_address : $wo->job->client->mailing_address;
                $data['mailing_address'] = 'John Doe<br>1600 Pennsylvania Ave NW<br> Washington DC 20500';
                $data['barcode'] = '9207190222227810003294';
                $data['document'] = 'ntoback';
                $data['wo_number'] = $wo->id;
                if ($xpage->type == 'notice-to-owner') {
                    $data['barcode_id'] = '0000001234';
                    $data['barcode_key'] = '0012345678';
                    $data['document'] = 'nto';
                    $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                }

                if ($xpage->type == 'notice-to-owner-back') {
                    // $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    continue;
                }

                if ($xpage->type == 'amended-notice-to-owner') {
                    $data['barcode_id'] = '0000001234';
                    $data['barcode_key'] = '0012345678';
                    $data['document'] = 'anto';
                    $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                }

                if ($xpage->type == 'amended-notice-to-owner-back') {
                    // $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                    continue;
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
                    $data['document'] = 'nonp';
                    $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                }
                if ($xpage->type == 'notice-of-nonpayment-for-bonded-private-jobs-statutes-713') {
                    $data['document'] = 'nonp713';
                    $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                }
                if ($xpage->type == 'notice-of-nonpayment-for-government-jobs-statutes-255') {
                    $data['document'] = 'nonp255';
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

                if ($xpage->type == 'notice-of-nonpayment-with-intent-to-lien-andor-foreclose') {
                    $data['document'] = 'nonwitlaf';
                    $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                }

                if ($xpage->type == 'partial-satisfaction-of-lien') {
                    $data['document'] = 'psol';
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

                $pdf_pages[] = $pdf->output();
                // if (!($xpage->type == 'notice-to-owner' || $xpage->type == 'notice-to-owner-back')) {
                //     $data['document'] = 'allback';
                //     $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                //     $pdf_pages[] = $pdf->output();
                //     unset($pdf);
                // }
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

    public function AttachPDF(Request $request, $id)
    {
        $wo = WorkOrder::findOrFail($id);
        /////////////////////////////////////////////////////////
        if ($request['loading'] == 'start') {
            $attachment_file = $wo->attachments->where('attachable_type', \App\WorkOrder::class)->where('file_mime', 'application/pdf');
            $attachment_files = [];
            foreach ($attachment_file as $att_file) {
                if (substr($att_file->file_path, -3) != 'pdf') {
                    continue;
                }
                if (! Storage::disk()->exists($att_file->file_path)) {
                    continue;
                }
                $pos = strripos($att_file->file_path, '/');
                $len = strlen($att_file->file_path);
                //$filepathname=substr($att_file->file_path, $pos-$len+1);
                $filepathname = $att_file->original_name;

                $attachment_files[] = [
                    $att_file->file_path => str_replace('App\\', '', $att_file->attachable_type).':'.str_replace('AUTOMATICALLY GENERATED', '', $att_file->description).':'.$filepathname.':'.$att_file->type,
                ];
            }

            $attachment_file = $wo->job->attachments->where('attachable_type', \App\Job::class)->where('file_mime', 'application/pdf');
            foreach ($attachment_file as $att_file) {
                if (substr($att_file->file_path, -3) != 'pdf') {
                    continue;
                }
                if (! Storage::disk()->exists($att_file->file_path)) {
                    continue;
                }
                $pos = strripos($att_file->file_path, '/');
                $len = strlen($att_file->file_path);
                //$filepathname=substr($att_file->file_path, $pos-$len+1);
                $filepathname = $att_file->original_name;

                $attachment_files[] = [
                    $att_file->file_path => str_replace('App\\', '', $att_file->attachable_type).':'.str_replace('AUTOMATICALLY GENERATED', '', $att_file->description).':'.$filepathname.':'.$att_file->type,
                ];
            }

            $bond_file = $wo->job->parties->where('type', 'bond')->where('bond_pdf', '!=', null);
            foreach ($bond_file as $att_file) {
                if (substr($att_file->bond_pdf, -3) != 'pdf') {
                    continue;
                }
                if (! Storage::disk()->exists($att_file->bond_pdf)) {
                    continue;
                }
                $pos = strripos($att_file->bond_pdf, '/');
                $len = strlen($att_file->bond_pdf);
                $filepathname = substr($att_file->bond_pdf, $pos - $len + 1);

                $attachment_files[] = [
                    $att_file->bond_pdf => $att_file->type.'::'.$filepathname,
                ];
            }

            $data = [
                'work_order' => $wo,
                'id' => $id,
                'attachment_files' => $attachment_files,
            ];

            return view('admin.pdf.attachpdf', $data);
        }
        //////////////////////////////////////////////////////////
        if ($request['attach_method'] == 'upload') {
            if ($request['file'] == null || $request['file'] == '') {
                Session::flash('message', 'file is required.');
                // $attachment_file=Attachment::where('attachable_type','App\WorkOrder')->orwhere('attachable_type','App\Job')->where('file_mime','application/pdf')->get();
                // $attachment_files=array();
                // foreach ($attachment_file as $att_file) {
                //   if (substr($att_file->file_path,-3)!='pdf') continue;
                //   if(!Storage::disk()->exists($att_file->file_path)) continue;
                //   $pos=strripos($att_file->file_path,'/');
                //   $len=strlen($att_file->file_path);
                //   $filepathname=substr($att_file->file_path, $pos-$len+1);

                //   $attachment_files[]=[
                //     $att_file->file_path=>str_replace('App\\','',$att_file->attachable_type).':'.str_replace('AUTOMATICALLY GENERATED','',$att_file->description).':'.$filepathname
                //   ];
                // }
                // $bond_file=JobParty::where('type','bond')->where('bond_pdf','!=',null)->get();
                // foreach ($bond_file as $att_file) {
                //   if (substr($att_file->bond_pdf,-3)!='pdf') continue;
                //   if(!Storage::disk()->exists($att_file->bond_pdf)) continue;
                //   $pos=strripos($att_file->bond_pdf,'/');
                //   $len=strlen($att_file->bond_pdf);
                //   $filepathname=substr($att_file->bond_pdf, $pos-$len+1);

                //   $attachment_files[]=[
                //     $att_file->bond_pdf=>$att_file->type.'::'.$filepathname
                //   ];
                // }
                $attachment_file = $wo->attachments->where('attachable_type', \App\WorkOrder::class)->where('file_mime', 'application/pdf');
                $attachment_files = [];
                foreach ($attachment_file as $att_file) {
                    if (substr($att_file->file_path, -3) != 'pdf') {
                        continue;
                    }
                    if (! Storage::disk()->exists($att_file->file_path)) {
                        continue;
                    }
                    $pos = strripos($att_file->file_path, '/');
                    $len = strlen($att_file->file_path);
                    //$filepathname=substr($att_file->file_path, $pos-$len+1);
                    $filepathname = $att_file->original_name;

                    $attachment_files[] = [
                        $att_file->file_path => str_replace('App\\', '', $att_file->attachable_type).':'.str_replace('AUTOMATICALLY GENERATED', '', $att_file->description).':'.$filepathname.':'.$att_file->type,
                    ];
                }

                $attachment_file = $wo->job->attachments->where('attachable_type', \App\Job::class)->where('file_mime', 'application/pdf');
                foreach ($attachment_file as $att_file) {
                    if (substr($att_file->file_path, -3) != 'pdf') {
                        continue;
                    }
                    if (! Storage::disk()->exists($att_file->file_path)) {
                        continue;
                    }
                    $pos = strripos($att_file->file_path, '/');
                    $len = strlen($att_file->file_path);
                    //$filepathname=substr($att_file->file_path, $pos-$len+1);
                    $filepathname = $att_file->original_name;

                    $attachment_files[] = [
                        $att_file->file_path => str_replace('App\\', '', $att_file->attachable_type).':'.str_replace('AUTOMATICALLY GENERATED', '', $att_file->description).':'.$filepathname.':'.$att_file->type,
                    ];
                }

                $bond_file = $wo->job->parties->where('type', 'bond')->where('bond_pdf', '!=', null);
                foreach ($bond_file as $att_file) {
                    if (substr($att_file->bond_pdf, -3) != 'pdf') {
                        continue;
                    }
                    if (! Storage::disk()->exists($att_file->bond_pdf)) {
                        continue;
                    }
                    $pos = strripos($att_file->bond_pdf, '/');
                    $len = strlen($att_file->bond_pdf);
                    $filepathname = substr($att_file->bond_pdf, $pos - $len + 1);

                    $attachment_files[] = [
                        $att_file->bond_pdf => $att_file->type.'::'.$filepathname,
                    ];
                }
                $data = [
                    'work_order' => $wo,
                    'id' => $id,
                    'attachment_files' => $attachment_files,
                ];

                return view('admin.pdf.attachpdf', $data);
            }

            $f = $request->file('file');
            $xfilename = 'uploaded_temp.'.$f->guessExtension();
            $xpath = 'attachments/temp/';
            $f->storeAs($xpath, $xfilename);

            $attachedFile = $xpath.$xfilename;
            if ($request['placeType']) {
                $PlaceTpye = 'Use in Place of Notice';
            } else {
                $PlaceTpye = 'Place Behind Notice';
            }
        }
        /////////////////////////////////////////////////////////////////
        if ($request['attach_method'] == 'from_attachment') {
            if ($request['placeType']) {
                $PlaceTpye = 'Use in Place of Notice';
            } else {
                $PlaceTpye = 'Place Behind Notice';
            }
            $attachedFile = $request['file_path'];
        }

        ///////////////////////////////////////////////////////////////////////
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
            'attachedFile' => $attachedFile,
            'PlaceTpye' => $PlaceTpye,

        ];

        return view('admin.pdf.mailparties', $data);
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
                               if ($party->bond_type == 'company') {
                                   $dmt = 'certified-nongreen';
                               }
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

        return view('admin.pdf.mailparties', $data);
    }

    public function save(Request $request, $id)
    {

        // try {
        //   $work = WorkOrder::findOrFail($id);
        // } catch (ModelNotFoundException $ex) {
        //   return 'kicked';
        // }
        // if ($request['kickout']) {return 'unkicked';};
        //////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////
        if ($request['attachedFile']) {
            // $this->validate($request,[
//             'email.*' => 'required|email'
//         ]);

            if ($request['PlaceTpye'] == 'Use in Place of Notice') {
                $mailing_types = [
                    'none' => 'None',
                    'standard-mail' => 'Regular Mail',
                    'certified-green' => 'Certfied Green RR',
                    'certified-nongreen' => 'Certfied Non Green',
                    'registered-mail' => 'Registered Mail',
                    'express-mail' => 'Express Mail',
                    'other-mail' => 'eMail',
                ];
                $max_page_length = 0;
                $work = WorkOrder::findOrFail($id);

                $attachments = Attachment::where('attachable_type', \App\WorkOrder::class)->where('attachable_id', $work->id)->where('type', 'generated')->get();
                foreach ($attachments as $attch) {
                    Storage::delete($attch->file_path);
                    Storage::delete($attch->thumb_path);
                    $attch->delete();
                }
                //$recipients =  WorkOrderRecipient::where('work_order_id',$id)->whereIN('party_id',$request->party_id)->get();
                $recipients = WorkOrderRecipient::where('work_order_id', $id)->get();
                foreach ($recipients as $rp) {
                    $rp->delete();
                }

                foreach ($request->party_id as $xid) {
                    $pdf_pages = [];
                    $xmailing_type = $request->mailing_type;
                    $xmailing_number = $request->mailing_number;
                    $xfirm_name = $request->firm_name;
                    $xparty_type = $request->party_type;
                    $xattention_name = $request->attention_name;
                    $xaddress = $request->address;
                    $xemails = $request->email;
                    $xalso_emails = $request->has('also_email') ? $request->also_email : [];
                    $xalso_emails_value = $request->has('also_email_value') ? $request->also_email_value : [];
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
                        } elseif (isset($xalso_emails[$xid])) {
                            $recipient->email = $xalso_emails_value[$xid];
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
                            $mid = $xbc;
                            $xbc .= $serial;
                            $xbc .= $this->LhunMod10($xbc);

                            $barcode = $this->storeBarcode($xbc, $mid, $serial);
                            $recipient->barcode = $barcode;

                            if (array_key_exists($xid, $xreturn_receipt)) {
                                $recipient->return_receipt = 1;
                            } else {
                                $recipient->return_receipt = 0;
                            }
                        }
                        if ($xmailing_type[$xid] == 'registered-mail' || $xmailing_type[$xid] == 'express-mail') {
                            $recipient->mailing_number = $xmailing_number[$xid];
                        }
                        $recipient->save();
                    }

                    if (isset($recipient)) {
                        foreach ($work->pdf_pages as $xpage) {
                            $address_type = $work->job->client->return_address_type;
                            $sni_address = nl2br(CompanySetting::first()->name." \n ".CompanySetting::first()->address);

                            $data = unserialize($xpage->fields);

                            $signature = $work->job->client->signature;

                            $data['signature'] = $signature;
                            $data['source'] = $recipient->party->source;
                            $data['client_mailing_address'] = ($address_type == 'sni') ? $sni_address : $work->job->client->mailing_address;
                            $data['barcode'] = $recipient->barcode;
                            $data['mailing_type'] = $recipient->mailing_type;
                            $data['mailing_number'] = $recipient->mailing_number;
                            $xaddress = $recipient->firm_name;
                            if ($recipient->attention_name != '') {
                                $xaddress .= '<br />ATTN: '.$recipient->attention_name;
                            }
                            $xaddress .= '<br />'.nl2br($recipient->address);
                            $data['mailing_address'] = $xaddress;
                            $data['wo_number'] = $work->id;

                            if ($xpage->type == 'notice-to-owner-back') {
                                if ($recipient->mailing_type == 'other-mail') {
                                } else {
                                    $data['document'] = 'ntoback';
                                    $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                                }
                                //return $pdf->download();
                            }
                            if ($xpage->type == 'amended-notice-to-owner-back') {
                                if ($recipient->mailing_type == 'other-mail') {
                                } else {
                                    $data['document'] = 'antoback';
                                    $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                                }
                                //return $pdf->download();
                            }

                            if (! ($xpage->type == 'notice-to-owner' || $xpage->type == 'notice-to-owner-back' || $xpage->type == 'amended-notice-to-owner' || $xpage->type == 'amended-notice-to-owner-back')) {
                                if ($recipient->mailing_type == 'other-mail') {
                                } else {
                                    $data['document'] = 'allback';

                                    $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                                }
                            }
                            if (isset($pdf)) {
                                $pdf_pages[] = $pdf->output();
                                unset($pdf);
                            }
                        }
                        //}

                        $pagecount = 0;
                        $existPDF = Storage::get($request['attachedFile']);
                        $m = new Merger();
                        $m->addRaw($existPDF);
                        if (count($pdf_pages) > 0) {
                            $pagecount++;

                            foreach ($pdf_pages as $xpdf) {
                                $m->addRaw($xpdf);
                            }
                        }

                        $xpath = 'attachments/workorders/'.$id.'/pdfs/';
                        $file_path = $xpath.'document-'.$id.'-'.$xid.'.pdf';
                        $attach_path = $file_path;

                        Storage::put($file_path, $m->merge());
                        $genPDF = Storage::get($file_path);
                        $mmm = new Merger();
                        $mmm->addRaw($genPDF);
                        $pageCount = $mmm->pageCount();
                        if ($pageCount > $max_page_length && ($recipient->mailing_type != 'none' && $recipient->mailing_type != 'other-mail')) {
                            $max_page_length = $pageCount;
                        }

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

                        if ($recipient->mailing_type == 'other-mail' || $recipient->email) {
                            //$client=$work->job->client;
                            //if ($client->notification_setting=='immediate'){
                            Mail::to($recipient)->send(new NoticeDelivery($recipient, $attach_path, $work->job->client));
                            //}
                            $recipient->email_sent = \Carbon\Carbon::now();
                            $recipient->save();
                        }
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
                    $tlines = $template->lines()->where('template_lines.type', 'apply-during-docgen')->get();
                    $k = 1000000;
                    foreach ($tlines as $xln) {
                        $k++;
                        $xline['recipient_id'] = $k;
                        $xline['description'] = $xln->description;
                        $xline['price'] = $xln->price;
                        $xline['quantity'] = $xln->quantity;
                        $xline['mailing_type'] = 'apply-during-docgen';
                        $new_lines['apply-during-docgen-'.$xln->description] = $xline;
                    }
                    if ($work->is_rush) {
                        $tlines = $template->lines()->where('template_lines.type', 'apply-during-docgen-rush')->get();
                        $k = 1000000;
                        foreach ($tlines as $xln) {
                            $k++;
                            $xline['recipient_id'] = $k;
                            $xline['description'] = $xln->description;
                            $xline['price'] = $xln->price;
                            $xline['quantity'] = $xln->quantity;
                            $xline['mailing_type'] = 'apply-during-docgen';
                            $new_lines['apply-during-docgen-rush-'.$xln->description] = $xline;
                        }
                    }
                    foreach ($work->recipients as $xrx) {
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
                    }
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
                    $tlines = $template->lines()->where('template_lines.type', 'apply-during-docgen')->get();
                    $k = 1000000;
                    foreach ($tlines as $xln) {
                        $k++;
                        $xline['recipient_id'] = $k;
                        $xline['description'] = $xln->description;
                        $xline['price'] = $xln->price;
                        $xline['quantity'] = $xln->quantity;
                        $xline['mailing_type'] = 'apply-during-docgen';
                        $new_lines['apply-during-docgen-'.$xln->description] = $xline;
                    }
                    if ($work->is_rush) {
                        $tlines = $template->lines()->where('template_lines.type', 'apply-during-docgen-rush')->get();
                        $k = 1000000;
                        foreach ($tlines as $xln) {
                            $k++;
                            $xline['recipient_id'] = $k;
                            $xline['description'] = $xln->description;
                            $xline['price'] = $xln->price;
                            $xline['quantity'] = $xln->quantity;
                            $xline['mailing_type'] = 'apply-during-docgen';
                            $new_lines['apply-during-docgen-rush-'.$xln->description] = $xline;
                        }
                    }

                    foreach ($work->recipients as $xrx) {
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
                    }
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
                    'max_page_length' => $max_page_length,
                    'client' => $client,
                    'work_order_id' => $work->id,
                    'work_order_number' => $work->number,
                    'recipients' => $work->recipients,
                    'new_lines' => $new_lines,
                    'mailing_types' => $mailing_types,
                ];

                return view('admin.pdf.invoice', $data);
            } else {
                ///////////////////////////////////////////////////////////////////////

                $mailing_types = [
                    'none' => 'None',
                    'standard-mail' => 'Regular Mail',
                    'certified-green' => 'Certfied Green RR',
                    'certified-nongreen' => 'Certfied Non Green',
                    'registered-mail' => 'Registered Mail',
                    'express-mail' => 'Express Mail',
                    'other-mail' => 'eMail',
                ];
                $max_page_length = 0;
                $work = WorkOrder::findOrFail($id);

                $attachments = Attachment::where('attachable_type', \App\WorkOrder::class)->where('attachable_id', $work->id)->where('type', 'generated')->get();
                foreach ($attachments as $attch) {
                    Storage::delete($attch->file_path);
                    Storage::delete($attch->thumb_path);
                    $attch->delete();
                }
                //$recipients =  WorkOrderRecipient::where('work_order_id',$id)->whereIN('party_id',$request->party_id)->get();
                $recipients = WorkOrderRecipient::where('work_order_id', $id)->get();
                foreach ($recipients as $rp) {
                    $rp->delete();
                }

                foreach ($request->party_id as $xid) {
                    $pdf_pages = [];
                    $xmailing_type = $request->mailing_type;
                    $xmailing_number = $request->mailing_number;
                    $xfirm_name = $request->firm_name;
                    $xparty_type = $request->party_type;
                    $xattention_name = $request->attention_name;
                    $xaddress = $request->address;
                    $xemails = $request->email;
                    $xalso_emails = $request->has('also_email') ? $request->also_email : [];
                    $xalso_emails_value = $request->has('also_email_value') ? $request->also_email_value : [];
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
                        } elseif (isset($xalso_emails[$xid])) {
                            $recipient->email = $xalso_emails_value[$xid];
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
                            $mid = $xbc;
                            $xbc .= $serial;
                            $xbc .= $this->LhunMod10($xbc);

                            $barcode = $this->storeBarcode($xbc, $mid, $serial);
                            $recipient->barcode = $barcode;

                            if (array_key_exists($xid, $xreturn_receipt)) {
                                $recipient->return_receipt = 1;
                            } else {
                                $recipient->return_receipt = 0;
                            }
                        }
                        if ($xmailing_type[$xid] == 'registered-mail' || $xmailing_type[$xid] == 'express-mail') {
                            $recipient->mailing_number = $xmailing_number[$xid];
                        }
                        $recipient->save();
                    }

                    if (isset($recipient)) {
                        foreach ($work->pdf_pages as $xpage) {
                            $address_type = $work->job->client->return_address_type;
                            $sni_address = nl2br(CompanySetting::first()->name." \n ".CompanySetting::first()->address);

                            $data = unserialize($xpage->fields);

                            $signature = $work->job->client->signature;

                            $data['signature'] = $signature;
                            $data['source'] = $recipient->party->source;
                            $data['mailing_type'] = $recipient->mailing_type;
                            $data['mailing_number'] = $recipient->mailing_number;
                            $data['client_mailing_address'] = ($address_type == 'sni') ? $sni_address : $work->job->client->mailing_address;
                            $data['barcode'] = $recipient->barcode;
                            $xaddress = $recipient->firm_name;
                            if ($recipient->attention_name != '') {
                                $xaddress .= '<br />ATTN: '.$recipient->attention_name;
                            }
                            $xaddress .= '<br />'.nl2br($recipient->address);
                            $data['mailing_address'] = $xaddress;
                            $data['wo_number'] = $work->id;
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

                            if ($xpage->type == 'out-of-state-nto-preliminary-notice-of-lien-rights') {
                                if (strlen($recipient->barcode) > 0) {
                                    $data['barcode'] = $recipient->barcode;
                                }
                                $data['document'] = 'pnolr';
                                $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                            }
                            if ($xpage->type == 'rescission-letter') {
                                if (strlen($recipient->barcode) > 0) {
                                    $data['barcode'] = $recipient->barcode;
                                }
                                $data['document'] = 'rl';
                                $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                            }

                            if ($xpage->type == 'notice-of-nonpayment-with-intent-to-lien-andor-foreclose') {
                                if (strlen($recipient->barcode) > 0) {
                                    // $data['barcode'] = 'XXXX XXXX XXXX '.substr($recipient->barcode,-4);;
                                    $data['barcode'] = $recipient->barcode;
                                }
                                $data['document'] = 'nonwitlaf';
                                $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                            }

                            if ($xpage->type == 'partial-satisfaction-of-lien') {
                                $data['document'] = 'psol';
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

                            if (isset($pdf)) {
                                $pdf_pages[] = $pdf->output();
                                unset($pdf);
                            }
                        }
                    }

                    $pagecount = 0;
                    // $existPDF = Storage::get($request['attachedFile']);
                    // $m = new Merger();
                    // $m->addRaw($existPDF);

                    if (count($pdf_pages) > 0) {
                        $pagecount++;
                        $m = new Merger();
                        foreach ($pdf_pages as $xpdf) {
                            $m->addRaw($xpdf);
                        }
                        $existPDF = Storage::get($request['attachedFile']);
                        $m->addRaw($existPDF);
                        if (isset($recipient)) {
                            $pdf_pages2 = [];
                            $address_type = $work->job->client->return_address_type;
                            $sni_address = nl2br(CompanySetting::first()->name." \n ".CompanySetting::first()->address);

                            foreach ($work->pdf_pages as $xpage) {
                                $data = unserialize($xpage->fields);

                                $signature = $work->job->client->signature;

                                $data['signature'] = $signature;
                                $data['source'] = $recipient->party->source;
                                $data['mailing_type'] = $recipient->mailing_type;
                                $data['mailing_number'] = $recipient->mailing_number;
                                $data['client_mailing_address'] = ($address_type == 'sni') ? $sni_address : $work->job->client->mailing_address;
                                $data['barcode'] = $recipient->barcode;
                                $xaddress = $recipient->firm_name;
                                if ($recipient->attention_name != '') {
                                    $xaddress .= '<br />ATTN: '.$recipient->attention_name;
                                }
                                $xaddress .= '<br />'.nl2br($recipient->address);
                                $data['mailing_address'] = $xaddress;
                                $data['wo_number'] = $work->id;

                                if ($xpage->type == 'notice-to-owner-back') {
                                    if ($recipient->mailing_type == 'other-mail') {
                                    } else {
                                        $data['document'] = 'ntoback';
                                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                                    }
                                    //return $pdf->download();
                                }
                                if ($xpage->type == 'amended-notice-to-owner-back') {
                                    if ($recipient->mailing_type == 'other-mail') {
                                    } else {
                                        $data['document'] = 'antoback';
                                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                                    }
                                    //return $pdf->download();
                                }

                                if (! ($xpage->type == 'notice-to-owner' || $xpage->type == 'notice-to-owner-back' || $xpage->type == 'amended-notice-to-owner' || $xpage->type == 'amended-notice-to-owner-back')) {
                                    if ($recipient->mailing_type == 'other-mail') {
                                    } else {
                                        $data['document'] = 'allback';

                                        $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                                    }
                                }
                                if (isset($pdf)) {
                                    $pdf_pages2[] = $pdf->output();
                                    unset($pdf);
                                }
                            }
                        }
                        foreach ($pdf_pages2 as $xpdf) {
                            $m->addRaw($xpdf);
                        }

                        $xpath = 'attachments/workorders/'.$id.'/pdfs/';
                        $file_path = $xpath.'document-'.$id.'-'.$xid.'.pdf';
                        $attach_path = $file_path;

                        Storage::put($file_path, $m->merge());
                        $genPDF = Storage::get($file_path);
                        $mmm = new Merger();
                        $mmm->addRaw($genPDF);
                        $pageCount = $mmm->pageCount();
                        if ($pageCount > $max_page_length && ($recipient->mailing_type != 'none' && $recipient->mailing_type != 'other-mail')) {
                            $max_page_length = $pageCount;
                        }

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

                        if ($recipient->mailing_type == 'other-mail' || $recipient->email) {
                            //$client=$work->job->client;
                            //if ($client->notification_setting=='immediate'){
                            Mail::to($recipient)->send(new NoticeDelivery($recipient, $attach_path, $work->job->client));
                            //}
                            $recipient->email_sent = \Carbon\Carbon::now();
                            $recipient->save();
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
                    $tlines = $template->lines()->where('template_lines.type', 'apply-during-docgen')->get();
                    $k = 1000000;
                    foreach ($tlines as $xln) {
                        $k++;
                        $xline['recipient_id'] = $k;
                        $xline['description'] = $xln->description;
                        $xline['price'] = $xln->price;
                        $xline['quantity'] = $xln->quantity;
                        $xline['mailing_type'] = 'apply-during-docgen';
                        $new_lines['apply-during-docgen-'.$xln->description] = $xline;
                    }
                    if ($work->is_rush) {
                        $tlines = $template->lines()->where('template_lines.type', 'apply-during-docgen-rush')->get();
                        $k = 1000000;
                        foreach ($tlines as $xln) {
                            $k++;
                            $xline['recipient_id'] = $k;
                            $xline['description'] = $xln->description;
                            $xline['price'] = $xln->price;
                            $xline['quantity'] = $xln->quantity;
                            $xline['mailing_type'] = 'apply-during-docgen';
                            $new_lines['apply-during-docgen-rush-'.$xln->description] = $xline;
                        }
                    }
                    foreach ($work->recipients as $xrx) {
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
                    }
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
                    $tlines = $template->lines()->where('template_lines.type', 'apply-during-docgen')->get();
                    $k = 1000000;
                    foreach ($tlines as $xln) {
                        $k++;
                        $xline['recipient_id'] = $k;
                        $xline['description'] = $xln->description;
                        $xline['price'] = $xln->price;
                        $xline['quantity'] = $xln->quantity;
                        $xline['mailing_type'] = 'apply-during-docgen';
                        $new_lines['apply-during-docgen-'.$xln->description] = $xline;
                    }
                    if ($work->is_rush) {
                        $tlines = $template->lines()->where('template_lines.type', 'apply-during-docgen-rush')->get();
                        $k = 1000000;
                        foreach ($tlines as $xln) {
                            $k++;
                            $xline['recipient_id'] = $k;
                            $xline['description'] = $xln->description;
                            $xline['price'] = $xln->price;
                            $xline['quantity'] = $xln->quantity;
                            $xline['mailing_type'] = 'apply-during-docgen';
                            $new_lines['apply-during-docgen-rush-'.$xln->description] = $xline;
                        }
                    }
                    foreach ($work->recipients as $xrx) {
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
                    }
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
                    'max_page_length' => $max_page_length,
                    'client' => $client,
                    'work_order_id' => $work->id,
                    'work_order_number' => $work->number,
                    'recipients' => $work->recipients,
                    'new_lines' => $new_lines,
                    'mailing_types' => $mailing_types,
                ];

                return view('admin.pdf.invoice', $data);
            }
        } else {
            ////////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////////
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
            $max_page_length = 0;
            $work = WorkOrder::findOrFail($id);

            $attachments = Attachment::where('attachable_type', \App\WorkOrder::class)->where('attachable_id', $work->id)->where('type', 'generated')->get();
            foreach ($attachments as $attch) {
                Storage::delete($attch->file_path);
                Storage::delete($attch->thumb_path);
                $attch->delete();
            }
            //$recipients =  WorkOrderRecipient::where('work_order_id',$id)->whereIN('party_id',$request->party_id)->get();
            $recipients = WorkOrderRecipient::where('work_order_id', $id)->get();
            foreach ($recipients as $rp) {
                $rp->delete();
            }

            foreach ($request->party_id as $xid) {
                $pdf_pages = [];
                $xmailing_type = $request->mailing_type;
                $xmailing_number = $request->mailing_number;
                $xfirm_name = $request->firm_name;
                $xparty_type = $request->party_type;
                $xattention_name = $request->attention_name;
                $xaddress = $request->address;
                $xemails = $request->email;
                $xalso_emails = $request->has('also_email') ? $request->also_email : [];
                $xalso_emails_value = $request->has('also_email_value') ? $request->also_email_value : [];
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
                    } elseif (isset($xalso_emails[$xid])) {
                        $recipient->email = $xalso_emails_value[$xid];
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
                            $mid = $xbc;
                        $xbc .= $serial;
                        $xbc .= $this->LhunMod10($xbc);

                        $barcode = $this->storeBarcode($xbc, $mid, $serial);
                        $recipient->barcode = $barcode;

                        if (array_key_exists($xid, $xreturn_receipt)) {
                            $recipient->return_receipt = 1;
                        } else {
                            $recipient->return_receipt = 0;
                        }
                    }
                    if ($xmailing_type[$xid] == 'registered-mail' || $xmailing_type[$xid] == 'express-mail') {
                        $recipient->mailing_number = $xmailing_number[$xid];
                    }
                    $recipient->save();
                }

                if (isset($recipient)) {
                    $pdf_pages2 = [];
                    $address_type = $work->job->client->return_address_type;
                    $sni_address = nl2br(CompanySetting::first()->name." \n ".CompanySetting::first()->address);

                    foreach ($work->pdf_pages as $xpage) {
                        $data = unserialize($xpage->fields);

                        $signature = $work->job->client->signature;

                        $data['signature'] = $signature;
                        $data['source'] = $recipient->party->source;
                        $data['client_mailing_address'] = ($address_type == 'sni') ? $sni_address : $work->job->client->mailing_address;
                        $data['barcode'] = $recipient->barcode;
                        $data['mailing_type'] = $recipient->mailing_type;
                        $data['mailing_number'] = $recipient->mailing_number;
                        $xaddress = $recipient->firm_name;
                        if ($recipient->attention_name != '') {
                            $xaddress .= '<br />ATTN: '.$recipient->attention_name;
                        }
                        $xaddress .= '<br />'.nl2br($recipient->address);
                        $data['mailing_address'] = $xaddress;
                        $data['wo_number'] = $work->id;
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

                        if ($xpage->type == 'notice-to-owner-back') {
                            if ($recipient->mailing_type == 'other-mail') {
                            } else {
                                $data['document'] = 'ntoback';
                                $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                            }
                            //return $pdf->download();
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

                        if ($xpage->type == 'amended-notice-to-owner-back') {
                            if ($recipient->mailing_type == 'other-mail') {
                            } else {
                                $data['document'] = 'antoback';
                                $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                            }
                            //return $pdf->download();
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

                        if ($xpage->type == 'out-of-state-nto-preliminary-notice-of-lien-rights') {
                            if (strlen($recipient->barcode) > 0) {
                                $data['barcode'] = $recipient->barcode;
                            }
                            $data['document'] = 'pnolr';
                            $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                        }
                        if ($xpage->type == 'rescission-letter') {
                            if (strlen($recipient->barcode) > 0) {
                                $data['barcode'] = $recipient->barcode;
                            }
                            $data['document'] = 'rl';
                            $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                        }

                        if ($xpage->type == 'notice-of-nonpayment-with-intent-to-lien-andor-foreclose') {
                            if (strlen($recipient->barcode) > 0) {
                                // $data['barcode'] = 'XXXX XXXX XXXX '.substr($recipient->barcode,-4);;
                                $data['barcode'] = $recipient->barcode;
                            }
                            $data['document'] = 'nonwitlaf';
                            $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');
                        }

                        if ($xpage->type == 'partial-satisfaction-of-lien') {
                            $data['document'] = 'psol';
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

                        if (isset($pdf)) {
                            $pdf_pages[] = $pdf->output();
                            unset($pdf);
                        }

                        if (! ($xpage->type == 'notice-to-owner' || $xpage->type == 'notice-to-owner-back' || $xpage->type == 'amended-notice-to-owner' || $xpage->type == 'amended-notice-to-owner-back')) {
                            if ($recipient->mailing_type == 'other-mail') {
                            } else {
                                $data['document'] = 'allback';

                                $pdf = PDF::loadView('admin.pdf.pdf-document', $data)->setPaper('Letter');

                                $pdf_pages[] = $pdf->output();
                                unset($pdf);
                            }
                        }
                    }
                }

                $pagecount = 0;

                if (count($pdf_pages) > 0) {
                    $pagecount++;
                    $m = new Merger();
                    foreach ($pdf_pages as $xpdf) {
                        $m->addRaw($xpdf);
                    }

                    $xpath = 'attachments/workorders/'.$id.'/pdfs/';
                    $file_path = $xpath.'document-'.$id.'-'.$xid.'.pdf';
                    $attach_path = $file_path;

                    Storage::put($file_path, $m->merge());

                    $genPDF = Storage::get($file_path);
                    $mmm = new Merger();
                    $mmm->addRaw($genPDF);
                    $pageCount = $mmm->pageCount();
                    if ($pageCount > $max_page_length && ($recipient->mailing_type != 'none' && $recipient->mailing_type != 'other-mail')) {
                        $max_page_length = $pageCount;
                    }

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

                    if ($recipient->mailing_type == 'other-mail' || $recipient->email) {
                        //$client=$work->job->client;
                        //if ($client->notification_setting=='immediate'){
                        Mail::to($recipient)->send(new NoticeDelivery($recipient, $attach_path, $work->job->client));
                        //}
                        $recipient->email_sent = \Carbon\Carbon::now();
                        $recipient->save();
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
                $tlines = $template->lines()->where('template_lines.type', 'apply-during-docgen')->get();
                $k = 1000000;
                foreach ($tlines as $xln) {
                    $k++;
                    $xline['recipient_id'] = $k;
                    $xline['description'] = $xln->description;
                    $xline['price'] = $xln->price;
                    $xline['quantity'] = $xln->quantity;
                    $xline['mailing_type'] = 'apply-during-docgen';
                    $new_lines['apply-during-docgen-'.$xln->description] = $xline;
                }
                if ($work->is_rush) {
                    $tlines = $template->lines()->where('template_lines.type', 'apply-during-docgen-rush')->get();
                    $k = 1000000;
                    foreach ($tlines as $xln) {
                        $k++;
                        $xline['recipient_id'] = $k;
                        $xline['description'] = $xln->description;
                        $xline['price'] = $xln->price;
                        $xline['quantity'] = $xln->quantity;
                        $xline['mailing_type'] = 'apply-during-docgen';
                        $new_lines['apply-during-docgen-rush-'.$xln->description] = $xline;
                    }
                }
                foreach ($work->recipients as $xrx) {
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
                }
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
                $tlines = $template->lines()->where('template_lines.type', 'apply-during-docgen')->get();
                $k = 1000000;
                foreach ($tlines as $xln) {
                    $k++;
                    $xline['recipient_id'] = $k;
                    $xline['description'] = $xln->description;
                    $xline['price'] = $xln->price;
                    $xline['quantity'] = $xln->quantity;
                    $xline['mailing_type'] = 'apply-during-docgen';
                    $new_lines['apply-during-docgen-'.$xln->description] = $xline;
                }
                if ($work->is_rush) {
                    $tlines = $template->lines()->where('template_lines.type', 'apply-during-docgen-rush')->get();
                    $k = 1000000;
                    foreach ($tlines as $xln) {
                        $k++;
                        $xline['recipient_id'] = $k;
                        $xline['description'] = $xln->description;
                        $xline['price'] = $xln->price;
                        $xline['quantity'] = $xln->quantity;
                        $xline['mailing_type'] = 'apply-during-docgen';
                        $new_lines['apply-during-docgen-rush-'.$xln->description] = $xline;
                    }
                }

                foreach ($work->recipients as $xrx) {
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
                }
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
                'max_page_length' => $max_page_length,
                'client' => $client,
                'work_order_id' => $work->id,
                'work_order_number' => $work->number,
                'recipients' => $work->recipients,
                'new_lines' => $new_lines,
                'mailing_types' => $mailing_types,
            ];

            return view('admin.pdf.invoice', $data);
        }
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

    public function storeBarcode($barcode, $mid, $serial)
    {
        try {
            Barcode::create(['code' => $barcode]);

            return $barcode;
        } catch (\Exception $e) {
            $newSerial = $serial + 7;
            $newBarcode = $mid.$serial;
            $newBarcode .= $this->LhunMod10($newBarcode);
            Settings::set('barcode.serial', $newSerial);

            return $this->storeBarcode($newBarcode, $mid, $newSerial);
        }
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
        if ($request->has('print')) {
            $work = WorkOrder::findOrFail($request->work_order_id);
            $job = $work->job;
            if ($request->print == 'yes') {
                $work->status = 'print';
                $work->save();

                // $mailto = array();

                 // $users = $work->job->client->activeusers;
                 // foreach ($users as $user) {
                 //        $mailto [] = $user->email;
                 //  }
                 // $client= $work->job->client;
                 // if ($client->notification_setting=='immediate'){
                 //   if (count($mailto) > 0) {
                 //      Mail::to($mailto)->send(new NoticeComplete($work->id,$work->invoicesPending));
                 //   }
                 // }
            }

            return redirect()->route('jobs.edit', $job->id);
        }
    }

    public function deleteRegenerate($id)
    {
        $work = WorkOrder::findOrFail($id);
        $attachments = $work->attachments()->where('type', 'generated')->get();
        $recipients = $work->recipients;
        foreach ($work->pdf_pages as $xpage) {
            $xpage->delete();
        }
        foreach ($attachments as $xattachment) {
            $xattachment->delete();
        }
        foreach ($recipients as $xrecipient) {
            $xrecipient->delete();
        }
        Session::flash('message', 'Attachments(PDF) were deleted.');

        return redirect()->route('workorders.document', $id);
    }
}
