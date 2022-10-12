<?php

namespace App\Http\Controllers\Clients;

use App\Attachment;
use App\Client;
use App\CompanySetting;
use App\ContactInfo;
use App\Custom\Payeezy;
use App\Entity;
use App\Http\Controllers\Controller;
use App\Job;
use App\Mail\SubscriptionPaymentMade;
use App\Notifications\VerifyUserEmail;
use App\SubscriptionPayment;
use Auth;
use iio\libmergepdf\Merger;
use Illuminate\Http\Request;
use Mail;
use Response;
use Session;
use Storage;

class ClientController extends Controller
{
    public function index()
    {
        if (Auth::user()->approve_status == 'approved') {
        } else {
            auth()->logout();

            return redirect()->route('login');
        }

        $client = Auth::user()->client;
        if ($client->service && $client->subscription && $client->subscriptionRate == 0 && $client->expiration && $client->expiration < date('Y-m-d H:i:s')) {
            $client->expiration = date('Y-m-d H:i:s', strtotime($client->expiration.' +'.$client->subscription.'days'));
        }
        if ($client->status == 0) {
            return redirect()->route('client.create');
        }

        $notices_count = $client->work_orders()->whereNotIn('work_orders.status', ['completed', 'cancelled', 'cancelled charge', 'cancelled no charge', 'closed', 'cancelled duplicate', 'cancelled duplicate needs credit', 'temporary'])->where('work_orders.deleted_at', null)->count();
        $invoices_count = $client->invoices()->where('payed_at', null)->count();
        $jobs_count = $client->jobs->where('status', '!=', 'closed')->where('deleted_at', null)->count();
        $jobs = $client->jobs->where('status', '!=', 'closed')->where('deleted_at', null);

        $jobs_withWorkorder = Job::where('client_id', $client->id)->whereHas('workorders', function ($q) {
            $q->where('status', '!=', 'temporary');
        })->where('status', '!=', 'closed')->where('deleted_at', null)
        ->get()->pluck('id')->toArray();
        $jobs_noworkorder = $jobs->whereNotIn('id', $jobs_withWorkorder);
        $job_count_withoutwork = count($jobs_noworkorder);

        $data = [
            'notices_count' => $notices_count,
            'invoices_count' => $invoices_count,
            'jobs_count' => $jobs_count,
            'jobs' => $jobs_noworkorder,
            'job_count_withoutwork' => $job_count_withoutwork,
        ];

        return view('client.index', $data);
    }

    public function create()
    {
        $client = Auth::user()->client;
        if ($client->status == 0) {
            Session::flash('message', 'In order to proceed we need more Information about you, Please complete the following form.');
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
            'client' => 'I Print',
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

        $data = [
            'client' => $client,
            'clients' => $clients,
            'print_method' => $print_method,
            'billing_type' => $billing_type,
            'send_certified' => $send_certified,
            'gender' => $gender,
            'override_weekly' => $override_weekly,
            'override_payment' => $override_payment,
            'override_notice' => $override_notice,
            'override_emailReminder' => $override_emailReminder,
            'override_smsReminder' => $override_smsReminder,
            'override_lastday_over' => $override_lastday_over,
            'notification_setting' => $notification_setting,
        ];
        // show the edit form and pass the nerd
        return view('client.create', $data);
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
            'address_1' => 'required',
            'phone' => 'required',
            'country' => 'required',
            'city' => 'required',
            'default_materials' => 'required',

        ]);

        $data = $request->all();
        $client = Client::findOrFail($id);

        $temp_name = $client->company_name;
        $client->update($data);
        $client->notification_setting = $request['notification_setting'];
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
        // if ($request->has('autobatch')) {
        //     $client->autobatch= 1;
        // }else{
        //     $client->autobatch= 0;
        // }

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

        if ($request->service == 'self') {
            $client->return_address_type = 'client';
        } else {
            $client->return_address_type = 'sni';
        }
        $client->save();

        $user = Auth::user();
        if ($user->id == $client->client_user_id) {
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->save();
        }

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

        Session::flash('message', 'Successfully updated the client: '.$temp_name);

        return redirect()->route('client.create');
    }

    public function notAllow(Request $request, $id)
    {
        $client = Auth::user()->client;
        $client->allow_email_reminder = 0;
        $client->save();

        Session::flash('message', 'You have now been removed from reminders.');

        return redirect()->route('client.create');
    }

    public function interestrate($id)
    {
        if (request()->ajax()) {
            $client = Client::findOrFail($id);

            return $client->interest_rate;
        }

        return redirect()->route('home');
    }

    public function defaultmaterials($id)
    {
        if (request()->ajax()) {
            $client = Client::findOrFail($id);

            return $client->default_materials;
        }

        return redirect()->route('home');
    }

    public function printAttachment($id)
    {
        $url = route('client.attachment.view', $id);
        $data = [
            'url' => $url,
        ];

        return view('admin.mailing.exeprint', $data);
    }

    public function printNotice($id)
    {
        $exist_attach = Attachment::where('id', base64_decode($id))->get();
        if (count($exist_attach) < 1) {
            Session::flash('message', 'Attachment has already been deleted.');

            return redirect()->route('client.invoices.index');
        }

        $attachment = Attachment::findOrFail(base64_decode($id));
        $content = Storage::get($attachment->file_path);

        return Response::make($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Print-Notice-attachment'.$id.'.pdf"',
        ]);
    }

    public function viewAttachment($id)
    {
        $attachment = Attachment::findOrFail($id);
        $content = Storage::get($attachment->file_path);

        return Response::make($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="mailing-final-'.$id.'.pdf"',
        ]);
    }

    public function printSelected(Request $request)
    {
        if (! $request->attach) {
            Session::flash('message', 'You did not select notices to print. Please select.');

            return redirect()->route('client.notices.index');
        }
        $m = new Merger();
        foreach ($request->attach as $key => $value) {
            $attachment = Attachment::findOrFail($key);
            $xpdf = Storage::get($attachment->file_path);
            $m->addRaw($xpdf);
        }
        $pdf_file = $m->merge();

        return  Response::make($pdf_file, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="printSelected.pdf"',
        ]);
        $data = [
            'url' => $url,
        ];

        return view('admin.mailing.exeprint', $data);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function renews(Request $request)
    {
        $client = Auth::user()->client;
        if (isset($request->isUpdate)) {
            $client->service = $request->service;
            $client->subscription = $request->subscription;
            $client->description = $request->description;
            if ($request->service == 'self') {
                $client->return_address_type = 'client';
            } else {
                $client->return_address_type = 'sni';
            }
            $client->save();

            return $client->subscriptionRate;
        }
        if (! $client->subscriptionRate) {
            return redirect()->back();
        }

        // subscription payment made
        $data = $request->all();
        $data['token'] = json_decode($request->token, true);

        if (strlen($client->payeezy_type) == 0) {
            $client->payeezy_type = $data['token']['type'];
            $client->payeezy_value = $data['token']['value'];
            $client->payeezy_cardholder_name = $data['token']['cardholder_name'];
            $client->payeezy_exp_date = $data['token']['exp_date'];
            $client->save();
        }
        $company = CompanySetting::first();

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
            'amount' => number_format($client->subscriptionRate, 2, '', ''),
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

        $payment = new SubscriptionPayment();
        $payment->type = 'credit_card';
        $payment->amount = $client->subscriptionRate;
        $payment->client_id = $client->id;
        $payment->reference = $result_data->correlation_id;
        $payment->gateway = 'payeezy';
        $payment->transaction_status = $result_data->transaction_status;
        $payment->log_result = $result;
        $payment->user_id = Auth::user()->id;
        $payment->service_type = $client->service.'-service';
        $payment->subscription_period = $client->subscription.'-day';
        $payment->subscription_rate = $client->subscriptionRate;
        $payment->expiration = date('Y-m-d H:i:s', strtotime('+'.$client->subscription.'days'));
        $payment->save();

        $client->expiration = $payment->expiration;
        $client->save();

        if ($result_data->transaction_status == 'approved') {
            $users = Auth::user()->client->activeusers;
            foreach ($users as $user) {
                $mailto[] = $user->email;
            }

            if (json_encode(unserialize($client->override_payment)) != 'false' && json_encode(unserialize($client->override_payment)) != 'null') {
                Mail::to(unserialize($client->override_payment))->send(new SubscriptionPaymentMade($payment, $client));
            } else {
                $mailto = [];
                $users = $client->activeusers;
                foreach ($users as $user) {
                    $mailto[] = $user->email;
                }
                if (count($mailto) > 0) {
                    Mail::to($mailto)->send(new SubscriptionPaymentMade($payment, $client));
                }
            }
        }

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

    public function resendValidate()
    {
        $user = Auth::user();
        $user->notify(new VerifyUserEmail($user));

        return redirect()->back();
    }
}
