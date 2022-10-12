<?php

namespace App\Http\Controllers\Clients;

use App\Attachment;
use App\Http\Controllers\Controller;
use App\Job;
use App\Notifications\NewRelease;
use App\User;
use Auth;
use iio\libmergepdf\Merger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Response;
use PDF;
use Session;
use Storage;

class ReleaseController extends Controller
{
    public function newrelease()
    {
        $client = auth()->user()->client;
        $jobs = $client->jobs;
        $jobs = $client->jobs;
        $jobs = $jobs->where('status', '!=', 'closed')->where('deleted_at', null);
        $jobs = $jobs->pluck('name', 'id');

        $releases = [
            'waiver-and-release-of-lien-upon-progress-payment' => 'WAIVER AND RELEASE OF LIEN UPON PROGRESS PAYMENT (Non Conditional)',
            'waiver-and-release-of-lien-upon-final-payment' => 'WAIVER AND RELEASE OF LIEN UPON FINAL PAYMENT (Non Conditional)',
            'conditional-waiver-and-release-of-lien-upon-progress-payment' => 'CONDITIONAL WAIVER AND RELEASE OF LIEN UPON PROGRESS PAYMENT',
            'conditional-waiver-and-release-of-lien-upon-final-payment' => 'CONDITIONAL WAIVER AND RELEASE OF LIEN UPON FINAL PAYMENT ',

        ];
        $data = [
            'jobs' => $jobs,
            'releases' => $releases,
        ];

        return view('client.release.new', $data);
    }

    public function getparties($id)
    {
        $job = Job::findOrFail($id);

        $parties = $job->parties()->where('type', 'customer')->get();

        $data = [
            'parties' => $parties,
        ];

        return view('client.release.components.partieslist', $data);
    }

    public function pdf(Request $request)
    {
        $this->validate($request, [

            'job_id' => 'required',
        ]);
        $work_type = $request->release_type;
        $job = Job::findOrFail($request->job_id);
        $client_company_name = $job->client->company_name;
        $nto_number = $job->number;
        $client = $job->parties->where('type', 'client')->first();
        $customer = $job->parties->where('type', 'customer')->first();
        $landowner = $job->parties->where('type', 'landowner')->first();
        if ($client) {
            $client_name = $client->contact->full_name;
            if ($job->client->gender == 'female') {
                $client_heshe = 'She';
            } else {
                $client_heshe = 'He';
            }
            $client_phone = $client->contact->phone;
            $client_email = $client->contact->email;
            $client_address = $client->contact->address_no_country;
            $client_county = $client->contact->county;
        } else {
            $client_name = '';
            $client_heshe = '';
            $client_phone = '';
            $client_email = '';
            $client_address = '';
            $client_county = '';
        }
        $client_county = $job->client->county;
        $client_title = $job->client->title;

        if ($landowner) {
            $land_owner_firm_name = $landowner->contact->entity->firm_name;
            $land_owner_name = $landowner->contact->full_name;
            $land_owner_address = $landowner->contact->address_no_country;
            $deed = $landowner->landowner_deed_number;
        } else {
            $land_owner_firm_name = '';
            $land_owner_name = '';
            $land_owner_address = '';
            $deed = '';
        }
        if ($customer) {
            $customer_name = $customer->contact->entity->firm_name;
        } else {
            $customer_name = '';
        }

        $job_name = $job->name;
        $job_address = $job->full_address_no_country;
        $job_county = $job->county;
        $job_legal = $job->legal_description;

        if ($work_type == 'waiver-and-release-of-lien-upon-final-payment') {
            $warolufppage = [
                'type' => $work_type,
                'client_company_name' => $client_company_name,
                'client_name' => $client_name,
                'client_address' => $client_address,
                'client_county' => $client_county,
                'client_title' => $client_title, 'client_email' => $client_email,
                'customer_name' => $customer_name,
                'dated_on' => date('m/d/Y'),
                'amount' => 0,
                'land_owner_firm_name' => $land_owner_firm_name,
                'nto_number' => $nto_number,
                'job_name' => $job_name,
                'job_address' => $job_address,
                'job_county' => $job_county,
                'sworn_signed_at' => '',
                'below_title' => '',
                'job_legal' => $job_legal,
            ];
            $pdf_pages[] = $warolufppage;
        }

        if ($work_type == 'waiver-and-release-of-lien-upon-progress-payment') {
            $warolupppage = [
                'type' => $work_type,
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
                'below_title' => '',
                'job_legal' => $job_legal,
            ];
            $pdf_pages[] = $warolupppage;
        }

        if ($work_type == 'conditional-waiver-and-release-of-lien-upon-progress-payment') {
            $cwarolupppage = [
                'type' => $work_type,
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
                'below_title' => '',
                'job_legal' => $job_legal,
            ];
            $pdf_pages[] = $cwarolupppage;
        }

        if ($work_type == 'conditional-waiver-and-release-of-lien-upon-final-payment') {
            $cwarolufppage = [
                'type' => $work_type,
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
                'below_title' => '',
                'job_legal' => $job_legal,
            ];
            $pdf_pages[] = $cwarolufppage;
        }

        $selected = $job->parties()->where('type', 'customer')->get();
        $view = true;
        $signature = $job->client->signature;
        $data = [
            'job_id' => $request->job_id,
            'selected' => $selected,
            'pdf_pages' => $pdf_pages,
            'view' => $view,
            'signature' => $signature,
        ];

        return view('client.release.document', $data);
    }

    public function update(Request $request)
    {
        $fields = array_except($request->all(), ['_token']);

        $xfields = serialize($this->replace_on_array(chr(13), '<br/>', $fields));

        if ($fields['type'] == 'waiver-and-release-of-lien-upon-final-payment') {
            return view('client.release.warolufp', unserialize($xfields));
        }

        if ($fields['type'] == 'waiver-and-release-of-lien-upon-progress-payment') {
            return view('client.release.warolupp', unserialize($xfields));
        }

        if ($fields['type'] == 'conditional-waiver-and-release-of-lien-upon-progress-payment') {
            return view('client.release.cwarolupp', unserialize($xfields));
        }

        if ($fields['type'] == 'conditional-waiver-and-release-of-lien-upon-final-payment') {
            return view('client.release.cwarolufp', unserialize($xfields));
        }
    }

    public function generate(Request $request)
    {
        $data = array_except($request->all(), ['_token']);
        if (! isset($data['selected'])) {
            Session::flash('message', 'No Contact');

            return redirect()->route('client.release.new');
        }

        $id = $data['job_id'];
        $job = Job::findOrFail($data['job_id']);

        $signature = $data['signature'];

        $data['signature'] = $signature;
        $data['job_address'] = str_replace(chr(13), '<br>', $data['job_address']);
        $data['client_address'] = str_replace(chr(13), '<br />', $data['client_address']);
        if ($data['type'] == 'waiver-and-release-of-lien-upon-final-payment') {
            $data['document'] = 'warolufp';
        }

        if ($data['type'] == 'waiver-and-release-of-lien-upon-progress-payment') {
            $data['document'] = 'warolupp';
        }

        if ($data['type'] == 'conditional-waiver-and-release-of-lien-upon-progress-payment') {
            $data['document'] = 'cwarolupp';
        }

        if ($data['type'] == 'conditional-waiver-and-release-of-lien-upon-final-payment') {
            $data['document'] = 'cwarolufp';
        }
        $pdf = PDF::loadView('client.release.pdf-document', $data)->setPaper('Letter');
        $pdf_pages[] = $pdf->output();

        $pagecount = 0;

        if (count($pdf_pages) > 0) {
            $pagecount++;
            $m = new Merger();
            foreach ($pdf_pages as $xpdf) {
                foreach ($data['selected'] as $sel) {
                    $m->addRaw($xpdf);
                }
            }

            //return Response::make($m->merge(), 200, [
            //    'Content-Type' => 'application/pdf',
            //    'Content-Disposition' => 'inline; filename=""'
            //]);
            $attachment = new Attachment();
            $attachment->type = 'release';
            if ($data['document'] == 'warolufp' || $data['document'] == 'warolupp') {
                $attachment->is_final_release = 1;
            }
            $attachment->generated_id = '0';
            $xdescription = 'Automatically generated release for '.$job->name;

            $attachment->description = strtoupper($xdescription);
            $attachment->file_mime = 'application/pdf';
            $attachment->user_id = Auth::user()->id;
            $job->attachments()->save($attachment);
            $attachment->save();

            $xpath = 'attachments/jobs/'.$id.'/pdfs/';
            $file_path = $xpath.'document-'.$id.'-release-'.$attachment->id.'.pdf';

            Storage::put($file_path, $m->merge());

            //lets Attach the file to the Work Order
            $attachment->file_size = Storage::size($file_path);
            $attachment->file_path = $file_path;
            $attachment->original_name = 'document-'.$id.'-release-'.$attachment->id.'.pdf';
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

            // send notifications

            $data = [
                'note' => 'Have been added to a Job',
                'entered_at' => $attachment->created_at->format('Y-m-d H:i:s'),
            ];
            $adminEmail = \App\AdminEmails::where('class', 'NewRelease')->first();
            $adminUserIds = explode(',', $adminEmail->users);
            if (count($adminUserIds) > 0 && $adminEmail->users) {
                $admin_users = User::where('status', 1)->whereIn('id', $adminUserIds)->get();
            } else {
                $admin_users = User::where('status', 1)->isRole(['admin', 'researcher'])->get();
            }

            Notification::send($admin_users, new NewRelease($job->id, $data, Auth::user()->full_name));

            // redirect to jonbs attachment
            $data = [
                'job' => $job,
            ];

            return view('client.release.close', $data);
        //return redirect(route('client.jobs.edit',$request->job_id) . "#attachments");
        } else {
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

    public function doClose(Request $request)
    {
        if ($request->has('complete')) {
            $job = Job::findOrFail($request->job_id);
            if ($request->complete == 'yes') {
                $job->status = 'closed';
                $job->save();
            }

            return redirect(route('client.jobs.edit', $job->id).'#attachments');
        }
    }
}
