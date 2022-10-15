<?php

namespace App\Http\Controllers\Researcher;

use App\Client;
use App\Http\Controllers\Controller;
use App\Role;
use App\User;
use Hash;
use Illuminate\Http\Request;
use Session;

class ManageClientUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($client_id)
    {
        $client = Client::findOrFail($client_id);
        $users = $client->users()->paginate(25);
        $data = [
            'users' => $users,
            'client' => $client,
        ];

        return view('researcher.manageclientusers.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($client_id)
    {
        $client = Client::findOrFail($client_id);
        $data = [
            'client' => $client,
        ];

        return view('researcher.manageclientusers.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $client_id)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users,email',
            'new_password' => 'required|confirmed|min:8',
        ]);

        $data = $request->all();
        $data['password'] = str_random(10);
        $user = User::create($data);

        $default_role = Role::where('name', 'client-secondary')->get()->first();
        $user->attachRole($default_role);
        $user->password = Hash::make($request->new_password);
        $user->client_id = $client_id;
        $user->save();

        //remove next line if
        $user->confirmEmail();

        Session::flash('message', 'User '.$user->full_name.' successfully created.');

        return redirect()->route('clientusers.index', $client_id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($client_id, $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($client_id, $id)
    {
        $client = Client::findOrFail($client_id);
        $user = User::findOrFail($id);
        $data = ['user' => $user, 'client' => $client];

        return view('researcher.manageclientusers.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $client_id, $id)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users,email,'.$id,
            'new_password' => 'nullable|confirmed|min:8',
        ]);

        $user = User::findOrFail($id);
        $user->update($request->all());
        if ($request->has('new_password')) {
            $user->password = Hash::make($request->new_password);
        }
        $user->save();

        Session::flash('message', 'User '.$user->full_name.' successfully updated.');

        return redirect()->route('clientusers.index', $client_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($client_id, $id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        Session::flash('message', 'User '.$user->full_name.' successfully deleted.');

        return redirect()->route('clientusers.index', $client_id);
    }
}
