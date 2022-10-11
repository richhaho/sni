<?php

namespace App\Http\Controllers\Researcher;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Client;
use Session;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Password;
use App\Role;
use App\Entity;
use App\ContactInfo;

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
           if(session('client_filter.search') != ''){
               if (is_numeric(session('client_filter.search'))) {
                   $clients->where('id',session('client_filter.search'));
               } else {
                  $clients = Client::search(session('client_filter.search'));
               }
           }
       }
       
       $clients = $clients->orderBy('company_name')->paginate(15);
       
       Session::put('backUrl',\URL::full());
       $data = [
           'clients' => $clients,
           
       ];
         
       return view('researcher.clients.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       $clients =  Client::get()->pluck('full_name', 'id')->prepend('Select one...',0);
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
         'print_method'=>$print_method,
         'billing_type' => $billing_type,
         'send_certified' => $send_certified,
         'gender' => $gender
        ];
        return view('researcher.clients.create',$data);
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
            'zip' => 'required'
        ]);
        
        $client= Client::create($request->all());
        
        //lets create contact and associate...
        //create entity
        $entity = new Entity();
     
        if (strlen(trim($client->company_name)) == 0) {
            $entity->firm_name = $client->full_name;
        } else {
            $entity->firm_name = $client->company_name;
        }
        $entity->latest_type = "client";
        $entity->client_id  = $client->id;
        $entity->save();
  
        //create associate
        $associate = new ContactInfo();
        if(strlen($request->first_name) > 0) {
            $associate->first_name =  $request->first_name;
        } else {
            $associate->first_name =  " ";
        }
        if(strlen($request->last_name) > 0) {
            $associate->last_name =  $request->last_name;
        } else {
            $associate->last_name =  " ";
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
           
           $default_role= Role::where('name', 'client')->first();
           $xuser->attachRole( $default_role);
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
        Session::flash('message', 'Successfully updated the client: ' .$temp_name);
    
        return redirect()->route('contacts.index',$client->id);
        
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

        $clients =  Client::get()->pluck('full_name', 'id')->prepend('Select one...',0);
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
         'client' => $client,
         'clients' => $clients,
         'print_method'=>$print_method,
         'billing_type' => $billing_type,
         'send_certified' => $send_certified,
         'gender' => $gender
        ];
        // show the edit form and pass the nerd
        return view('researcher.clients.edit',$data);
            
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
        'zip' => 'required'
        ]);
        
        $client = Client::findOrFail($id);
        $temp_name = $client->company_name;
        $client->update($request->all());
        Session::flash('message', 'Successfully updated the client: ' .$temp_name);
        
        return redirect()->route('clients.edit',$client->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,Request $request)
    {
        $client = Client::findOrFail($id);
        $temp_name = $client->full_name;
        $client->delete();

        // redirect
        Session::flash('message', 'Successfully deleted the client: ' .$temp_name);
        
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
            $work_orders = $client->work_orders->pluck('number','id')->toArray();;
            $data = [
                'work_orders' => $work_orders
            ];
            return view('researcher.clients.components.workorders',$data);
        }

        return redirect()->route('home');
    }
    
    public function setfilter (Request $request) {
      
        if ($request->exists('search')) {
            if($request->search == '' ) {
                session()->forget('client_filter.search');
            } else {
                session(['client_filter.search' => $request->search]);
            }
        }
        
        
      
      
        return redirect()->route('clients.index');
    }
    
    public function resetfilter (Request $request) {
        //dd('enterd');
         session()->forget('client_filter');
        return redirect()->route('clients.index');
    }
    
     public function enable (Request $request,$id) {
        //dd('enterd');
         $client = Client::findOrFail($id);
         $client->status = 4;
         $client->save();
       //return redirect()->to (route('clients.index') . '?page=' . $request->page );
         return redirect()->back();
    }
    
     public function disable (Request $request, $id) {
        $client = Client::findOrFail($id);
        $client->status = 3;
        $client->save();
        
        //return redirect()->to (route('clients.index') . '?page=' . $request->page );
        return redirect()->back();
    }
}
