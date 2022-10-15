<?php

namespace App\Http\Controllers\Admin;

use App\Client;
use App\ContactInfo;
use App\Entity;
use App\Http\Controllers\Controller;
use App\Job;
use App\Notifications\ApprovedClientUser;
use App\Role;
use App\SubscriptionRate;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Session;

class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $clients = Client::query()->whereNull('deleted_at');
        if (session()->has('client_filter.search')) {
            if (session('client_filter.search') != '') {
                if (is_numeric(session('client_filter.search'))) {
                    $clients->where('id', session('client_filter.search'));
                } else {
                    //$clients = Client::search(session('client_filter.search'));
                    $clients = $clients->where(function ($query) {
                        $query->where('company_name', 'like', '%'.session('client_filter.search').'%')->orwhere('first_name', 'like', '%'.session('client_filter.search').'%')->orwhere('last_name', 'like', '%'.session('client_filter.search').'%')->orwhere('email', 'like', '%'.session('client_filter.search').'%');
                    });
                }
            }
        }
        /////////////////////////////////////////////////////////
        if (session()->has('client_filter.approval_status')) {
            if (session('client_filter.approval_status') != '') {
                $filter_status = session('client_filter.approval_status');
            } else {
                $filter_status = 'all';
            }
        } else {
            $filter_status = 'all';
        }

        //return json_encode($clients->get());
        /////////////////////////////////////////////////////////////
        if (session()->has('client_filter.client_status')) {
            if (session('client_filter.client_status') != '') {
                $filter_client_status = session('client_filter.client_status');
            } else {
                $filter_client_status = 'all';
            }
        } else {
            $filter_client_status = 'all';
        }

        if ($filter_client_status == 'enabled') {
            $clients = $clients->where('status', 4);
        }
        if ($filter_client_status == 'disabled') {
            $clients = $clients->where('status', 3);
        }
        ///////////////////////////////////////////////////////////
        $a = $clients;

        if ($filter_status == 'all') {
            $clients = $clients->orderBy('company_name')->paginate(15);
        }
        if ($filter_status == 'pending' || $filter_status == 'denied') {
            $filter_clients = User::where('approve_status', $filter_status)->pluck('client_id');
            $clients = $clients->whereIn('id', $filter_clients)->orderBy('company_name')->paginate(15);
        }
        if ($filter_status == 'approved') {
            $filter_clients = User::where('approve_status', 'pending')->orwhere('approve_status', 'denied')->pluck('client_id');
            $clients = $clients->whereNotIn('id', $filter_clients)->orderBy('company_name')->paginate(15);
        }

        Session::put('backUrl', \URL::full());

        $approval_status = [
            'all' => 'All',
            'pending' => 'Pending',
            'approved' => 'Approved',
            'denied' => 'Denied', ];
        $client_status = [
            'all' => 'All',
            'disabled' => 'Disabled',
            'enabled' => 'Enabled', ];

        $data = [
            'clients' => $clients,
            'approval_status' => $approval_status,
            'filter_status' => $filter_status,
            'client_status' => $client_status,
            'filter_client_status' => $filter_client_status,

        ];

        return view('admin.clients.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $clients = Client::get()->pluck('full_name', 'id')->prepend('Select one...', 0);
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
            //'none' => 'Select one...',
            'attime' => 'When Work order is created',
            'invoiced' => 'Invoiced once a week',
        ];
        $send_certified = [
            'none' => 'None',
            'green' => 'Green Certified',
            'nongreen' => 'Non-green Certified',
        ];
        $notification_setting = [
            'immediate' => 'Immediate',
            'off' => 'Off',
        ];
        $address_type = [
            'sni' => 'Sunshine Address',
            'client' => 'Client Address',
        ];
        $current_addresstype = 'sni';

        $data = [
            'clients' => $clients,
            'print_method' => $print_method,
            'billing_type' => $billing_type,
            'send_certified' => $send_certified,
            'gender' => $gender,
            'notification_setting' => $notification_setting,
            'address_type' => $address_type,
            'current_addresstype' => $current_addresstype,

        ];

        return view('admin.clients.create', $data);
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
            'company_name' => 'required_without_all:first_name,last_name',
            'email' => 'required_with:create_login|nullable|email',
            'first_name' => 'required_with:create_login|nullable',
            'last_name' => 'required_with:create_login|nullable',
            'address_1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'zip' => 'required',
        ]);

        $client = Client::create($request->all());
        $client->notification_setting = $request['notification_setting'];
        $client->return_address_type = $request['address_type'];
        $rate = SubscriptionRate::first();
        $client->self_30day_rate = $rate->self_30day_rate;
        $client->self_365day_rate = $rate->self_365day_rate;
        $client->full_30day_rate = $rate->full_30day_rate;
        $client->full_365day_rate = $rate->full_365day_rate;
        $client->save();
        //lets create contact and associate...
        //create entity
        $entity = new Entity();

        if (strlen(trim($client->company_name)) == 0) {
            $entity->firm_name = $client->full_name;
        } else {
            $entity->firm_name = $client->company_name;
        }
        $entity->latest_type = 'client';
        $entity->client_id = $client->id;
        $entity->save();

        //create associate
        $associate = new ContactInfo();
        if (strlen($request->first_name) > 0) {
            $associate->first_name = $request->first_name;
        } else {
            $associate->first_name = ' ';
        }
        if (strlen($request->last_name) > 0) {
            $associate->last_name = $request->last_name;
        } else {
            $associate->last_name = ' ';
        }
        //$associate->gender = $request->gender;
        $associate->address_1 = $request->address_1;
        $associate->address_2 = $request->address_2;
        $associate->city = $request->city;
        $associate->state = $request->state;
        $associate->zip = $request->zip;
        $associate->country = $request->country;
        $associate->phone = $request->phone;
        $associate->mobile = $request->mobile;
        $associate->fax = $request->fax;
        $associate->email = $request->email;
        $associate->primary = 2;
        $associate->status = 1;
        $associate->entity_id = $entity->id;
        $associate->save();

        if ($request->has('create_login')) {
            $xuser = new User();
            $xuser->email = $request->input('email');
            $xuser->first_name = $request->input('first_name');
            $xuser->last_name = $request->input('last_name');
            $xuser->client_id = $client->id;
            $xuser->password = Hash::make(str_random(10));
            // comment next line if you need verification

            $xuser->save();

            $xuser->confirmEmail();

            $default_role = Role::where('name', 'client')->first();
            $xuser->attachRole($default_role);
            $client->client_user_id = $xuser->id;
            $client->save();

            $credentials = ['email' => $xuser->email];
            $response = Password::sendResetLink($credentials, function (Message $message) {
                $message->subject($this->getEmailSubject());
            });
            switch ($response) {
                case Password::RESET_LINK_SENT:
                   //dd('link_sent');
                case Password::INVALID_USER:
                    //dd('invalid_user');
            }
        }

        if ($request->has('enable_login')) {
            $client->admin_user->status = 1;
            $client->admin_user->save();
        }

        if ($request->has('disable_login')) {
            $client->admin_user->status = 0;
            $client->admin_user->save();
        }
        $temp_name = $client->full_name;
        Session::flash('message', 'Successfully updated the client: '.$temp_name);

        return redirect()->route('contacts.index', $client->id);
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $client = Client::findOrFail($id);
        if ($client->service && $client->subscription && $client->subscriptionRate == 0 && $client->expiration && $client->expiration < date('Y-m-d H:i:s')) {
            $client->expiration = date('Y-m-d H:i:s', strtotime($client->expiration.' +'.$client->subscription.'days'));
        }
        if (count($client->users) > 0) {
            $user_status = $client->users[0]->approve_status;
        } else {
            $user_status = 'approved';
        }

        $clients = Client::get()->pluck('full_name', 'id')->prepend('Select one...', 0);
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
            'attime' => 'When Work order is created',
            'invoiced' => 'Invoiced once a week',
        ];
        $send_certified = [
            'none' => 'None',
            'green' => 'Green Certified',
            'nongreen' => 'Non-green Certified',
        ];
        $notification_setting = [
            'immediate' => 'Immediate',
            'off' => 'Off',
        ];
        $override_weekly = unserialize($client->override_weekly);
        $override_payment = unserialize($client->override_payment);
        $override_notice = unserialize($client->override_notice);

        $override_emailReminder = unserialize($client->override_email_reminder);
        $override_smsReminder = unserialize($client->override_sms_reminder);
        $override_lastday_over = unserialize($client->override_lastday_over);

        $address_type = [
            'sni' => 'Sunshine Address',
            'client' => 'Client Address',
        ];
        if ($user_status == 'pending') {
            $approval_status = [
                'pending' => 'Pending',
                'approved' => 'Approved',
                'denied' => 'Denied', ];
        } else {
            $approval_status = [
                'approved' => 'Approved',
                'denied' => 'Denied', ];
        }

        $current_addresstype = $client->return_address_type;
        $default_customer_type = [
            null => '',
            'general_contractor' => 'General Contractor',
            'sub_contractor' => 'Sub Contractor',
            'sub_sub' => 'Sub-Sub Contractor',
            'landowner' => 'Property Owner',
            'leaseholder' => 'Lease Holder (Lessee/Tenant)',
        ];

        $data = [
            'client' => $client,
            'clients' => $clients,
            'print_method' => $print_method,
            'billing_type' => $billing_type,
            'send_certified' => $send_certified,
            'gender' => $gender,
            'notification_setting' => $notification_setting,
            'address_type' => $address_type,
            'current_addresstype' => $current_addresstype,
            'approval_status' => $approval_status,
            'user_status' => $user_status,
            'default_customer_type' => $default_customer_type,
            'override_weekly' => $override_weekly,
            'override_payment' => $override_payment,
            'override_notice' => $override_notice,
            'override_emailReminder' => $override_emailReminder,
            'override_smsReminder' => $override_smsReminder,
            'override_lastday_over' => $override_lastday_over,

        ];
        // show the edit form and pass the nerd

        return view('admin.clients.edit', $data);
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
            'company_name' => 'required_without_all:first_name,last_name',
            'email' => 'required_with:create_login|nullable|email',
            'zip' => 'required',
        ]);

        $client = Client::findOrFail($id);
        $users = $client->users;
        $temp_name = $client->company_name;
        $data = $request->all();
        if ($data['expiration']) {
            $data['expiration'] = date('Y-m-d H:i:s', strtotime($data['expiration']));
        }
        $client->update($data);
        $client->notification_setting = $request['notification_setting'];
        $client->return_address_type = $request['address_type'];
        $client->default_customer_type = $request['default_customer_type'];

        $client->override_weekly = serialize($request->override_weekly);
        $client->override_payment = serialize($request->override_payment);
        $client->override_notice = serialize($request->override_notice);

        $client->override_email_reminder = serialize($request->override_emailReminder);
        $client->override_sms_reminder = serialize($request->override_smsReminder);
        $client->override_lastday_over = serialize($request->override_lastday_over);

        if ($request->has('allow_smsReminder')) {
            $client->allow_sms_reminder = 1;
        } else {
            $client->allow_sms_reminder = 0;
        }
        if ($request->has('allow_emailReminder')) {
            $client->allow_email_reminder = 1;
        } else {
            $client->allow_email_reminder = 0;
        }
        if ($request->has('autobatch')) {
            $client->autobatch = 1;
        } else {
            $client->autobatch = 0;
        }
        if ($request->has('autopay_weekly')) {
            $client->autopay_weekly = 1;
        } else {
            $client->autopay_weekly = 0;
        }

        if ($request->has('gps_tracking')) {
            $client->gps_tracking = 1;
        } else {
            $client->gps_tracking = 0;
        }

        if ($request->has('allow_jobclose')) {
            $client->allow_jobclose = 1;
        } else {
            $client->allow_jobclose = 0;
        }

        if ($request->has('turn_job_reminder')) {
            $client->turn_job_reminder = 1;
        } else {
            $client->turn_job_reminder = 0;
        }

        if ($request->has('is_monitoring_user')) {
            $client->is_monitoring_user = 1;
        } else {
            $client->is_monitoring_user = 0;
        }

        if ($request->has('has_contract_tracker')) {
            $client->has_contract_tracker = 1;
        } else {
            $client->has_contract_tracker = 0;
        }

        $client->notes = $request->notes;
        $client->save();

        foreach ($users as $user) {
            if ($user->approve_status != 'approved' && $request['approval_status'] == 'approved') {
                $user->notify(new ApprovedClientUser($user));
            }

            $user->approve_status = $request['approval_status'];
            $user->save();
        }

        if ($request['approval_status'] == 'approved') {
            if (count($client->contacts->where('primary', 2)) > 0) {
                $associate = $client->contacts->where('primary', 2)->first();
                if (strlen($request->first_name) > 0) {
                    $associate->first_name = $request->first_name;
                } else {
                    $associate->first_name = ' ';
                }
                if (strlen($request->last_name) > 0) {
                    $associate->last_name = $request->last_name;
                } else {
                    $associate->last_name = ' ';
                }
                //$associate->gender = $request->gender;
                $associate->address_1 = $request->address_1;
                $associate->address_2 = $request->address_2;
                $associate->city = $request->city;
                $associate->state = $request->state;
                $associate->zip = $request->zip;
                $associate->country = $request->country;
                $associate->phone = $request->phone;
                $associate->mobile = $request->mobile;
                $associate->fax = $request->fax;
                $associate->email = $request->email;

                $associate->save();

                $entity = $associate->entity;
                if (strlen(trim($client->company_name)) == 0) {
                    $entity->firm_name = $client->full_name;
                } else {
                    $entity->firm_name = $client->company_name;
                }
                $entity->save();
            } else {
                //lets create contact and associate...
                //create entity
                $entity = new Entity();

                if (strlen(trim($client->company_name)) == 0) {
                    $entity->firm_name = $client->full_name;
                } else {
                    $entity->firm_name = $client->company_name;
                }
                $entity->latest_type = 'client';
                $entity->client_id = $client->id;
                $entity->save();

                //create associate
                $associate = new ContactInfo();
                if (strlen($request->first_name) > 0) {
                    $associate->first_name = $request->first_name;
                } else {
                    $associate->first_name = ' ';
                }
                if (strlen($request->last_name) > 0) {
                    $associate->last_name = $request->last_name;
                } else {
                    $associate->last_name = ' ';
                }
                //$associate->gender = $request->gender;
                $associate->address_1 = $request->address_1;
                $associate->address_2 = $request->address_2;
                $associate->city = $request->city;
                $associate->state = $request->state;
                $associate->zip = $request->zip;
                $associate->country = $request->country;
                $associate->phone = $request->phone;
                $associate->mobile = $request->mobile;
                $associate->fax = $request->fax;
                $associate->email = $request->email;
                $associate->primary = 2;
                $associate->status = 1;
                $associate->entity_id = $entity->id;
                $associate->save();
            }
        }
        Session::flash('message', 'Successfully updated the client: '.$temp_name);

        return redirect()->route('clients.edit', $client->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $client = Client::findOrFail($id);

        $jobs = Job::where([['client_id', $id], ['deleted_at', null]])->count();
        $invoices = Job::where([['client_id', $id], ['deleted_at', null]])->count();

        //echo json_encode($invoices);return;
        if ($jobs > 0 || $invoices > 0) {
            Session::flash('message', 'This Client has one or more jobs(Invoices, Work orders).  Please delete all jobs(invoices, work orders) before deleting this Client.');
        } else {
            $temp_name = $client->full_name;
            $client->delete();
            // redirect
            Session::flash('message', 'Successfully deleted the client: '.$temp_name);
        }

        return redirect()->to(($request->input('redirect_to')));
    }

    /**
     * Display the default interest rate of a client.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function interestrate($id)
    {
        if (request()->ajax()) {
            $client = Client::findOrFail($id);

            return $client->interest_rate;
        }

        return redirect()->route('home');
    }

    /**
     * Display the default interest rate of a client.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function defaultmaterials($id)
    {
        if (request()->ajax()) {
            $client = Client::findOrFail($id);

            return $client->default_materials;
        }

        return redirect()->route('home');
    }

    public function workorders($id)
    {
        if (request()->ajax()) {
            $client = Client::findOrFail($id);
            $work_orders = $client->work_orders->pluck('number', 'id')->toArray();
            $data = [
                'work_orders' => $work_orders,
            ];

            return view('admin.clients.components.workorders', $data);
        }

        return redirect()->route('home');
    }

    public function setfilter(Request $request)
    {
        if ($request->exists('search')) {
            if ($request->search == '') {
                session()->forget('client_filter.search');
            } else {
                session(['client_filter.search' => $request->search]);
            }
        }
        if ($request->exists('approval_status')) {
            session(['client_filter.approval_status' => $request->approval_status]);
        }
        if ($request->exists('client_status')) {
            session(['client_filter.client_status' => $request->client_status]);
        }

        return redirect()->route('clients.index');
    }

    public function resetfilter(Request $request)
    {
        //dd('enterd');
        session()->forget('client_filter');

        return redirect()->route('clients.index');
    }

    public function enable(Request $request, $id)
    {
        //dd('enterd');
        $client = Client::findOrFail($id);
        $client->status = 4;
        $client->save();
        $users = $client->users;
        foreach ($users as $user) {
            $user->status = 1;
            $user->save();
        }
        //return redirect()->to (route('clients.index') . '?page=' . $request->page );
        return redirect()->back();
    }

    public function disable(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        $client->status = 3;
        $client->save();
        $users = $client->users;
        foreach ($users as $user) {
            $user->status = 0;
            $user->save();
        }
        //return redirect()->to (route('clients.index') . '?page=' . $request->page );
        return redirect()->back();
    }

    public function cancelSubscription($client_id)
    {
        $client = Client::where('id', $client_id)->first();
        $client->expiration = null;
        $client->save();
        Session::flash('message', 'Subscription was canceled.');

        return redirect()->back();
    }
}
