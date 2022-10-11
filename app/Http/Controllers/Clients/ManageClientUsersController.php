<?php

namespace App\Http\Controllers\Clients;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\VerifyClientUserEmail;
use App\Client;
use App\User;
use App\Role;
use Session;
use Auth;
use Hash;

class ManageClientUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $client = Auth::user()->client;
        $users = $client->users()->paginate(25);
        $data = [
            'users' => $users,
            'client' =>  $client
        ];
        
        return view('client.manageclientusers.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $client = Auth::user()->client;
        $data = [
            'client' =>  $client
        ];
        
       return view('client.manageclientusers.create',$data);
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
             'first_name' => 'required',
             'last_name' => 'required',
             'new_password' => 'required|confirmed|min:8',
             'email' => 'required'
             ]);
            $exist_user = User::where('email',$request->email)->first();
            $data = $request->all();
            $old_user = User::onlyTrashed()->where('client_id',Auth::user()->client_id)->where('email',$request->email)->first();

            if($old_user) {
                //$old_user->restore();
                $old_user->deleted_at=null;
                $old_user->first_name = $data['first_name'];
                $old_user->last_name = $data['last_name'];
                $old_user->password =  Hash::make($request->new_password);
                $old_user->save();
                Session::flash('message', 'User with email ' . $old_user->email . ' successfully restored.');
                return redirect()->route('client.clientusers.index');
            } 
            if ($exist_user){
                Session::flash('message', 'Email ' . $exist_user->email . '  already exists.');
            }
            else {

             $data = $request->all();
             $data['password'] = str_random(10);
             $user = User::create($data);

                $default_role= Role::where('name', 'client-secondary')->get()->first();
                $user->attachRole($default_role);
                $user->password =  Hash::make($request->new_password);
                $user->client_id = Auth::user()->client_id;
                $user->status = $data['status'];
                $user->approve_status=Auth::user()->approve_status;
                $user->save();

             $user->confirmEmail();
             Session::flash('message', 'User ' . $user->full_name . ' successfully created.');
            }
        
        
        //$user->notify(new VerifyClientUserEmail($user));
        
        return redirect()->route('client.clientusers.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($client_id,$id)
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
         $client = Auth::user()->client;
         $user = User::findOrFail($id);
         
         if($user->client_id <> $client->id) {
            abort(403);
         }

        $data = ['user' =>$user ,'client' =>$client];
        return view('client.manageclientusers.edit',$data);
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
        'first_name' => 'required',
        'last_name' => 'required',
        'email' => 'required|unique:users,email,'. $id ,
        'new_password' => 'nullable|confirmed|min:8'
        ]);
         
        $user = User::findOrFail($id);
        $user->update($request->all());
        if ($request->has('new_password')) {
            $user->password =   Hash::make($request->new_password);
        }
        $user->save();
        
        Session::flash('message', 'User ' . $user->full_name . ' successfully updated.');
        return redirect()->route('client.clientusers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        Session::flash('message', 'User ' . $user->full_name . ' successfully deleted.');
        return redirect()->route('client.clientusers.index');
    }
} 
 