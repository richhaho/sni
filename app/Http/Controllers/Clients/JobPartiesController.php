<?php

namespace App\Http\Controllers\Clients;

use App\Client;
use App\ContactInfo;
use App\Entity;
use App\Http\Controllers\Controller;
use App\Job;
use App\JobParty;
use App\WorkOrder;
use Auth;
use Illuminate\Http\Request;
use Response;
use Session;
use Storage;

class JobPartiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($job_id)
    {
        $job = Job::findORFail($job_id);
        $this->authorize('wizard', $job);
        $entities = $job->client->entities->pluck('firm_name', 'id');
        if (Session::has('workorder')) {
            $xworkorder = Session::get('workorder');
        } else {
            $xworkorder = request()->input('workorder');
        }

        // $locked=count(WorkOrder::where('job_id',$job_id)->whereIn('status',['pending','data entry','edit','print'])->get());
        $locked = count($job->workorders->whereIn('status', ['pending', 'data entry', 'edit', 'print'])->where('service', '!=', 'self'));
        $style = '';
        if ($locked > 0) {
            $style = 'pointer-events:none;opacity:0.5;';
        }

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

        $gender = [
            'none' => 'Select one..',
            'female' => 'Female',
            'male' => 'Male',
        ];

        $data = [
            'style' => $style,
            'job' => $job,
            'parties_type' => $parties_type,
            'work_order' => $xworkorder,
            'entities' => $entities,
            'gender' => $gender,
        ];

        return view('client.jobparties.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($job_id)
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $job_id)
    {
        $job = Job::findORFail($job_id);
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
        if ($request->input('workorder') == '') {
            return redirect()->route('client.parties.index', $job_id)->with('redirect_to', $request->input('redirect_to'));
        } else {
            return redirect()->route('client.parties.index', $job_id)->with('workorder', $request->input('workorder'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($job_id, $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($job_id, $id)
    {
        $job = Job::findORFail($job_id);
        $this->authorize('wizard', $job);
        $job_party = JobParty::findOrFail($id);
        if ($job_party->job->client_id != Auth::user()->client->id) {
            abort(403);
        }
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

        return view('client.jobparties.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $job_id, $id)
    {
        $job = Job::findORFail($job_id);
        $this->authorize('wizard', $job);
        $this->validate($request, [

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
        Session::flash('message', 'Job party '.$job_party->contact->full_name.' successfully updated.');

        if ($request->input('workorder') == '') {
            return redirect()->route('client.parties.index', $job_id)->with('redirect_to', $request->input('redirect_to'));
        } else {
            return redirect()->route('client.parties.index', $job_id)->with('workorder', $request->input('workorder'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($job_id, $id)
    {
        $job = Job::findORFail($job_id);
        $this->authorize('wizard', $job);
        $job_party = JobParty::findOrFail($id);
        $temp_name = $job_party->contact->full_name;
        $job_party->delete();

        // redirect
        Session::flash('message', 'Job party '.$temp_name.' successfully deleted.');

        if (request()->input('workorder') == '') {
            return redirect()->route('client.parties.index', $job_id)->with('redirect_to', request()->input('redirect_to'));
        } else {
            return redirect()->route('client.parties.index', $job_id)->with('workorder', request()->input('workorder'));
        }
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
                return view('client.jobparties.dynamicforms.bond');
                break;
            case 'landowner':
                return view('client.jobparties.dynamicforms.landowner');
                break;
            case 'leaseholder':
                return view('client.jobparties.dynamicforms.leaseholder');
                break;
            case 'copy_recipient':
                return view('client.jobparties.dynamicforms.copy');

                return;
                break;
        }
    }

    public function newContactForm($client_id)
    {
        $clients = Client::where('id', '<>', $client_id)->get()->pluck('full_name', 'id')->prepend('Select one...', 0);
        $client = Client::findOrFail($client_id);
        $entities = $client->entities()->get()->pluck('firm_name', 'id');

        $gender = [
            'none' => 'Select one..',
            'female' => 'Female',
            'male' => 'Male',
        ];
        $print_method = [
            'none' => 'None',
            'sni' => 'SNI Prints',
            'client' => 'Client Prints',
        ];
        $billing_type = [
            'none' => 'Select one...',
            'attime' => 'When Work order is created',
            'invoiced' => 'Invoiced once a week',
        ];
        $send_certified = [
            'none' => 'None',
            'green' => 'Green Certified',
            'nongreen' => 'Non-green Certified',
        ];
        $data = [
            'clients' => $clients,
            'print_method' => $print_method,
            'billing_type' => $billing_type,
            'send_certified' => $send_certified,
            'gender' => $gender,
            'entities' => $entities,
        ];

        return view('client.jobparties.dynamicforms.newcontact', $data);
    }

    public function downloadbond($job_id, $id)
    {
        $job_party = JobParty::findOrFail($id);

        return response()->download(Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix().$job_party->bond_pdf);
//        $contents = Storage::get($job_party->bond_pdf);
//        $response = Response::make($contents, '200');
//        $response->header('Content-Type', $job_party->bond_pdf_filename_mime);
//        return $response;
    }

    public function copyParty(Request $request, $job_id, $id)
    {
        $job_party = JobParty::findOrFail($id);
        $copy = $job_party->replicate();
        $copy->source = 'CL';
        $copy->type = $request->party_type;
        $copy->save();
        Session::flash('message', 'Job party '.$job_party->contact->full_name.' successfully copied.');

        if ($request->input('workorder') == '') {
            return redirect()->route('client.parties.index', $job_id)->with('redirect_to', $request->input('redirect_to'));
        } else {
            return redirect()->route('client.parties.index', $job_id)->with('workorder', $request->input('workorder'));
        }
    }
}
