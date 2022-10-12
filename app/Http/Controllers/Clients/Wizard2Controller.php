<?php

namespace App\Http\Controllers\Clients;

use App\Attachment;
use App\AttachmentType;
use App\Client;
use App\CompanySetting;
use App\ContactInfo;
use App\ContractTracker;
use App\Coordinate;
use App\Custom\Payeezy;
use App\Entity;
use App\Http\Controllers\Controller;
use App\Invoice;
use App\InvoiceLine;
use App\Job;
use App\JobLog;
use App\JobParty;
use App\Mail\PaymentMade;
use App\Note;
use App\Notifications\NewAttachment;
use App\Notifications\NewWorkOrder;
use App\Notifications\PastDueClientEnteredWorkOrder;
use App\Notifications\ShareJobSelectedOnWorkorderCreation;
use App\Payment;
use App\SharedJobToUser;
use App\Template;
use App\TempUser;
use App\User;
use App\WorkOrder;
use App\WorkOrderAnswers;
use App\WorkOrderFields;
use App\WorkOrderType;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Mail;
use Response;
use Session;
use Storage;

class Wizard2Controller extends Controller
{
    //private $counties=['Alachua','Baker','Bay','Bradford','Brevard','Broward','Calhoun','Charlotte','Citrus','Clay','Collier','Columbia','DeSoto','Dixie','Duval','Escambia','Flagler','Franklin','Gadsden','Gilchrist','Glades','Gulf','Hamilton','Hardee','Hendry','Hernando','Highlands','Hillsborough','Holmes','Indian River','Jackson','Jefferson','Lafayette','Lake','Lee','Leon','Levy','Liberty','Madison','Manatee','Marion','Martin','Miami-Dade','Monroe','Nassau','Okaloosa','Okeechobee','Orange','Osceola','Palm Beach','Pasco','Pinellas','Polk','Putnam','Santa Rosa','Sarasota','Seminole','St. Johns','St. Lucie','Sumter','Suwannee','Taylor','Union','Volusia','Wakulla','Walton','Washington'];
    private $counties = ['ALACHUA', 'BAKER', 'BAY', 'BRADFORD', 'BREVARD', 'BROWARD', 'CALHOUN', 'CHARLOTTE', 'CITRUS', 'CLAY', 'COLLIER', 'COLUMBIA', 'DESOTO', 'DIXIE', 'DUVAL', 'ESCAMBIA', 'FLAGLER', 'FRANKLIN', 'GADSDEN', 'GILCHRIST', 'GLADES', 'GULF', 'HAMILTON', 'HARDEE', 'HENDRY', 'HERNANDO', 'HIGHLANDS', 'HILLSBOROUGH', 'HOLMES', 'INDIAN RIVER', 'JACKSON', 'JEFFERSON', 'LAFAYETTE', 'LAKE', 'LEE', 'LEON', 'LEVY', 'LIBERTY', 'MADISON', 'MANATEE', 'MARION', 'MARTIN', 'MIAMI-DADE', 'MONROE', 'NASSAU', 'OKALOOSA', 'OKEECHOBEE', 'ORANGE', 'OSCEOLA', 'PALM BEACH', 'PASCO', 'PINELLAS', 'POLK', 'PUTNAM', 'SANTA ROSA', 'SARASOTA', 'SEMINOLE', 'ST. JOHNS', 'ST. LUCIE', 'SUMTER', 'SUWANNEE', 'TAYLOR', 'UNION', 'VOLUSIA', 'WAKULLA', 'WALTON', 'WASHINGTON'];

    public function getEmployer($job_id)
    {
        $job = Job::findOrFail($job_id);

        $this->authorize('wizard', $job);

        $entities = $job->client->entities->pluck('firm_name', 'id');
        if (Session::has('workorder')) {
            $xworkorder = Session::get('workorder');
        } else {
            $xworkorder = request()->input('workorder');
        }

        $parties_type = [
            'general_contractor' => 'General Contractor',
            'sub_contractor' => 'Sub Contractor',
            'sub_sub' => 'Sub-Sub Contractor',
            'landowner' => 'Property Owner',
            'leaseholder' => 'Lease Holder (Lessee/Tenant)',

            'architect' => 'Architect',
            'condo assoc' => 'Condo Assoc',
            'Developer' => 'Developer',
            'engineer' => 'Engineer',
            'government agency' => 'Government Agency',
            'homeowners assoc' => 'Homeowners Assoc',
            'management co' => 'Management Co',
            'surveying co' => 'Surveying Co',
            'other' => 'Other',
        ];

        $gender = [
            'none' => 'Select one..',
            'female' => 'Female',
            'male' => 'Male',
        ];

        $data = [
            'job' => $job,
            'parties_type' => $parties_type,
            'work_order' => $xworkorder,
            'entities' => $entities,
            'gender' => $gender,
        ];

        return view('client.wizard2.employer', $data);
    }

    public function getGC($job_id)
    {
        $job = Job::findOrFail($job_id);

        $this->authorize('wizard', $job);

        $entities = $job->client->entities->pluck('firm_name', 'id');
        if (Session::has('workorder')) {
            $xworkorder = Session::get('workorder');
        } else {
            $xworkorder = request()->input('workorder');
        }

        $parties_type = [
            'general_contractor' => 'General Contractor',

        ];

        $gender = [
            'none' => 'Select one..',
            'female' => 'Female',
            'male' => 'Male',
        ];

        $data = [
            'job' => $job,

            'work_order' => $xworkorder,
            'entities' => $entities,
            'gender' => $gender,
        ];

        return view('client.wizard2.gc', $data);
    }

    public function getLandowner($job_id)
    {
        $job = Job::findOrFail($job_id);

        $this->authorize('wizard', $job);

        $entities = $job->client->entities->pluck('firm_name', 'id');
        if (Session::has('workorder')) {
            $xworkorder = Session::get('workorder');
        } else {
            $xworkorder = request()->input('workorder');
        }

        $parties_type = [
            'landowner' => 'Property Owner',

        ];

        $gender = [
            'none' => 'Select one..',
            'female' => 'Female',
            'male' => 'Male',
        ];

        $data = [
            'job' => $job,

            'work_order' => $xworkorder,
            'entities' => $entities,
            'gender' => $gender,
        ];

        return view('client.wizard2.landowner', $data);
    }

    public function getOther($job_id)
    {
        $job = Job::findOrFail($job_id);

        $this->authorize('wizard', $job);

        $entities = $job->client->entities->pluck('firm_name', 'id');
        if (Session::has('workorder')) {
            $xworkorder = Session::get('workorder');
        } else {
            $xworkorder = request()->input('workorder');
        }

        $parties_type = [
            'copy_recipient' => 'Copy Recipient',

        ];

        $gender = [
            'none' => 'Select one..',
            'female' => 'Female',
            'male' => 'Male',
        ];

        $data = [
            'job' => $job,
            'work_order' => $xworkorder,
            'entities' => $entities,
            'gender' => $gender,
        ];

        return view('client.wizard2.other', $data);
    }

    public function getLeaseholder($job_id)
    {
        $job = Job::findOrFail($job_id);

        $this->authorize('wizard', $job);

        $entities = $job->client->entities->pluck('firm_name', 'id');
        if (Session::has('workorder')) {
            $xworkorder = Session::get('workorder');
        } else {
            $xworkorder = request()->input('workorder');
        }

        $parties_type = [
            'leaseholder' => 'Lease Holder',

        ];

        $gender = [
            'none' => 'Select one..',
            'female' => 'Female',
            'male' => 'Male',
        ];

        $data = [
            'job' => $job,

            'work_order' => $xworkorder,
            'entities' => $entities,
            'gender' => $gender,
        ];

        return view('client.wizard2.leaseholder', $data);
    }

    public function getBond($job_id)
    {
        $job = Job::findOrFail($job_id);

        $this->authorize('wizard', $job);

        $entities = $job->client->entities->pluck('firm_name', 'id');
        if (Session::has('workorder')) {
            $xworkorder = Session::get('workorder');
        } else {
            $xworkorder = request()->input('workorder');
        }

        $parties_type = [
            'bond' => 'Bond',

        ];

        $gender = [
            'none' => 'Select one..',
            'female' => 'Female',
            'male' => 'Male',
        ];

        $data = [
            'job' => $job,
            'work_order' => $xworkorder,
            'entities' => $entities,
            'gender' => $gender,
        ];

        return view('client.wizard2.bond', $data);
    }

    public function getParties($job_id)
    {
        $job = Job::findOrFail($job_id);

        $this->authorize('wizard', $job);

        $entities = $job->client->entities->pluck('firm_name', 'id');
        if (Session::has('workorder')) {
            $xworkorder = Session::get('workorder');
        } else {
            $xworkorder = request()->input('workorder');
        }
        if (! $xworkorder) {
            return redirect()->route('wizard2.getjobworkorder');
        }
        $parties_type = [
            '' => '',
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
        $parties_type1 = [

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

        $gender = [
            'none' => 'Select one..',
            'female' => 'Female',
            'male' => 'Male',
        ];

        $attachment_types = AttachmentType::where('slug', '!=', 'generated')->get()->pluck('name', 'slug');

        $data = [
            'job' => $job,
            'parties_type' => $parties_type,
            'parties_type1' => $parties_type1,
            'work_order' => $xworkorder,
            'entities' => $entities,
            'gender' => $gender,
            'attachment_types' => $attachment_types,
        ];

        return view('client.wizard2.parties', $data);
    }

    public function addattachments(Request $request, $job_id)
    {
        $job = Job::findOrFail($job_id);
        $this->authorize('wizard', $job);

        $this->validate($request, [
            'file' => 'required|file',
        ]);

        $attachment = new Attachment();
        $f = $request->file('file');

        $attachment->type = $request->input('type');
        $attachment->description = $request->input('description');
        $attachment->original_name = $f->getClientOriginalName();
        $attachment->file_mime = $f->getMimeType();
        $attachment->file_size = $f->getSize();
        $attachment->user_id = Auth::user()->id;
        if ($request->attach_to == 'job') {
            $xentity = Job::findOrFail($request->to_id);
            $xpath = 'attachments/jobs/'.$request->to_id.'/';
        } else {
            $xentity = WorkOrder::findOrFail($request->to_id);
            $xpath = 'attachments/workorders/'.$request->to_id.'/';
        }

        $xentity->attachments()->save($attachment);
        $attachment->save();

        $xfilename = 'attachment-'.$attachment->id.'.'.$f->guessExtension();

        $f->storeAs($xpath, $xfilename);
        $attachment->file_path = $xpath.$xfilename;
        $attachment->save();

        //dd($f->getMimeType());
        switch ($f->getMimeType()) {
            case 'application/pdf':
                $xblob = file_get_contents($f->getRealPath());
                $img = new \Imagick();
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
                $img = new \Imagick();
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

        $adminEmail = \App\AdminEmails::where('class', 'NewAttachment')->first();
        $adminUserIds = explode(',', $adminEmail->users);
        if (count($adminUserIds) > 0 && $adminEmail->users) {
            $admin_users = User::where('status', 1)->whereIn('id', $adminUserIds)->get();
        } else {
            $admin_users = User::where('status', 1)->isRole(['admin', 'researcher'])->get();
        }
        $data = [
            'note' => 'Have been added to a Job',
            'entered_at' => $attachment->created_at->format('Y-m-d H:i:s'),
        ];
        foreach ($admin_users as $user) {
            Notification::send($user, new NewAttachment($attachment->id, $data, '', Auth::user()->full_name, 'job'));
        }

        if ($job->notify_email) {
            $notify_user = TempUser::create(['email' => $job->notify_email]);
            Notification::send($notify_user, new NewAttachment($attachment->id, $data, '', Auth::user()->full_name, 'job'));
            $notify_user->delete();
        }

        Session::flash('message', 'Attachment added');

        return redirect()->route('wizard2.getparties', [$job_id]);
    }

    public function deleteattachment($job_id, $att_id)
    {
        $job = Job::findOrFail($job_id);
        $this->authorize('wizard', $job);

        $attachment = Attachment::findOrFail($att_id);

        if (is_null($attachment->thumb_path)) {
        } else {
            Storage::delete($attachment->thumb_path);
        }
        Storage::delete($attachment->file_path);
        $attachment->delete();

        Session::flash('message', 'Attachment removed');

        return redirect()->route('wizard2.getparties', [$job_id]);
    }

    public function showattachment($job_id, $id)
    {
        $attachment = Attachment::findOrFail($id);
        $contents = Storage::get($attachment->file_path);
        $response = Response::make($contents, '200', [
            'Content-Type' => $attachment->file_mime,
            'Content-Disposition' => 'attachment; filename="'.$attachment->original_name.'"',
        ]);

        return $response;
    }

    public function showthumbnail($id)
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

    public function pullNotice(Request $request)
    {
        $number = intval($request->number);
        $secret = intval($request->secret);
        $job = Job::where('id', $number)->where('secret_key', $secret)->where('deleted_at', null)->first();
        $client = Auth::user()->client;
        if (count($job) == 0) {
            return 0;
        }
        if ($job->client->id == Auth::user()->client_id) {
            return $job->id;
        } else {
            $new_job = $job->replicate();
            $new_job->name = $job->name;
            $new_job->number = null;
            $new_job->started_at = null;
            $new_job->last_day = null;
            $new_job->default_materials = $client->default_materials;
            $new_job->coordinate_id = null;
            $new_job->secret_key = null;
            $new_job->contract_amount = 0.00;

            $new_job->client_id = Auth::user()->client_id;
            $new_job->save();
            $this->createLog((array) $new_job, $new_job);
            foreach ($job->attachments->where('type', 'notice-of-commencement') as $attachment) {
                $new_attachment = $attachment->replicate();
                $new_attachment->attachable_id = $new_job->id;
                $new_attachment->save();
            }
            foreach ($job->attachments->where('type', 'bond') as $attachment) {
                $new_attachment = $attachment->replicate();
                $new_attachment->attachable_id = $new_job->id;
                $new_attachment->save();
            }

            // party type 'client'
            $contact = $client->contacts->where('primary', 2)->first();
            if ($contact) {
                $new_party = new JobParty();
                $new_party->job_id = $new_job->id;
                $new_party->contact_id = $contact->id;
                $new_party->entity_id = $contact->entity_id;
                $new_party->type = 'client';
                $new_party->source = 'CL';
                $new_party->save();
            }

            foreach ($job->parties as $party) {
                if ($party->type == 'client') {
                    continue;
                }

                $new_party = $party->replicate();
                $new_party->job_id = $new_job->id;
                $new_party->source = 'CL';
                $new_party->save();

                if ($new_party->type == 'customer' || $new_party->type == 'sub_contractor') {
                    $new_party->type = 'copy_recipient';
                    $new_party->save();
                }
                $entity = $party->firm;
                $contact = $party->contact;
                if ($entity->client_id != $new_job->client_id) {
                    $entity_count = \App\Entity::where('firm_name', $entity->firm_name)->where('client_id', $new_job->client_id)->count();
                    if ($entity_count > 0) {
                        $existent_entity = \App\Entity::where('firm_name', $entity->firm_name)->where('client_id', $new_job->client_id)->first();
                        $contact_count = \App\ContactInfo::where('entity_id', $existent_entity->id)
                                ->where('first_name', $contact->first_name)
                                ->where('last_name', $contact->last_name)
                                ->where('address_1', $contact->address_1)
                                ->where('address_2', $contact->address_2)->count();
                        if ($contact_count > 0) {
                            // use existent contact and existent entity to recreate job party
                            $existent_contact = \App\ContactInfo::where('entity_id', $existent_entity->id)
                                ->where('first_name', $contact->first_name)
                                ->where('last_name', $contact->last_name)
                                ->where('address_1', $contact->address_1)
                                ->where('address_2', $contact->address_2)->first();
                            $new_party->entity_id = $existent_entity->id;
                            $new_party->contact_id = $existent_contact->id;
                            $new_party->save();
                        } else {
                            // new Contact
                            $new_contact = $contact->replicate();
                            $new_contact->entity_id = $existent_entity->id;
                            $new_contact->save();
                            $new_party->entity_id = $existent_entity->id;
                            $new_party->contact_id = $new_contact->id;
                            $new_party->save();
                        }
                    } else {
                        //does not exist lets replicate entity, contact, Party.
                        $new_entity = $entity->replicate();
                        $new_entity->client_id = $new_job->client_id;
                        $new_entity->save();

                        $new_contact = $contact->replicate();
                        $new_contact->entity_id = $new_entity->id;
                        $new_contact->save();

                        $new_party->entity_id = $new_entity->id;
                        $new_party->contact_id = $new_contact->id;
                        $new_party->save();
                    }
                }

                if (strlen($new_party->bond_pdf) > 0) {
                    $new_path = 'jobparties/bonds/pdfs/job-'.$new_party->job_id.'-party-'.$new_party->id.'.pdf';
                    Storage::copy($new_party->bond_pdf, $new_path);
                    $new_party->bond_pdf = $new_path;
                    $new_party->save();
                }
            }

            $note = new Note();
            $now = Carbon::now();
            $job_address = implode(' ', explode('<br>', $job->full_address));
            $note->note_text = 'Address: '.$job_address.', First Workorder Number:'.(count($job->workorders) > 0 ? $job->workorders[0]->number : '');
            $note->entered_at = $now->toDateTimeString();
            $note->entered_by = Auth::user()->id;
            $note->viewable = 0;
            $note->client_id = $job->client_id;
            $note->noteable_type = 'App\Job';
            $note = $new_job->notes()->save($note);

            return $new_job->id;
        }
    }

    public function getJobWorkorder(Request $request)
    {
        if ($request->has('job_id')) {
            $job_id = $request->job_id;
        } else {
            $job_id = '';
            session()->forget('wizard2.newjob');
        }
        if ($request->has('workorder_id')) {
            $wo_id = $request->workorder_id;
        } else {
            $wo_id = '';
        }
        $wo = WorkOrder::where('id', $wo_id)->first();
        $job = Job::where('id', $job_id)->first();
        $client = Auth::user()->client->load('jobs');
        $jobs = $client->jobs;
        $jobs = $jobs->where('status', '!=', 'closed')->where('deleted_at', null);

        // $jobs = $jobs->pluck('name','id')->prepend( 'New Job From QR Code', '000')->prepend( 'New Job',0);
        $jobs = $jobs->pluck('name', 'id')->toArray();
        $sharedJobIds = SharedJobToUser::where('user_id', Auth::user()->id)->pluck('job_id')->toArray();
        $sharedJobs = Job::whereIn('id', $sharedJobIds)->where('status', '!=', 'closed')->where('deleted_at', null)->pluck('name', 'id')->toArray();
        $jobs = $jobs + $sharedJobs;

        $job_types = [
            'private' => 'Private - Residential, Commercial properties etc',
            'public' => 'Public - Roadwork, Airport, Government buildings etc',
        ];
        $coordinates = Coordinate::where('client_id', Auth::user()->client_id)
                      ->where('deleted_at', null)->pluck('name', 'id')->prepend('', '');

        $wo_types = ['' => ''] + WorkOrderType::all()->pluck('name', 'slug')->toArray();
        $to_delete = [
            'waiver-and-release-of-lien-upon-progress-payment' => 'WAIVER AND RELEASE OF LIEN UPON PROGRESS PAYMENT (Non Conditional)',
            'waiver-and-release-of-lien-upon-final-payment' => 'WAIVER AND RELEASE OF LIEN UPON FINAL PAYMENT (Non Conditional)',
            'conditional-waiver-and-release-of-lien-upon-progress-payment' => 'CONDITIONAL WAIVER AND RELEASE OF LIEN UPON PROGRESS PAYMENT',
            'conditional-waiver-and-release-of-lien-upon-final-payment' => 'CONDITIONAL WAIVER AND RELEASE OF LIEN UPON FINAL PAYMENT ',
        ];

        $wo_types = array_diff_key($wo_types, $to_delete);
        $question_list = WorkOrderFields::where('workorder_type', 'notice-to-owner')->orderBy('field_order')->get();

        $contract_tracker = $request->has('contract_tracker') ? ContractTracker::where('id', $request->contract_tracker)->first() : null;
        if ($contract_tracker && $contract_tracker->is_converted) {
            return redirect()->route('wizard2.getjobworkorder');
        }

        $data = [
            'client_id' => $client->id,
            'jobs' => $jobs,
            'job_id' => $job_id,
            'job_types' => $job_types,
            'counties' => $this->counties,
            'default_materials' => $client->default_materials,
            'coordinates' => $coordinates,
            'job_number' => isset($request['number']) ? $request['number'] : '',
            'secret_key' => isset($request['key']) ? $request['key'] : '',
            'wo_types' => $wo_types,
            'job' => $job,
            'question_list' => $question_list,
            'contract_tracker' => $contract_tracker,
            'wo' => $wo,
        ];

        return view('client.wizard2.job_workorder', $data);
    }

    public function getJobForm($job_id)
    {
        $coordinates = Coordinate::where('client_id', Auth::user()->client_id)
                      ->where('deleted_at', null)->get()->pluck('full_name', 'id')->prepend('', '');
        $job_types = [
            'private' => 'Private - Residential, Commercial properties etc',
            'public' => 'Public - Roadwork, Airport, Government buildings etc',
        ];
        if ($job_id == 0) {
            $data = [
                'job_types' => $job_types,
                'coordinates' => $coordinates,
            ];

            return view('client.wizard2.dynamicforms.jobformempty', $data);
        } else {
            $job = Job::findOrFail($job_id);
            $data = [
                'job' => $job,
                'job_types' => $job_types,
                'coordinates' => $coordinates,
            ];

            return view('client.wizard2.dynamicforms.jobform', $data);
        }
    }

    public function createLog($data, $job)
    {
        $jobfields = ['type', 'client_id', 'number', 'project_number', 'noc_number',
            'name', 'address_source', 'address_1', 'address_2', 'address_corner', 'city',
            'county', 'state', 'zip', 'country', 'started_at', 'last_day', 'status',
            'contract_amount', 'interest_rate', 'default_materials', 'legal_description',
            'folio_number', 'private_type', 'is_mall_unit', 'is_tenant', 'is_condo',
            'association_name', 'a_unit_number', 'mall_name', 'm_unit_number', 'coordinate_id', ];
        $changeArray = [];
        foreach ($jobfields as $field) {
            if (isset($data[$field])) {
                $change['field'] = $field;
                $change['old'] = null;
                $change['new'] = $data[$field];
                $changeArray[] = $change;
            }
        }
        $changes = json_encode($changeArray);
        if (count($changeArray) > 0) {
            JobLog::create([
                'job_id' => $job->id,
                'user_id' => Auth::user()->id,
                'user_name' => Auth::user()->fullName,
                'edited_at' => Carbon::now(),
                'data' => $changes,
                'type' => 'created',
            ]);
        }
    }

    public function postJobWorkorder(Request $request)
    {
        if ($request->has('job_id')) {
            $messages = [
                'date_format' => 'All dates must comply wit the folowing format mm/dd/yyyy',
            ];

            $this->validate($request, [
                'client_id' => 'required',
                'type' => 'required',
                'name' => 'required',
                'started_at' => 'required|date|date_format:m/d/Y',
                'contract_amount' => 'required',
                'address_1' => 'required_if:address_corner,""',
                'city' => 'required',
                'county' => 'required',
                'interest_rate' => 'required',
                'wo_type' => 'required',
            ], $messages);

            $data = $request->all();
            if (strlen($data['started_at']) > 0) {
                $data['started_at'] = date('Y-m-d', strtotime($data['started_at']));
            } else {
                $data['started_at'] = null;
            }
            if (strlen($data['last_day']) > 0) {
                $data['last_day'] = date('Y-m-d', strtotime($data['last_day']));
            } else {
                $data['last_day'] = null;
            }
            $owner_name = $request->owner_name;
            $owner_address_1 = $request->owner_address_1;
            $owner_address_2 = $request->owner_address_2;
            $owner_city = $request->owner_city;
            $owner_state = $request->owner_state;
            $owner_zip = $request->owner_zip;
            if ($request->job_id == 0 && $request->pulled_job_id == 0) {
                $job = Job::create($data);
                $this->createLog($data, $job);
                $job->search_status = 'new';
                $job->save();

                // From Contract tracker
                if ($request->has('contract_tracker')) {
                    $createdFrom = ' from contract tracker.';
                    $contract_tracker = ContractTracker::where('id', $request->contract_tracker)->first();
                    if ($contract_tracker && ! $contract_tracker->is_converted) {
                        $contract_tracker->is_converted = 1;
                        $contract_tracker->save();
                        $job->contract_tracker_id = $contract_tracker->id;
                        $job->save();
                        if ($contract_tracker->contract_file && Storage::disk()->exists($contract_tracker->contract_file)) {
                            $attachment = new Attachment();
                            $file = Storage::get($contract_tracker->contract_file);

                            $attachment->type = 'contract-tracker';
                            $attachment->description = 'Attached from contract tracker';
                            $attachment->original_name = $contract_tracker->file_original_name;
                            $attachment->file_mime = $contract_tracker->file_mime;
                            $attachment->file_size = $contract_tracker->file_size;
                            $attachment->user_id = Auth::user()->id;
                            $job->attachments()->save($attachment);
                            $attachment->save();

                            $xfilename = 'attachment-'.$attachment->id.'.'.$contract_tracker->file_extension;
                            $xpath = 'attachments/jobs/'.$job->id.'/';
                            Storage::put($xpath.$xfilename, $file);
                            $attachment->file_path = $xpath.$xfilename;
                            $attachment->save();

                            switch ($contract_tracker->file_mime) {
                                case 'application/pdf':
                                    $xblob = $file;
                                    $img = new \Imagick();
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
                                    $xblob = $file;
                                    $img = new \Imagick();
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

                            $attachment->clientviewable = 'yes';
                            $attachment->save();
                        }
                    }
                }
                ////////////////////////////////////

                session(['wizard2.newjob' => $job->id]);
                $client = Client::findOrFail($request->client_id);
                $contact = $client->contacts->where('primary', 2)->first();

                if ($contact) {
                    $job_party = new JobParty();
                    $job_party->job_id = $job->id;
                    $job_party->contact_id = $contact->id;
                    $job_party->entity_id = $contact->entity_id;
                    $job_party->type = 'client';
                    $job_party->save();
                }
                //=== pull landowner from owner data on property_records
                if ($owner_name) {
                    $contacts = $client->contacts;
                    $matched = false;

                    foreach ($contacts as $contact) {
                        $entity_contact = Entity::where('id', $contact->entity_id)->first();
                        $first_name = '';
                        $last_name = '';

                        if ($entity_contact->firm_name == strtoupper($owner_name) && $contact->first_name == $first_name && $contact->last_name == $last_name && (substr($contact->address_1, 0, 5) == strtoupper(substr($owner_address_1, 0, 5)) || $contact->address_1 == $owner_address_1) && $contact->city == strtoupper($owner_city) && $contact->zip == $owner_zip) {
                            $entity_contact->latest_type = 'owner';
                            $entity_contact->save();

                            $data['entity_id'] = $entity_contact->id;
                            $data['contact_id'] = $contact->id;
                            $data['type'] = 'landowner';
                            $data['job_id'] = $job->id;
                            $landowner_deed_number = '';
                            $data['source'] = 'OTHR';
                            $data['landowner_deed_number'] = $landowner_deed_number;
                            $newJobParty = JobParty::create($data);

                            $matched = true;
                        }
                    }

                    if (! $matched) {
                        $data['firm_name'] = strtoupper($owner_name);
                        $data['latest_type'] = 'owner';
                        $data['client_id'] = $job->client_id;
                        $data['is_hot'] = 0;
                        $data['hot_id'] = 0;

                        $entity = Entity::create($data);

                        $xdata['first_name'] = '';
                        $xdata['last_name'] = '';
                        $xdata['gender'] = 'none';
                        $xdata['address_1'] = strtoupper($owner_address_1);
                        $xdata['address_2'] = strtoupper($owner_address_2);
                        $xdata['city'] = strtoupper($owner_city);
                        $xdata['state'] = strtoupper($owner_state);
                        $xdata['zip'] = $owner_zip;
                        $xdata['country'] = 'USA';
                        if (strlen($owner_state) > 2) {
                            $xdata['country'] = strtoupper($owner_state);
                            $xdata['state'] = '';
                        }

                        $new_contact = ContactInfo::create($xdata);
                        $new_contact->entity_id = $entity->id;
                        $new_contact->primary = 1;
                        $new_contact->save();

                        $xdata['entity_id'] = $entity->id;
                        $xdata['contact_id'] = $new_contact->id;
                        $xdata['type'] = 'landowner';
                        $xdata['job_id'] = $job->id;

                        $landowner_deed_number = '';
                        $xdata['landowner_deed_number'] = $landowner_deed_number;
                        $newJobParty = JobParty::create($xdata);
                    }
                }
                ////////////////////////////////////////////////////
            } else {
                $job = Job::findOrFail($request->pulled_job_id == 0 ? $request->job_id : $request->pulled_job_id);
                $client = Client::findOrFail($request->client_id);
                $this->authorize('wizard', $job);

                $sharedJobUserIds = array_unique(SharedJobToUser::where('job_id', $job->id)->pluck('user_id')->toArray());
                foreach ($sharedJobUserIds as $userId) {
                    $sharedUser = User::where('id', $userId)->first();
                    if (! $sharedUser) {
                        continue;
                    }
                    Notification::send($sharedUser, new ShareJobSelectedOnWorkorderCreation($job));
                }

                $changeArray = $job->getChanges($data);
                $changes = json_encode($changeArray);
                $job->update($data);
                if (count($changeArray) > 0) {
                    JobLog::create([
                        'job_id' => $job->id,
                        'user_id' => Auth::user()->id,
                        'user_name' => Auth::user()->fullName,
                        'edited_at' => Carbon::now(),
                        'data' => $changes,
                    ]);
                }
                $job->save();
                if ($request->job_id == session('wizard2.newjob')) {
                } else {
                    session(['wizard2.newjob' => 0]);
                    $wo_types = ['' => ''] + WorkOrderType::all()->pluck('name', 'slug')->toArray();
                    $to_delete = [
                        'waiver-and-release-of-lien-upon-progress-payment' => 'WAIVER AND RELEASE OF LIEN UPON PROGRESS PAYMENT (Non Conditional)',
                        'waiver-and-release-of-lien-upon-final-payment' => 'WAIVER AND RELEASE OF LIEN UPON FINAL PAYMENT (Non Conditional)',
                        'conditional-waiver-and-release-of-lien-upon-progress-payment' => 'CONDITIONAL WAIVER AND RELEASE OF LIEN UPON PROGRESS PAYMENT',
                        'conditional-waiver-and-release-of-lien-upon-final-payment' => 'CONDITIONAL WAIVER AND RELEASE OF LIEN UPON FINAL PAYMENT ',
                    ];

                    $wo_types = array_diff_key($wo_types, $to_delete);
                    $data = [
                        'wo_types' => $wo_types,
                        'job' => $job,
                        'wo' => null,
                    ];

                    $hasParty = $job->parties->where('type', '!=', 'client');
                    //if (count($job->parties) == 1) {
                    if (count($hasParty) == 0 || $request->pulled_job_id != 0) {
                    } else {
                        $wo_id = $this->storeWorkorder($request, $job);
                        Session::put('workorder', $wo_id);

                        //return redirect()->route('wizard2.attachments',[$job->id]);
                        return redirect()->route('wizard2.getparties', [$job->id]);
                    }
                }
            }

            // store workorder
            $wo_id = $this->storeWorkorder($request, $job);
            Session::put('workorder', $wo_id);

            //////////////////
            $customer_count = count($job->parties->where('type', 'customer'));
            if ($customer_count > 0) {
                return redirect()->route('wizard2.getparties', $job->id);
            }

            return redirect()->route('wizard2.getemployer', $job->id);
        }

        return redirect()->back();
    }

    public function storeWorkorder($request, $job)
    {
        $data = $request->all();
        if ($request->has('is_rush')) {
            $data['is_rush'] = 1;
        } else {
            $data['is_rush'] = 0;
        }
        $data['due_at'] = date('Y-m-d', strtotime($data['due_at']));
        $data['mailing_at'] = date('Y-m-d', strtotime($data['mailing_at']));
        $data['status'] = 'temporary';
        $data['type'] = $data['wo_type'];

        $wo = WorkOrder::create($data);
        $user_id = Auth::user()->id;
        $wo->created_by = $user_id;
        $wo->responsible_user = $user_id;
        $wo->job_id = $job->id;
        $wo->service = $job->client->service;
        $wo->save();
        $job->status = $wo->type;
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

        if (count($job->workorders) == 1 && $job->search_status == 'new') {
            $job->search_status = 'done';
            $job->save();
            $note = new Note();
            $now = Carbon::now();
            $note_text = 'Original Address: '.$job->address_1.' '.$job->address_2.', '.$job->city.', '.$job->state.' '.$job->zip_code;
            $note->note_text = $note_text;
            $note->entered_at = $now->toDateTimeString();
            $note->entered_by = Auth::user()->id;
            $note->viewable = 1;
            $note->noteable_type = 'App\Job';
            $note->client_id = $job->client->id;
            $note = $job->notes()->save($note);
        }
        $days45ago = date('Y-m-d H:i:s', strtotime('-45days'));
        $unpaid_invoices45 = $job->client->invoices->where('status', '!=', 'paid')->where('created_at', '<', $days45ago);

        if (count($unpaid_invoices45) > 0) {
            $now = Carbon::now();
            $note = Note::create();
            $note->noteable_id = $wo->id;
            $note->noteable_type = 'App\WorkOrder';
            $note->client_id = $job->client->id;
            $note->created_at = date('Y-m-d H:i:s', strtotime($now));
            $note->note_text = 'This client has invoices past due.';
            $note->entered_at = $now->toDateTimeString();
            $note->entered_by = Auth::user()->id;
            $note->viewable = 0;
            $note->save();

            $adminEmail = \App\AdminEmails::where('class', 'PastDueClientEnteredWorkOrder')->first();
            $adminUserIds = explode(',', $adminEmail->users);
            if (count($adminUserIds) > 0 && $adminEmail->users) {
                $admin_users = User::where('status', 1)->whereIn('id', $adminUserIds)->get();
            } else {
                $admin_users = User::where('status', 1)->isRole(['admin', 'researcher'])->get();
            }
            $ndata = [
                'note' => 'Number: '.$wo->number,
                'entered_at' => $wo->created_at->format('Y-m-d H:i:s'),
            ];
            Notification::send($admin_users, new PastDueClientEnteredWorkOrder($wo->id, $ndata, Auth::user()->full_name));
        } else {
            $ndata = [
                'note' => 'Number: '.$wo->number,
                'entered_at' => $wo->created_at->format('Y-m-d H:i:s'),
            ];

            $adminEmail = \App\AdminEmails::where('class', 'NewWorkOrder')->first();
            $adminUserIds = explode(',', $adminEmail->users);
            if (count($adminUserIds) > 0 && $adminEmail->users) {
                $admin_users = User::where('status', 1)->whereIn('id', $adminUserIds)->get();
            } else {
                $admin_users = User::where('status', 1)->isRole(['admin', 'researcher'])->get();
            }
            Notification::send($admin_users, new NewWorkOrder($wo->id, $ndata, Auth::user()->full_name));
        }

        return $wo->id;
    }

    public function additionalForm($party_type)
    {
        switch ($party_type) {
            case 'customer':
                return;
                break;
            case 'general_contractor':
                return;
                break;
            case 'bond':
                return view('client.wizard2.dynamicforms.bond');
                break;
            case 'landowner':
                return view('client.wizard2.dynamicforms.landowner');
                break;
            case 'leaseholder':
                return view('client.wizard2.dynamicforms.leaseholder');
                break;
            case 'copy_recipient':
                return view('client.wizard2.dynamicforms.copy');

                return;
                break;
        }
    }

    public function listcontacts($id, Request $request)
    {
        $search_query = $request->input('term');

        $job = Job::findOrFail($id);

        // $this->authorize('wizard',  $job);

        $remove_contacts = $job->client->contacts->where('hot_id', '<>', 0)->pluck('hot_id');
        $client_contacts = $job->client->contacts->pluck('id')->where('status', 1)->toArray();

        $entities = \App\Entity::search($search_query)->where('client_id', $job->client->id)->get()->pluck('id')->toArray();

        //$entities_local= \App\ContactInfo::search($search_query)->get()->where('status',1)->pluck('entity_id')->toArray();

        $entities_hot = \App\Entity::search($search_query)->where('client_id', 0)->get()->pluck('id')->toArray();

        $entities_hot_all = \App\Entity::where('client_id', 0)->orwhere('client_id', $job->client->id)->get()->pluck('id')->toArray();

        $all_entities = array_merge($entities, $entities_hot);

        $entity_contacts = \App\ContactInfo::whereIn('entity_id', $all_entities)->where('status', 1)->whereNotIn('id', $remove_contacts)->get();

        $contacts = \App\ContactInfo::search($search_query)->get()->where('status', 1)->whereIn('id', $client_contacts)->whereNotIn('id', $remove_contacts);
        $contacts_hot = \App\ContactInfo::search($search_query)->get()->where('status', 1)->where('is_hot', 1)->whereNotIn('id', $remove_contacts);
        $contacts_hot_all = \App\ContactInfo::search($search_query)->get()->where('status', 1)->whereIn('entity_id', $entities_hot_all)->whereNotIn('id', $remove_contacts);

        $all_contacts = $contacts->merge($contacts_hot);
        $all_contacts = $all_contacts->merge($contacts_hot_all);
        $all_contacts = $all_contacts->merge($entity_contacts)->sortBy('name_entity_name')->toArray();

        $all_contacts = array_values($all_contacts);

        $result = [];
        foreach ($all_contacts as $ac) {
            if ($ac['is_hot'] == 1 && $ac['use_on_client'] == 0) {
            } else {
                array_push($result, $ac);
            }
        }

        return json_encode($result);
    }

    public function listcontactsall($id)
    {
        $job = Job::findOrFail($id);

        // $this->authorize('wizard',  $job);

        $contacts = $job->client->contacts->where('status', 1)->where('hot_id', 0);

        $entities = \App\Entity::where('client_id', $job->client->id)->get()->pluck('id')->toArray();
        $entity_contacts = \App\ContactInfo::whereIn('entity_id', $entities)->where('status', 1)->where('hot_id', 0)->get();
        $all_contacts = $contacts->merge($entity_contacts)->sortBy('name_entity_name')->toArray();
        $all_contacts = array_values($all_contacts);

        $result = [];
        foreach ($all_contacts as $ac) {
            if ($ac['is_hot'] == 1 && $ac['use_on_client'] == 0) {
            } else {
                array_push($result, $ac);
            }
        }

        return json_encode($result);
    }

    public function storeParty(Request $request, $job_id)
    {
        $job = Job::findOrFail($job_id);
        $this->authorize('wizard', $job);

        $data = $request->all();
        if ($request->exists('first_name')) {
            $this->validate($request, [
                'job_id' => 'required|exists:jobs,id',
                'type' => 'required',
                'firm_name' => 'required_without_all:first_name,last_name,entity_id',
                'first_name' => 'required_without_all:firm_name,entity_id',
                'last_name' => 'required_without_all:firm_name,entity_id',
                'address_1' => 'required_without:contact_id',
                'city' => 'required_without:contact_id',
                'state' => 'required_without:contact_id',
                'country' => 'required_without:contact_id',
            ]);

            if ($request->has('entity_id')) {
                //dd('por aqui');
                $entity = Entity::findOrFail($request->input('entity_id'));
                $xdata = $request->all();
                if (strlen($xdata['first_name']) == 0) {
                    $xdata['first_name'] = ' ';
                }
                if (strlen($xdata['last_name']) == 0) {
                    $xdata['last_name'] = ' ';
                }

                $contact = ContactInfo::create($xdata);
                $contact->entity_id = $request->input('entity_id');
                $contact->save();
            } else {
                $data['latest_type'] = $request->input('type');
                $entity = Entity::create($request->all());

                $xdata = $request->all();
                if (strlen($xdata['first_name']) == 0) {
                    $xdata['first_name'] = ' ';
                }
                if (strlen($xdata['last_name']) == 0) {
                    $xdata['last_name'] = ' ';
                }

                $contact = ContactInfo::create($xdata);

                $contact->entity_id = $entity->id;
                $contact->primary = 1;
                $contact->save();

                if ($request->input('firm_name') == '') {
                    $entity->firm_name = trim($contact->first_name.' '.$contact->last_name);
                    $entity->save();
                }
            }
            $data['contact_id'] = $contact->id;
        } else {
            $this->validate($request, [
                'job_id' => 'required|exists:jobs,id',
                'type' => 'required',
                'contact_id' => 'required',
            ]);

            $contact = ContactInfo::findOrFail($data['contact_id']);

            if ($contact->is_hot) {
                $xjob = Job::find($job_id);
                $existent_contact = $xjob->client->contacts()->where('contact_infos.hot_id', $contact->id)->first();
                if ($existent_contact) {
                    $contact = $existent_contact;
                } else {
                    $existent_entity = Entity::where([['hot_id', $contact->entity->id], ['client_id', $xjob->client_id]])->first();
                    if ($existent_entity) {
                        $entity = $existent_entity;
                    } else {
                        $entity = new Entity();
                        $entity = $contact->entity->replicate();
                        $entity->client_id = $xjob->client->id;
                        $entity->hot_id = $contact->entity->id;
                        $entity->save();
                    }
                    $new_contact = new ContactInfo();
                    $new_contact = $contact->replicate();
                    $new_contact->hot_id = $contact->id;
                    $new_contact->entity_id = $entity->id;
                    $new_contact->save();
                    $new_contact->refresh();

                    $contact = $new_contact;
                }
                $data['contact_id'] = $contact->id;
            }
        }
        $contact->refresh();
        $entity = $contact->entity;

        $data['entity_id'] = $entity->id;

        if ($request->type == 'copy_recipient') {
            if ($request->copy_recipient_type == 'other') {
                $data['copy_type'] = $request->other_copy_recipient_type;
            } else {
                $data['copy_type'] = $request->copy_recipient_type;
            }
        }

        if ($request->type == 'leaseholder') {
            if ($request->leaseholder_type == 'Lessee') {
                $data['leaseholder_bookpage_number'] = null;
            } else {
            }
        }

        if ($request->has('lien_prohibition')) {
            $data['landowner_lien_prohibition'] = 1;
        }

        if ($request->has('bond_date')) {
            if (strlen($data['bond_date']) > 0) {
                $data['bond_date'] = date('Y-m-d H:i:s', strtotime($data['bond_date']));
            } else {
                $data['bond_date'] = null;
            }
        }
        $JobParty = JobParty::create($data);
        $JobParty->source = 'CL';
        $JobParty->save();

        if ($request->hasFile('bond_pdf')) {
            if ($request->file('bond_pdf')->isValid()) {
                $f = $request->file('bond_pdf');
                $xfilename = 'job-'.$job_id.'-party-'.$JobParty->id.'.'.$f->guessExtension();
                $xpath = 'jobparties/bonds/pdfs';
                $f->storeAs($xpath, $xfilename);
                $JobParty->bond_pdf_filename = $f->getClientOriginalName();
                $JobParty->bond_pdf = $xpath.'/'.$xfilename;
                $JobParty->bond_pdf_filename_mime = $f->getMimeType();
                $JobParty->bond_pdf_filename_size = $f->getSize();
                $JobParty->save();
            } else {
                Session::flash('message', 'PDF file not uploaded correctly');

                return redirect()->back()->withInput();
            }
        }

        Session::flash('message', 'Job party '.$JobParty->contact->full_name.' successfully created.');

        return redirect()->route('wizard2.getparties', $job_id);
    }

    public function storeEmployer(Request $request, $job_id)
    {
        $job = Job::findOrFail($job_id);
        if (! isset($request->from_page) || $request->from_page != 'provide_info') {
            $this->authorize('wizard', $job);
        }

        $data = $request->all();

        if ($request->exists('first_name')) {
            $this->validate($request, [
                'job_id' => 'required|exists:jobs,id',
                'type' => 'required',
                'firm_name' => 'required_without_all:first_name,last_name,entity_id',
                'first_name' => 'required_without_all:firm_name,entity_id',
                'last_name' => 'required_without_all:firm_name,entity_id',
                'address_1' => 'required_without:contact_id',
                'city' => 'required_without:contact_id',
                'state' => 'required_without:contact_id',
                'country' => 'required_without:contact_id',
            ]);

            if ($request->has('entity_id')) {
                //dd('por aqui');
                $entity = Entity::findOrFail($request->input('entity_id'));
                $xdata = $request->all();
                if (strlen($xdata['first_name']) == 0) {
                    $xdata['first_name'] = ' ';
                }
                if (strlen($xdata['last_name']) == 0) {
                    $xdata['last_name'] = ' ';
                }

                $contact = ContactInfo::create($xdata);
                $contact->entity_id = $request->input('entity_id');
                $contact->save();
            } else {
                $data['latest_type'] = $request->input('type');
                $entity = Entity::create($request->all());

                $xdata = $request->all();
                if (strlen($xdata['first_name']) == 0) {
                    $xdata['first_name'] = ' ';
                }
                if (strlen($xdata['last_name']) == 0) {
                    $xdata['last_name'] = ' ';
                }

                $contact = ContactInfo::create($xdata);

                $contact->entity_id = $entity->id;
                $contact->primary = 1;
                $contact->save();

                if ($request->input('firm_name') == '') {
                    $entity->firm_name = trim($contact->first_name.' '.$contact->last_name);
                    $entity->save();
                }
            }
            $data['contact_id'] = $contact->id;
        } else {
            $this->validate($request, [
                'job_id' => 'required|exists:jobs,id',
                'type' => 'required',
                'contact_id' => 'required',
            ]);

            $contact = ContactInfo::findOrFail($data['contact_id']);

            if ($contact->is_hot) {
                $xjob = Job::find($job_id);
                $existent_contact = $xjob->client->contacts()->where('contact_infos.hot_id', $contact->id)->first();
                if ($existent_contact) {
                    $contact = $existent_contact;
                } else {
                    $existent_entity = Entity::where([['hot_id', $contact->entity->id], ['client_id', $xjob->client_id]])->first();
                    if ($existent_entity) {
                        $entity = $existent_entity;
                    } else {
                        $entity = new Entity();
                        $entity = $contact->entity->replicate();
                        $entity->client_id = $xjob->client->id;
                        $entity->hot_id = $contact->entity->id;
                        $entity->save();
                    }
                    $new_contact = new ContactInfo();
                    $new_contact = $contact->replicate();
                    $new_contact->hot_id = $contact->id;
                    $new_contact->entity_id = $entity->id;
                    $new_contact->save();
                    $new_contact->refresh();

                    $contact = $new_contact;
                }
                $data['contact_id'] = $contact->id;
            }
        }
        $contact->refresh();
        $entity = $contact->entity;

        $data['entity_id'] = $entity->id;

        if ($request->type == 'copy_recipient') {
            if ($request->copy_recipient_type == 'other') {
                $data['copy_type'] = $request->other_copy_recipient_type;
            } else {
                $data['copy_type'] = $request->copy_recipient_type;
            }
        }

        if (in_array($request->type, ['architect', 'condo assoc', 'Developer', 'engineer', 'government agency', 'homeowners assoc', 'management co', 'surveying co', 'other'])) {
            $data['type'] = 'copy_recipient';
            if ($request->type == 'other') {
                $data['copy_type'] = $request->other_copy_recipient_type;
            } else {
                $data['copy_type'] = $request->type;
            }
        }

        if ($request->type == 'leaseholder') {
            if ($request->leaseholder_type == 'Lessee') {
                $data['leaseholder_bookpage_number'] = null;
            } else {
            }
        }

        if ($request->has('lien_prohibition')) {
            $data['landowner_lien_prohibition'] = 1;
        }

        if ($data['type'] != 'copy_recipient') {
            $JobParty = JobParty::where('job_id', $data['job_id'])->where('type', $data['type'])->where('contact_id', $data['contact_id'])->where('entity_id', $data['entity_id'])->first();
        } else {
            $JobParty = JobParty::where('job_id', $data['job_id'])->where('copy_type', $data['copy_type'])->where('contact_id', $data['contact_id'])->where('entity_id', $data['entity_id'])->first();
        }

        if ($request->has('bond_date')) {
            if (strlen($data['bond_date']) > 0) {
                $data['bond_date'] = date('Y-m-d H:i:s', strtotime($data['bond_date']));
            } else {
                $data['bond_date'] = null;
            }
        }
        if (count($JobParty) == 0) {
            $JobParty = JobParty::create($data);
        }

        if ($request->hasFile('bond_pdf')) {
            if ($request->file('bond_pdf')->isValid()) {
                $f = $request->file('bond_pdf');
                $xfilename = 'job-'.$job_id.'-party-'.$JobParty->id.'.'.$f->guessExtension();
                $xpath = 'jobparties/bonds/pdfs';
                $f->storeAs($xpath, $xfilename);
                $JobParty->bond_pdf_filename = $f->getClientOriginalName();
                $JobParty->bond_pdf = $xpath.'/'.$xfilename;
                $JobParty->bond_pdf_filename_mime = $f->getMimeType();
                $JobParty->bond_pdf_filename_size = $f->getSize();
                $JobParty->save();
            } else {
                Session::flash('message', 'PDF file not uploaded correctly');

                return redirect()->back()->withInput();
            }
        }

        Session::flash('message', 'Employer '.$JobParty->contact->full_name.' successfully created.');

        $data['type'] = 'customer';
        $JobParty = JobParty::where('job_id', $data['job_id'])->where('type', $data['type'])->where('contact_id', $data['contact_id'])->where('entity_id', $data['entity_id'])->first();
        if (count($JobParty) == 0) {
            $JobParty = JobParty::create($data);
        }

        $landowner_count = count($job->parties->where('type', 'landowner'));
        //$customer_count=count($job->parties->where('type','customer'));
        if ($request->type == 'landowner' || $landowner_count > 0) {
            if (! isset($request->from_page) || $request->from_page != 'provide_info') {
                return redirect()->route('wizard2.getparties', $job->id);
            } else {
                return redirect()->back();
            }

//            if ($request->exists('is_bonded')) {
//
//                if($request->is_bonded == "true") {
//                    return redirect()->route('wizard2.getbond',$job->id);
//                } else {
//                    return redirect()->route('wizard2.getparties',$job->id);
//                }
//
//            } else {
//                if($request->work_for == "landowner") {
//                    return redirect()->route('wizard2.getlandowner',$job->id);
//                } else {
//                    // Go to Add Leaseholder
//                    return redirect()->route('wizard2.getleaseholder',$job->id);
//                }
//            }
        } else {
            if (! isset($request->from_page) || $request->from_page != 'provide_info') {
                return redirect()->route('wizard2.getlandowner', $job->id);
            } else {
                return redirect()->back();
            }
            //return redirect()->route('wizard2.getgc',$job->id);
        }
    }

    public function storeGc(Request $request, $job_id)
    {
        $job = Job::findOrFail($job_id);
        $this->authorize('wizard', $job);

        $data = $request->all();

        if ($request->exists('first_name')) {
            $this->validate($request, [
                'job_id' => 'required|exists:jobs,id',
                'type' => 'required',
                'firm_name' => 'required_without_all:first_name,last_name,entity_id',
                'first_name' => 'required_without_all:firm_name,entity_id',
                'last_name' => 'required_without_all:firm_name,entity_id',
                'address_1' => 'required_without:contact_id',
                'city' => 'required_without:contact_id',
                'state' => 'required_without:contact_id',
                'country' => 'required_without:contact_id',
            ]);

            if ($request->has('entity_id')) {
                //dd('por aqui');
                $entity = Entity::findOrFail($request->input('entity_id'));
                $xdata = $request->all();
                if (strlen($xdata['first_name']) == 0) {
                    $xdata['first_name'] = ' ';
                }
                if (strlen($xdata['last_name']) == 0) {
                    $xdata['last_name'] = ' ';
                }

                $contact = ContactInfo::create($xdata);
                $contact->entity_id = $request->input('entity_id');
                $contact->save();
            } else {
                $data['latest_type'] = $request->input('type');
                $entity = Entity::create($request->all());

                $xdata = $request->all();
                if (strlen($xdata['first_name']) == 0) {
                    $xdata['first_name'] = ' ';
                }
                if (strlen($xdata['last_name']) == 0) {
                    $xdata['last_name'] = ' ';
                }

                $contact = ContactInfo::create($xdata);

                $contact->entity_id = $entity->id;
                $contact->primary = 1;
                $contact->save();

                if ($request->input('firm_name') == '') {
                    $entity->firm_name = trim($contact->first_name.' '.$contact->last_name);
                    $entity->save();
                }
            }
            $data['contact_id'] = $contact->id;
        } else {
            $this->validate($request, [
                'job_id' => 'required|exists:jobs,id',
                'type' => 'required',
                'contact_id' => 'required',
            ]);

            $contact = ContactInfo::findOrFail($data['contact_id']);

            if ($contact->is_hot) {
                $xjob = Job::find($job_id);
                $existent_contact = $xjob->client->contacts()->where('contact_infos.hot_id', $contact->id)->first();
                if ($existent_contact) {
                    $contact = $existent_contact;
                } else {
                    $existent_entity = Entity::where([['hot_id', $contact->entity->id], ['client_id', $xjob->client_id]])->first();
                    if ($existent_entity) {
                        $entity = $existent_entity;
                    } else {
                        $entity = new Entity();
                        $entity = $contact->entity->replicate();
                        $entity->client_id = $xjob->client->id;
                        $entity->hot_id = $contact->entity->id;
                        $entity->save();
                    }
                    $new_contact = new ContactInfo();
                    $new_contact = $contact->replicate();
                    $new_contact->hot_id = $contact->id;
                    $new_contact->entity_id = $entity->id;
                    $new_contact->save();
                    $new_contact->refresh();

                    $contact = $new_contact;
                }
                $data['contact_id'] = $contact->id;
            }
        }
        $contact->refresh();
        $entity = $contact->entity;

        $data['entity_id'] = $entity->id;

        if ($request->type == 'copy_recipient') {
            if ($request->copy_recipient_type == 'other') {
                $data['copy_type'] = $request->other_copy_recipient_type;
            } else {
                $data['copy_type'] = $request->copy_recipient_type;
            }
        }

        if ($request->type == 'leaseholder') {
            if ($request->leaseholder_type == 'Lessee') {
                $data['leaseholder_bookpage_number'] = null;
            } else {
            }
        }

        if ($request->has('lien_prohibition')) {
            $data['landowner_lien_prohibition'] = 1;
        }

        $JobParty = JobParty::where('job_id', $data['job_id'])->where('type', $data['type'])->where('contact_id', $data['contact_id'])->where('entity_id', $data['entity_id'])->first();
        if (count($JobParty) == 0) {
            $JobParty = JobParty::create($data);
        }

        if ($request->hasFile('bond_pdf')) {
            if ($request->file('bond_pdf')->isValid()) {
                $f = $request->file('bond_pdf');
                $xfilename = 'job-'.$job_id.'-party-'.$JobParty->id.'.'.$f->guessExtension();
                $xpath = 'jobparties/bonds/pdfs';
                $f->storeAs($xpath, $xfilename);
                $JobParty->bond_pdf_filename = $f->getClientOriginalName();
                $JobParty->bond_pdf = $xpath.'/'.$xfilename;
                $JobParty->bond_pdf_filename_mime = $f->getMimeType();
                $JobParty->bond_pdf_filename_size = $f->getSize();
                $JobParty->save();
            } else {
                Session::flash('message', 'PDF file not uploaded correctly');

                return redirect()->back()->withInput();
            }
        }

        if ($request->has('bond_date')) {
            if (strlen($data['bond_date']) > 0) {
                $JobParty->bond_date = date('Y-m-d', strtotime($data['bond_date']));
            } else {
                $JobParty->bond_date = null;
            }
            $JobParty->save();
        }

        Session::flash('message', 'General Contractor '.$JobParty->contact->full_name.' successfully created.');

        if ($request->exists('is_bonded')) {
            if ($request->is_bonded == 'true') {
                return redirect()->route('wizard2.getbond', $job->id);
            } else {
                return redirect()->route('wizard2.getparties', $job->id);
            }
        } else {
            if ($request->work_for == 'landowner') {
                return redirect()->route('wizard2.getlandowner', $job->id);
            } else {
                return redirect()->route('wizard2.getleaseholder', $job->id);
            }
        }
    }

    public function storeLandowner(Request $request, $job_id)
    {
        $job = Job::findOrFail($job_id);
        $this->authorize('wizard', $job);

        $data = $request->all();
        if ($request->know == 'no') {
            Session::flash('message', 'Property Owner not specified');
        } else {
            if ($request->exists('first_name')) {
                $this->validate($request, [
                    'job_id' => 'required|exists:jobs,id',
                    'type' => 'required',
                    'firm_name' => 'required_without_all:first_name,last_name,entity_id',
                    'first_name' => 'required_without_all:firm_name,entity_id',
                    'last_name' => 'required_without_all:firm_name,entity_id',
                    'address_1' => 'required_without:contact_id',
                    'city' => 'required_without:contact_id',
                    'state' => 'required_without:contact_id',
                    'country' => 'required_without:contact_id',
                ]);

                if ($request->has('entity_id')) {
                    //dd('por aqui');
                    $entity = Entity::findOrFail($request->input('entity_id'));
                    $xdata = $request->all();
                    if (strlen($xdata['first_name']) == 0) {
                        $xdata['first_name'] = ' ';
                    }
                    if (strlen($xdata['last_name']) == 0) {
                        $xdata['last_name'] = ' ';
                    }

                    $contact = ContactInfo::create($xdata);
                    $contact->entity_id = $request->input('entity_id');
                    $contact->save();
                } else {
                    $data['latest_type'] = $request->input('type');
                    $entity = Entity::create($request->all());

                    $xdata = $request->all();
                    if (strlen($xdata['first_name']) == 0) {
                        $xdata['first_name'] = ' ';
                    }
                    if (strlen($xdata['last_name']) == 0) {
                        $xdata['last_name'] = ' ';
                    }

                    $contact = ContactInfo::create($xdata);

                    $contact->entity_id = $entity->id;
                    $contact->primary = 1;
                    $contact->save();

                    if ($request->input('firm_name') == '') {
                        $entity->firm_name = trim($contact->first_name.' '.$contact->last_name);
                        $entity->save();
                    }
                }
                $data['contact_id'] = $contact->id;
            } else {
                $this->validate($request, [
                    'job_id' => 'required|exists:jobs,id',
                    'type' => 'required',
                    'contact_id' => 'required',
                ]);

                $contact = ContactInfo::findOrFail($data['contact_id']);

                if ($contact->is_hot) {
                    $xjob = Job::find($job_id);
                    $existent_contact = $xjob->client->contacts()->where('contact_infos.hot_id', $contact->id)->first();
                    if ($existent_contact) {
                        $contact = $existent_contact;
                    } else {
                        $existent_entity = Entity::where([['hot_id', $contact->entity->id], ['client_id', $xjob->client_id]])->first();
                        if ($existent_entity) {
                            $entity = $existent_entity;
                        } else {
                            $entity = new Entity();
                            $entity = $contact->entity->replicate();
                            $entity->client_id = $xjob->client->id;
                            $entity->hot_id = $contact->entity->id;
                            $entity->save();
                        }
                        $new_contact = new ContactInfo();
                        $new_contact = $contact->replicate();
                        $new_contact->hot_id = $contact->id;
                        $new_contact->entity_id = $entity->id;
                        $new_contact->save();
                        $new_contact->refresh();

                        $contact = $new_contact;
                    }
                    $data['contact_id'] = $contact->id;
                }
            }
            $contact->refresh();
            $entity = $contact->entity;

            $data['entity_id'] = $entity->id;

            if ($request->type == 'copy_recipient') {
                if ($request->copy_recipient_type == 'other') {
                    $data['copy_type'] = $request->other_copy_recipient_type;
                } else {
                    $data['copy_type'] = $request->copy_recipient_type;
                }
            }

            if ($request->type == 'leaseholder') {
                if ($request->leaseholder_type == 'Lessee') {
                    $data['leaseholder_bookpage_number'] = null;
                } else {
                }
            }

            if ($request->has('lien_prohibition')) {
                $data['landowner_lien_prohibition'] = 1;
            }

            if ($request->has('bond_date')) {
                if (strlen($data['bond_date']) > 0) {
                    $data['bond_date'] = date('Y-m-d H:i:s', strtotime($data['bond_date']));
                } else {
                    $data['bond_date'] = null;
                }
            }
            $JobParty = JobParty::create($data);

            if ($request->hasFile('bond_pdf')) {
                if ($request->file('bond_pdf')->isValid()) {
                    $f = $request->file('bond_pdf');
                    $xfilename = 'job-'.$job_id.'-party-'.$JobParty->id.'.'.$f->guessExtension();
                    $xpath = 'jobparties/bonds/pdfs';
                    $f->storeAs($xpath, $xfilename);
                    $JobParty->bond_pdf_filename = $f->getClientOriginalName();
                    $JobParty->bond_pdf = $xpath.'/'.$xfilename;
                    $JobParty->bond_pdf_filename_mime = $f->getMimeType();
                    $JobParty->bond_pdf_filename_size = $f->getSize();
                    $JobParty->save();
                } else {
                    Session::flash('message', 'PDF file not uploaded correctly');

                    return redirect()->back()->withInput();
                }
            }

            Session::flash('message', 'Property Owner '.$JobParty->contact->full_name.' successfully created.');
        }

        return redirect()->route('wizard2.getother', $job->id);
    }

    public function storeOther(Request $request, $job_id)
    {
        $job = Job::findOrFail($job_id);
        $this->authorize('wizard', $job);

        $data = $request->all();
        if ($request->know == 'no') {
            Session::flash('message', 'Other Recipient Added');

            return redirect()->route('wizard2.getparties', $job->id);
        } else {
            if ($request->exists('first_name')) {
                $this->validate($request, [
                    'job_id' => 'required|exists:jobs,id',
                    'type' => 'required',
                    'firm_name' => 'required_without_all:first_name,last_name,entity_id',
                    'first_name' => 'required_without_all:firm_name,entity_id',
                    'last_name' => 'required_without_all:firm_name,entity_id',
                    'address_1' => 'required_without:contact_id',
                    'city' => 'required_without:contact_id',
                    'state' => 'required_without:contact_id',
                    'country' => 'required_without:contact_id',
                ]);

                if ($request->has('entity_id')) {
                    //dd('por aqui');
                    $entity = Entity::findOrFail($request->input('entity_id'));
                    $xdata = $request->all();
                    if (strlen($xdata['first_name']) == 0) {
                        $xdata['first_name'] = ' ';
                    }
                    if (strlen($xdata['last_name']) == 0) {
                        $xdata['last_name'] = ' ';
                    }

                    $contact = ContactInfo::create($xdata);
                    $contact->entity_id = $request->input('entity_id');
                    $contact->save();
                } else {
                    $data['latest_type'] = $request->input('type');
                    $entity = Entity::create($request->all());

                    $xdata = $request->all();
                    if (strlen($xdata['first_name']) == 0) {
                        $xdata['first_name'] = ' ';
                    }
                    if (strlen($xdata['last_name']) == 0) {
                        $xdata['last_name'] = ' ';
                    }

                    $contact = ContactInfo::create($xdata);

                    $contact->entity_id = $entity->id;
                    $contact->primary = 1;
                    $contact->save();

                    if ($request->input('firm_name') == '') {
                        $entity->firm_name = trim($contact->first_name.' '.$contact->last_name);
                        $entity->save();
                    }
                }
                $data['contact_id'] = $contact->id;
            } else {
                $this->validate($request, [
                    'job_id' => 'required|exists:jobs,id',
                    'type' => 'required',
                    'contact_id' => 'required',
                ]);

                $contact = ContactInfo::findOrFail($data['contact_id']);

                if ($contact->is_hot) {
                    $xjob = Job::find($job_id);
                    $existent_contact = $xjob->client->contacts()->where('contact_infos.hot_id', $contact->id)->first();
                    if ($existent_contact) {
                        $contact = $existent_contact;
                    } else {
                        $existent_entity = Entity::where([['hot_id', $contact->entity->id], ['client_id', $xjob->client_id]])->first();
                        if ($existent_entity) {
                            $entity = $existent_entity;
                        } else {
                            $entity = new Entity();
                            $entity = $contact->entity->replicate();
                            $entity->client_id = $xjob->client->id;
                            $entity->hot_id = $contact->entity->id;
                            $entity->save();
                        }
                        $new_contact = new ContactInfo();
                        $new_contact = $contact->replicate();
                        $new_contact->hot_id = $contact->id;
                        $new_contact->entity_id = $entity->id;
                        $new_contact->save();
                        $new_contact->refresh();

                        $contact = $new_contact;
                    }
                    $data['contact_id'] = $contact->id;
                }
            }
            $contact->refresh();
            $entity = $contact->entity;

            $data['entity_id'] = $entity->id;

            $data['type'] = 'copy_recipient';
            $JobParty = JobParty::create($data);

            Session::flash('message', 'Copy Recipient '.$JobParty->contact->full_name.' successfully created.');

            return redirect()->route('wizard2.getother', $job->id);
        }
    }

    public function storeLeaseholder(Request $request, $job_id)
    {
        $job = Job::findOrFail($job_id);
        $this->authorize('wizard', $job);

        $data = $request->all();

        if ($request->exists('first_name')) {
            $this->validate($request, [
                'job_id' => 'required|exists:jobs,id',
                'type' => 'required',
                'firm_name' => 'required_without_all:first_name,last_name,entity_id',
                'first_name' => 'required_without_all:firm_name,entity_id',
                'last_name' => 'required_without_all:firm_name,entity_id',
                'address_1' => 'required_without:contact_id',
                'city' => 'required_without:contact_id',
                'state' => 'required_without:contact_id',
                'country' => 'required_without:contact_id',
            ]);

            if ($request->has('entity_id')) {
                //dd('por aqui');
                $entity = Entity::findOrFail($request->input('entity_id'));
                $xdata = $request->all();
                if (strlen($xdata['first_name']) == 0) {
                    $xdata['first_name'] = ' ';
                }
                if (strlen($xdata['last_name']) == 0) {
                    $xdata['last_name'] = ' ';
                }

                $contact = ContactInfo::create($xdata);
                $contact->entity_id = $request->input('entity_id');
                $contact->save();
            } else {
                $data['latest_type'] = $request->input('type');
                $entity = Entity::create($request->all());

                $xdata = $request->all();
                if (strlen($xdata['first_name']) == 0) {
                    $xdata['first_name'] = ' ';
                }
                if (strlen($xdata['last_name']) == 0) {
                    $xdata['last_name'] = ' ';
                }

                $contact = ContactInfo::create($xdata);

                $contact->entity_id = $entity->id;
                $contact->primary = 1;
                $contact->save();

                if ($request->input('firm_name') == '') {
                    $entity->firm_name = trim($contact->first_name.' '.$contact->last_name);
                    $entity->save();
                }
            }
            $data['contact_id'] = $contact->id;
        } else {
            $this->validate($request, [
                'job_id' => 'required|exists:jobs,id',
                'type' => 'required',
                'contact_id' => 'required',
            ]);

            $contact = ContactInfo::findOrFail($data['contact_id']);

            if ($contact->is_hot) {
                $xjob = Job::find($job_id);
                $existent_contact = $xjob->client->contacts()->where('contact_infos.hot_id', $contact->id)->first();
                if ($existent_contact) {
                    $contact = $existent_contact;
                } else {
                    $existent_entity = Entity::where([['hot_id', $contact->entity->id], ['client_id', $xjob->client_id]])->first();
                    if ($existent_entity) {
                        $entity = $existent_entity;
                    } else {
                        $entity = new Entity();
                        $entity = $contact->entity->replicate();
                        $entity->client_id = $xjob->client->id;
                        $entity->hot_id = $contact->entity->id;
                        $entity->save();
                    }
                    $new_contact = new ContactInfo();
                    $new_contact = $contact->replicate();
                    $new_contact->hot_id = $contact->id;
                    $new_contact->entity_id = $entity->id;
                    $new_contact->save();
                    $new_contact->refresh();

                    $contact = $new_contact;
                }
                $data['contact_id'] = $contact->id;
            }
        }
        $contact->refresh();
        $entity = $contact->entity;

        $data['entity_id'] = $entity->id;

        if ($request->type == 'copy_recipient') {
            if ($request->copy_recipient_type == 'other') {
                $data['copy_type'] = $request->other_copy_recipient_type;
            } else {
                $data['copy_type'] = $request->copy_recipient_type;
            }
        }

        if ($request->type == 'leaseholder') {
            if ($request->leaseholder_type == 'Lessee') {
                $data['leaseholder_bookpage_number'] = null;
            } else {
            }
        }

        if ($request->has('lien_prohibition')) {
            $data['landowner_lien_prohibition'] = 1;
        }

        if ($request->has('bond_date')) {
            if (strlen($data['bond_date']) > 0) {
                $data['bond_date'] = date('Y-m-d H:i:s', strtotime($data['bond_date']));
            } else {
                $data['bond_date'] = null;
            }
        }
        $JobParty = JobParty::create($data);

        if ($request->hasFile('bond_pdf')) {
            if ($request->file('bond_pdf')->isValid()) {
                $f = $request->file('bond_pdf');
                $xfilename = 'job-'.$job_id.'-party-'.$JobParty->id.'.'.$f->guessExtension();
                $xpath = 'jobparties/bonds/pdfs';
                $f->storeAs($xpath, $xfilename);
                $JobParty->bond_pdf_filename = $f->getClientOriginalName();
                $JobParty->bond_pdf = $xpath.'/'.$xfilename;
                $JobParty->bond_pdf_filename_mime = $f->getMimeType();
                $JobParty->bond_pdf_filename_size = $f->getSize();
                $JobParty->save();
            } else {
                Session::flash('message', 'PDF file not uploaded correctly');

                return redirect()->back()->withInput();
            }
        }

        Session::flash('message', 'Property Owner '.$JobParty->contact->full_name.' successfully created.');

        return redirect()->route('wizard2.getlandowner', $job->id);
    }

    public function storeBond(Request $request, $job_id)
    {
        $job = Job::findOrFail($job_id);
        $this->authorize('wizard', $job);

        $data = $request->all();

        if ($request->exists('first_name')) {
            $this->validate($request, [
                'job_id' => 'required|exists:jobs,id',
                'type' => 'required',
                'firm_name' => 'required_without_all:first_name,last_name,entity_id',
                'first_name' => 'required_without_all:firm_name,entity_id',
                'last_name' => 'required_without_all:firm_name,entity_id',
                'address_1' => 'required_without:contact_id',
                'city' => 'required_without:contact_id',
                'state' => 'required_without:contact_id',
                'country' => 'required_without:contact_id',
            ]);

            if ($request->has('entity_id')) {
                //dd('por aqui');
                $entity = Entity::findOrFail($request->input('entity_id'));
                $xdata = $request->all();
                if (strlen($xdata['first_name']) == 0) {
                    $xdata['first_name'] = ' ';
                }
                if (strlen($xdata['last_name']) == 0) {
                    $xdata['last_name'] = ' ';
                }

                $contact = ContactInfo::create($xdata);
                $contact->entity_id = $request->input('entity_id');
                $contact->save();
            } else {
                $data['latest_type'] = $request->input('type');
                $entity = Entity::create($request->all());

                $xdata = $request->all();
                if (strlen($xdata['first_name']) == 0) {
                    $xdata['first_name'] = ' ';
                }
                if (strlen($xdata['last_name']) == 0) {
                    $xdata['last_name'] = ' ';
                }

                $contact = ContactInfo::create($xdata);

                $contact->entity_id = $entity->id;
                $contact->primary = 1;
                $contact->save();

                if ($request->input('firm_name') == '') {
                    $entity->firm_name = trim($contact->first_name.' '.$contact->last_name);
                    $entity->save();
                }
            }
            $data['contact_id'] = $contact->id;
        } else {
            $this->validate($request, [
                'job_id' => 'required|exists:jobs,id',
                'type' => 'required',
                'contact_id' => 'required',
            ]);

            $contact = ContactInfo::findOrFail($data['contact_id']);

            if ($contact->is_hot) {
                $xjob = Job::find($job_id);
                $existent_contact = $xjob->client->contacts()->where('contact_infos.hot_id', $contact->id)->first();
                if ($existent_contact) {
                    $contact = $existent_contact;
                } else {
                    $existent_entity = Entity::where([['hot_id', $contact->entity->id], ['client_id', $xjob->client_id]])->first();
                    if ($existent_entity) {
                        $entity = $existent_entity;
                    } else {
                        $entity = new Entity();
                        $entity = $contact->entity->replicate();
                        $entity->client_id = $xjob->client->id;
                        $entity->hot_id = $contact->entity->id;
                        $entity->save();
                    }
                    $new_contact = new ContactInfo();
                    $new_contact = $contact->replicate();
                    $new_contact->hot_id = $contact->id;
                    $new_contact->entity_id = $entity->id;
                    $new_contact->save();
                    $new_contact->refresh();

                    $contact = $new_contact;
                }
                $data['contact_id'] = $contact->id;
            }
        }
        $contact->refresh();
        $entity = $contact->entity;

        $data['entity_id'] = $entity->id;

        if ($request->type == 'copy_recipient') {
            if ($request->copy_recipient_type == 'other') {
                $data['copy_type'] = $request->other_copy_recipient_type;
            } else {
                $data['copy_type'] = $request->copy_recipient_type;
            }
        }

        if ($request->type == 'leaseholder') {
            if ($request->leaseholder_type == 'Lessee') {
                $data['leaseholder_bookpage_number'] = null;
            } else {
            }
        }

        if ($request->has('lien_prohibition')) {
            $data['landowner_lien_prohibition'] = 1;
        }

        if ($request->has('bond_date')) {
            if (strlen($data['bond_date']) > 0) {
                $data['bond_date'] = date('Y-m-d H:i:s', strtotime($data['bond_date']));
            } else {
                $data['bond_date'] = null;
            }
        }
        $JobParty = JobParty::create($data);

        if ($request->hasFile('bond_pdf')) {
            if ($request->file('bond_pdf')->isValid()) {
                $f = $request->file('bond_pdf');
                $xfilename = 'job-'.$job_id.'-party-'.$JobParty->id.'.'.$f->guessExtension();
                $xpath = 'jobparties/bonds/pdfs';
                $f->storeAs($xpath, $xfilename);
                $JobParty->bond_pdf_filename = $f->getClientOriginalName();
                $JobParty->bond_pdf = $xpath.'/'.$xfilename;
                $JobParty->bond_pdf_filename_mime = $f->getMimeType();
                $JobParty->bond_pdf_filename_size = $f->getSize();
                $JobParty->save();
            } else {
                Session::flash('message', 'PDF file not uploaded correctly');

                return redirect()->back()->withInput();
            }
        }

        Session::flash('message', 'Bond'.$JobParty->contact->full_name.' successfully created.');

        return redirect()->route('wizard2.getparties', $job->id);
    }

    public function editParty($job_id, $id)
    {
        $job = Job::findOrFail($job_id);
        $this->authorize('wizard', $job);
        $job_party = JobParty::findOrFail($id);
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
            'job_party' => $job_party,
            'parties_type' => $parties_type,
            'work_order' => request()->input('workorder'),
        ];

        return view('client.wizard2.editparty', $data);
    }

    public function updateParty(Request $request, $job_id, $id)
    {
        //echo $request->input('firm_name');return;
        $job = Job::findOrFail($job_id);
        $this->authorize('wizard', $job);
        $this->validate($request, [
            //'firm_name' => 'required_without_all:first_name,last_name',
            'address_1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
        ]);
        $job_party = JobParty::findOrFail($id);

        $entity = $job_party->firm;
        if ($request->input('firm_name') != null && $request->input('firm_name') != '') {
            $entity->firm_name = $request->input('firm_name');
            $entity->save();
        }

        $contact = $job_party->contact;

        if ($request->input('first_name') != null && $request->input('first_name') != '') {
            $contact->first_name = $request->input('first_name');
        } else {
            $contact->first_name = '';
        }
        if ($request->input('last_name') != null && $request->input('last_name') != '') {
            $contact->last_name = $request->input('last_name');
        } else {
            $contact->last_name = '';
        }

        $contact->address_1 = $request->input('address_1');
        $contact->address_2 = $request->input('address_2');
        $contact->email = $request->input('email');
        $contact->phone = $request->input('phone');
        $contact->mobile = $request->input('mobile');
        $contact->fax = $request->input('fax');
        $contact->city = $request->input('city');
        $contact->state = $request->input('state');
        $contact->zip = $request->input('zip');
        $contact->country = $request->input('country');
        //$contact->gender = $request->input('gender');
        if ($contact->hot_id == 0) {
            $contact->save();
            $entity->save();
        } else {
            if ($request->has('update_open_jobs')) {
                $contact->save();
                $entity->save();
            } else {
                if ($contact->isDirty()) {
                    $xcontact = new ContactInfo();
                    $xcontact = $contact->replicate();
                    $xcontact->hot_id = 0;
                    $xcontact->primary = 0;
                    $xcontact->save();
                    $job_party->contact_id = $xcontact->id;
                    $job_party->save();
                } else {
                }
            }
        }
        $data = $request->all();
        if ($request->has('bond_date')) {
            if (strlen($data['bond_date']) > 0) {
                $data['bond_date'] = date('Y-m-d H:i:s', strtotime($data['bond_date']));
            } else {
                $data['bond_date'] = null;
            }
        }
        $job_party->update($data);
        if ($request->hasFile('bond_pdf')) {
            if ($request->file('bond_pdf')->isValid()) {
                $f = $request->file('bond_pdf');
                $xfilename = 'job-'.$job_id.'-party-'.$JobParty->id.'.'.$f->guessExtension();
                $xpath = 'jobparties/bonds/pdfs';
                $f->storeAs($xpath, $xfilename);
                $job_party->bond_pdf_filename = $f->getClientOriginalName();
                $job_party->bond_pdf = $xpath.'/'.$xfilename;
                $job_party->bond_pdf_filename_mime = $f->getMimeType();
                $job_party->bond_pdf_filename_size = $f->getSize();
                $job_party->save();
            } else {
                Session::flash('message', 'PDF file not uploaded correctly');

                return redirect()->back()->withInput();
            }
        }

        if ($request->has('bond_date')) {
            if (strlen($data['bond_date']) > 0) {
                $job_party->bond_date = date('Y-m-d', strtotime($data['bond_date']));
            } else {
                $job_party->bond_date = null;
            }
            $job_party->save();
        }

        if ($request->has('lien_prohibition')) {
            $job_party->landowner_lien_prohibition = 1;
        } else {
            $job_party->landowner_lien_prohibition = 0;
        }

        if ($request->has('copy_recipient_type')) {
            if ($request->copy_recipient_type == 'other') {
                $job_party->copy_type = $request->other_copy_recipient_type;
            } else {
                $job_party->copy_type = $request->copy_recipient_type;
            }
        }

        if ($request->has('leaseholder_type')) {
            if ($request->leaseholder_type == 'Lessee') {
                $job_party->leaseholder_bookpage_number = null;
            } else {
            }
        }

        $job_party->save();
        Session::flash('message', 'Job party '.$job_party->contact->full_name.' successfully created.');

        return redirect()->route('wizard2.getparties', $job_id);
    }

    public function destroyParty($job_id, $id)
    {
        $job = Job::findOrFail($job_id);
        $this->authorize('wizard', $job);
        $job_party = JobParty::findOrFail($id);
        $temp_name = $job_party->contact->full_name;
        $job_party->delete();

        // redirect
        Session::flash('message', 'Job party '.$temp_name.' successfully deleted.');

        return redirect()->route('wizard2.getparties', $job_id);
    }

    public function payments($job_id, $wo_id)
    {
        $work = WorkOrder::findOrFail($wo_id);
        $job = $work->job;
        $this->authorize('wizard', $job);
        $client = $work->job->client;

        $work->status = 'open';
        $work->save();

        // NNP charge fees.
        // $isNNP = strpos($work->type, 'notice-of-nonpayment') === false ? false : true;
        // $day14after = date('Y-m-d', strtotime('+14days'));
        // $dueAt = date('Y-m-d', strtotime($work->due_at));
        // $today = date('Y-m-d');
        // $isRushExpress = ($day14after>=$dueAt && $dueAt>$today);

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
        $total_amount = 0;
        if ($doit) {
            foreach ($template->lines as $tline) {
                if ($tline->type == 'aply-when-rush' && $work->is_rush) {
                    $total_amount += $tline->quantity * $tline->price;
                }
                if ($tline->type == 'apply-always' && $work->service != 'self') {
                    $total_amount += $tline->quantity * $tline->price;
                }
                if ($tline->type == 'apply-always' && $work->service == 'self') {
                    if (count($template->lines->where('type', 'apply-always-ss')) == 0) {
                        $total_amount += $tline->quantity * $tline->price;
                    }
                }
                if ($tline->type == 'apply-always-ss' && $work->service == 'self') {
                    $total_amount += $tline->quantity * $tline->price;
                }
            }
        }

        if (count($work->wizardInvoices) > 0) {
            $invoice = $work->wizardInvoices->first();
        } else {
            if ($total_amount > 0) {
                $invoice = new Invoice();
                $invoice->client_id = $client->id;
                $invoice->work_order_id = $work->id;
                $invoice->type = 'client-wizard';
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

                $invoice->total_amount = $total_amount;
                $invoice->save();

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
                            //$total_amount += $line->amount;
                            $line->save();
                        } else {
                        }
                        if ($tline->type == 'apply-always' && $work->service != 'self') {
                            $line = new InvoiceLine();
                            $line->invoice_id = $invoice->id;
                            $line->description = $tline->description;
                            $line->quantity = $tline->quantity;
                            $line->price = $tline->price;
                            $line->status = '';
                            $line->amount = $tline->quantity * $tline->price;
                            //$total_amount += $line->amount;
                            $line->save();
                        }
                        if ($tline->type == 'apply-always' && $work->service == 'self') {
                            if (count($template->lines->where('type', 'apply-always-ss')) == 0) {
                                $line = new InvoiceLine();
                                $line->invoice_id = $invoice->id;
                                $line->description = $tline->description;
                                $line->quantity = $tline->quantity;
                                $line->price = $tline->price;
                                $line->status = '';
                                $line->amount = $tline->quantity * $tline->price;
                                //$total_amount += $line->amount;
                                $line->save();
                            }
                        }
                        if ($tline->type == 'apply-always-ss' && $work->service == 'self') {
                            $line = new InvoiceLine();
                            $line->invoice_id = $invoice->id;
                            $line->description = $tline->description;
                            $line->quantity = $tline->quantity;
                            $line->price = $tline->price;
                            $line->status = '';
                            $line->amount = $tline->quantity * $tline->price;
                            //$total_amount += $line->amount;
                            $line->save();
                        }
                    }
                }
            }
        }

        if ($total_amount == 0) {
            if ($doit) {
                $lines = [];
                foreach ($template->lines as $tline) {
                    if ($tline->type == 'apply-during-docgen-rush' && $work->is_rush && $work->service != 'self') {
                        $line_gen = [
                            //'invoice_id' => $invoice->id,
                            'description' => $tline->description,
                            'quantity' => $tline->quantity,
                            'price' => $tline->price,
                            'amount' => $tline->quantity * $tline->price,
                            'status' => '', ];
                        $lines[] = $line_gen;
                        $total_amount += $line_gen['amount'];
                    }
                    if ($tline->type == 'apply-during-docgen' && $work->service != 'self') {
                        $line_gen = [
                            //'invoice_id' => $invoice->id,
                            'description' => $tline->description,
                            'quantity' => $tline->quantity,
                            'price' => $tline->price,
                            'amount' => $tline->quantity * $tline->price,
                            'status' => '', ];
                        $lines[] = $line_gen;
                        $total_amount += $line_gen['amount'];
                    }
                }
                $invoice_gen = [
                    'total_amount' => $total_amount,
                    'lines' => $lines,
                ];

                $notice = $work;
                $contacts = $job->parties()->where('type', '!=', 'client')->orderBy('type')->get();
                $wo_types = WorkOrderType::all()->pluck('name', 'slug')->toArray();
                //dd($contacts);
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
                    'parties_type' => $parties_type,
                    'wo_types' => $wo_types,
                    'invoice' => $invoice_gen,
                    'job' => $job,
                    'contacts' => $contacts,
                    'notice' => $notice,
                ];

                return view('client.wizard2.noinvoice_confirmation', $data);
            }
        }

        if ($total_amount == 0) {
            Session::flash('message', 'Notice '.$work->number.' successfully created. Nothing to charge');

            return redirect()->route('client.notices.edit', $work->id);
        }

        if ($invoice->client->billing_type == 'invoiced') {
            $notice = $work;

            $contacts = $job->parties()->where('type', '!=', 'client')->orderBy('type')->get();
            $wo_types = WorkOrderType::all()->pluck('name', 'slug')->toArray();
            //dd($contacts);
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
                'parties_type' => $parties_type,
                'wo_types' => $wo_types,

                'invoice' => $invoice,
                'job' => $job,
                'contacts' => $contacts,
                'notice' => $notice,
                //'apply_always'=>count($template->lines->where('type','apply-always'))
            ];

            return view('client.wizard2.weeklypay', $data);
        }

        $work->status = 'payment pending';
        $work->save();

        return redirect()->route('wizard2.capturecc', $invoice->id);
    }

    public function choicenext($job_id, $wo_id)
    {
        $work = WorkOrder::findOrFail($wo_id);
        $work->status = 'open';
        $work->save();
        $data = [
            'job_id' => $job_id,
            'workorder_id' => $wo_id,
        ];

        return view('client.wizard2.choicenext', $data);
    }

    public function capturecc($invoice_id)
    {
        $invoice = Invoice::findorFail($invoice_id);
        $this->authorize('capturecc', $invoice);
        if ($invoice->status == 'open') {
            $client = $invoice->client;
            $company = CompanySetting::first();
            $data = [
                'invoice' => $invoice,
                'client' => $client,
                'api_key' => $company->apikey,
                'api_secret' => $company->apisecret,
                'js_security_key' => $company->js_security_key,
                'ta_token' => $company->ta_token,
                'payeezy_url' => $company->url,
            ];

            return view('client.wizard2.payment', $data);
        } else {
            return redirect()->route('wizard2.invoice.'.$invoice->status, $invoice->id);
        }
    }

    public function paid($payment_id)
    {
        session()->forget('wizard2.newjob');
        $payment = Payment::findOrFail($payment_id);
        $invoices_id = unserialize($payment->invoices_id);
        $invoice = Invoice::findorFail($invoices_id[0]);
        $this->authorize('capturecc', $invoice);

        $notice = $invoice->work_order;
        $job = $notice->job;
        $notice->status = 'open';
        $notice->save();
        $contacts = $job->parties()->where('type', '!=', 'client')->orderBy('type')->get();
        $wo_types = WorkOrderType::all()->pluck('name', 'slug')->toArray();
        //dd($contacts);
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

        // $template = Template::where('type_slug',$work->type)->where('client_id',$client->id)->first();
        // $doit = false;
        // if($template) {
        //     $doit = true;
        // } else {
        //     $template = Template::where('type_slug',$work->type)->where('client_id',0)->first();
        //     if($template) {
        //         $doit = true;
        //     }
        // }

        $data = [
            'parties_type' => $parties_type,
            'wo_types' => $wo_types,
            'payment' => $payment,
            'invoice' => $invoice,
            'job' => $job,
            'contacts' => $contacts,
            'notice' => $notice,
            //'apply_always'=>count($template->lines->where('type','apply-always'))
        ];

        return view('client.wizard2.paid', $data);
    }

    public function unpaid($payment_id)
    {
        session()->forget('wizard2.newjob');
        $payment = Payment::findOrFail($payment_id);
        $invoices_id = unserialize($payment->invoices_id);
        $invoice = Invoice::findorFail($invoices_id[0]);
        $this->authorize('capturecc', $invoice);
        $data = [];

        return view('client.wizard2.unpaid', $data);
    }

    public function paymentPurchase(Request $request)
    {
        if ($request->has('donottokenize')) {
            $this->validate($request, [
                'invoice_id' => 'required',
                'currency' => 'required',
            ]);
        } else {
            $this->validate($request, [
                'invoice_id' => 'required',
                'currency' => 'required',
                'token' => 'required',
            ]);
        }
        $invoice = Invoice::findOrFail($request->invoice_id);
        $this->authorize('capturecc', $invoice);
        $data = $request->all();
        $data['token'] = json_decode($request->token, true);

        $client = $invoice->client;
        if (strlen($client->payeezy_type) == 0) {
            $client->payeezy_type = $data['token']['type'];
            $client->payeezy_value = $data['token']['value'];
            $client->payeezy_cardholder_name = $data['token']['cardholder_name'];
            $client->payeezy_exp_date = $data['token']['exp_date'];
            $client->save();
        }
        $company = CompanySetting::first();

        //dd($data);

        $py = new Payeezy();
        $py->setApiKey($data['apikey']);
        $py->setApiSecret($data['apisecret']);
        $py->setMerchantToken($company->merchant_token);
        $py->setUrl('https://'.$company->url.'/v1/transactions');
        if ($client->company_name == '' || $client->company_name == null) {
            $client_name = $client->first_name.' '.$client->last_name;
        } else {
            $client_name = $client->company_name;
        }

        $payload = [
            'merchant_ref' => $client_name,
            'transaction_type' => 'purchase',
            'method' => 'token',
            'amount' => number_format($invoice->total_amount, 2, '', ''),
            'currency_code' => $data['currency'],
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

        //dd($result_data);
        //dd(Auth::user()->id);
        //save into payments
        $payment = new Payment();
        $payment->invoices_id = serialize([$invoice->id]);
        $payment->type = 'credit_card';
        $payment->amount = $invoice->total_amount;
        $payment->client_id = $client->id;
        $payment->reference = $result_data->correlation_id;
        $payment->gateway = 'payeezy';
        $payment->transaction_status = $result_data->transaction_status;
        $payment->log_result = $result;
        $payment->user_id = Auth::user()->id;
        $payment->save();

        // change invoice status
        if ($result_data->transaction_status == 'approved') {
            //$users = Auth::user()->client->users;
            $users = Auth::user()->client->activeusers;

            foreach ($users as $user) {
                $mailto[] = $user->email;
            }
            $invoice->status = 'paid';
            $invoice->payment_id = $payment->id;
            $invoice->payed_at = \Carbon\Carbon::now();
            $invoice->save();
            //$client=Auth::user()->client;
            //if ($client->notification_setting=='immediate'){

            // }

            $client = Auth::user()->client;
            if (json_encode(unserialize($client->override_payment)) != 'false' && json_encode(unserialize($client->override_payment)) != 'null') {
                Mail::to(unserialize($client->override_payment))->send(new PaymentMade($invoice->total_amount, [$invoice], $client, $payment->created_at));
            } else {
                $mailto = [];
                $responsible_user = User::where('id', $invoice->work_order->responsible_user)->first();
                if ($invoice->work_order->responsible_user && count($responsible_user) > 0) {
                    $mailto[] = $responsible_user->email;
                } else {
                    $users = $work->job->client->activeusers;
                    foreach ($users as $user) {
                        $mailto[] = $user->email;
                    }
                }
                if (count($mailto) > 0) {
                    Mail::to($mailto)->send(new PaymentMade($invoice->total_amount, [$invoice], $client, $payment->created_at));
                }
            }
            $work = $invoice->work_order;
            if ($work->job->notify_email) {
                Mail::to($work->job->notify_email)->send(new PaymentMade($invoice->total_amount, [$invoice], $client, $payment->created_at));
            }
        } else {
            $invoice->status = 'unpaid';
            $invoice->save();
        }

        return json_encode([
            'status' => $invoice->status,
            'id' => $payment->id,
        ]);
    }

    public function copyParty(Request $request, $job_id, $id)
    {
        $job_party = JobParty::findOrFail($id);
        $copy = $job_party->replicate();
        $copy->source = 'CL';
        $copy->type = $request->party_type;
        $copy->save();
        Session::flash('message', 'Job party '.$job_party->contact->full_name.' successfully copied.');

        return redirect()->route('wizard2.getparties', $job_id);
    }

    public function getExistingNotice(Request $request)
    {
        $job_id = $request->job_id;
        $work_id = $request->work_id;
        if (! $job_id || ! $work_id) {
            return redirect()->route('wizard2.getjobworkorder');
        }
        $work = WorkOrder::where('id', $work_id)->first();
        if (! $work) {
            return redirect()->route('wizard2.getjobworkorder');
        }
        $work->created_at = Carbon::now();
        $work->save();
        Session::put('workorder', $work_id);
        $job = Job::where('id', $job_id)->first();
        $customer_count = count($job->parties->where('type', 'customer'));
        if ($customer_count > 0) {
            return redirect()->route('wizard2.getparties', $job_id);
        }

        return redirect()->route('wizard2.getemployer', $job_id);
    }

    public function getUnpaidNotice(Request $request)
    {
        $work_id = $request->work_id;
        $invoice_id = $request->invoice_id;
        if (! $invoice_id || ! $work_id) {
            return redirect()->route('wizard2.getjobworkorder');
        }
        $invoice = Invoice::findorFail($invoice_id);
        if (! $invoice) {
            return redirect()->route('wizard2.getjobworkorder');
        }
        $work = WorkOrder::where('id', $work_id)->first();
        // $work->created_at = Carbon::now();
        // $work->save();
        $job = $work->job;
        $customer_count = count($job->parties->where('type', 'customer'));
        if ($customer_count == 0) {
            return redirect()->route('wizard2.getemployer', $job->id);
        }
        $client = $invoice->client;
        $company = CompanySetting::first();
        $data = [
            'invoice' => $invoice,
            'client' => $client,
            'api_key' => $company->apikey,
            'api_secret' => $company->apisecret,
            'js_security_key' => $company->js_security_key,
            'ta_token' => $company->ta_token,
            'payeezy_url' => $company->url,
        ];
        Session::put('workorder', $work_id);

        return view('client.wizard2.payment', $data);
    }
}
