<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
    }
    
    
    protected function authenticated($request, $user)
    {


        if($user->hasRole(['admin'])) {
            Session::put('user_role','admin');
            return redirect()->intended(route('admin'));
        }
        if($user->hasRole(['researcher'])) {
            Session::put('user_role','researcher');
            //return redirect()->intended(route('researcher'));
            if ($user->restricted) {
                return redirect()->intended(route('research.index'));
            }
            return redirect()->intended(route('admin'));
        }

        if($user->hasRole(['client','client-secondary'])) {
            if ($user->approve_status=='approved'){
                if($user->client) {
                    Session::put('user_role','client');
                    return redirect()->intended(route('client.index'));
                }
            }
            auth()->logout();
            return redirect()->route('login');
            
        }

    }
    
    
    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $credentials = $request->only($this->username(), 'password');
        $credentials['status'] = 1;
        return $credentials;
    }
}
