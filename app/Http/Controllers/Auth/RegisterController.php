<?php

namespace App\Http\Controllers\Auth;

use App\Client;
use App\Http\Controllers\Controller;
use App\Notifications\NewClientUser;
use App\Notifications\VerifyUserEmail;
use App\Role;
use App\SubscriptionRate;
use App\User;
use Hash;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('confirmEmail');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'agree' => 'required',
            'company_name' => 'required_without_all:first_name,last_name',
            'zip' => 'required',
            'address_1' => 'required',
            'phone' => 'required',
            'country' => 'required',
            'city' => 'required',
            'default_materials' => 'required',
            'g-recaptcha-response' => 'required',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        if ($data['honeypot'] != $data['g-recaptcha-response'] || ! $data['g-recaptcha-response']) {
            echo "Warning: You can't be registered.";

            return redirect()->route('login');
        }

        $client = new Client();
        $client->first_name = $data['first_name'];
        $client->last_name = $data['last_name'];
        $client->email = $data['email'];
        $client->status = 0;

        $client->company_name = strtoupper($data['company_name']);
        $client->title = strtoupper($data['title']);
        $client->gender = $data['gender'];
        $client->zip = strtoupper($data['zip']);
        $client->address_1 = strtoupper($data['address_1']);
        $client->address_2 = strtoupper($data['address_2']);
        $client->country = strtoupper($data['country']);
        $client->city = strtoupper($data['city']);
        $client->county = strtoupper($data['county']);
        $client->phone = strtoupper($data['phone']);
        $client->state = strtoupper($data['state']);
        $client->mobile = strtoupper($data['mobile']);
        $client->fax = strtoupper($data['fax']);
        $client->default_materials = strtoupper($data['default_materials']);
        $client->return_address_type = 'sni';

        $rate = SubscriptionRate::first();
        $client->self_30day_rate = $rate->self_30day_rate;
        $client->self_365day_rate = $rate->self_365day_rate;
        $client->full_30day_rate = $rate->full_30day_rate;
        $client->full_365day_rate = $rate->full_365day_rate;

        $client->save();

        //remove ferified key on array
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'client_id' => $client->id,
        ]);

        $user->status = 1;
        $user->approve_status = 'pending';
        $user->save();

        $client->client_user_id = $user->id;
        $client->save();

        $default_role = Role::where('name', 'client')->first();
        $user->attachRole($default_role);
        //$user->confirmEmail();
        $user->notify(new VerifyUserEmail($user));
        for ($i = 0; $i <= 10000000; $i++) {
        }

        $ndata = [
            'note' => $client->id,
            'entered_at' => $client->created_at->format('Y-m-d H:i:s'),
        ];
        $admin_users = User::where('status', 1)->isRole(['admin'])->get();
        Notification::send($admin_users, new NewClientUser($client->id, $ndata, $user->first_name.' '.$user->last_name));

        return $user;
    }

    public function confirmEmail($token)
    {
        $usercount = count(User::where('email_token', $token)->get());
        if ($usercount > 0) {
            $user = User::where('email_token', $token)->firstOrFail();
            $user->confirmEmail();

            return view('confirm', ['used' => false]);
        } else {
            return view('confirm', ['used' => true]);
        }
    }

    public function validationEmail(Request $request)
    {
        $usercount = count(User::withTrashed()->where('email', $request->email)->get());
        if ($usercount > 0) {
            return 'exist';
        } else {
            return 'unique';
        }
    }
}
