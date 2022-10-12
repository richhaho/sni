<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Role;
use App\User;
use Hash;
use Illuminate\Http\Request;
use Session;

class ManageUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::isRole('admin')->paginate(25);
        $researchers = User::isRole('researcher')->paginate(25);

        $data = [
            'users' => $users,
            'researchers' => $researchers,
        ];

        return view('admin.manageusers.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::where('type', 'Administration')->orwhere('type', 'Researcher')->pluck('display_name', 'id');
        $data = [
            'roles' => $roles,
        ];

        return view('admin.manageusers.create', $data);
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
            'email' => 'required|unique:users,email',
            'new_password' => 'required|confirmed|min:8',
        ]);

        $data = $request->all();
        $data['password'] = str_random(10);
        $user = User::create($data);
        $default_role = Role::findOrFail($request->role_id);

        $user->confirmEmail();

        $user->attachRole($default_role);
        $user->status = 1;
        $user->password = Hash::make($request->new_password);
        $user->save();

        //remove next line if

        Session::flash('message', 'User '.$user->full_name.' successfully created.');

        return redirect()->route('users.index');
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
        $user = User::findOrFail($id);
        $roles = Role::where('type', 'Administration')->orwhere('type', 'Researcher')->pluck('display_name', 'id');

        $data = [
            'user' => $user,
            'roles' => $roles,
        ];

        return view('admin.manageusers.edit', $data);
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
            'email' => 'required|unique:users,email,'.$id,
            'new_password' => 'nullable|confirmed|min:8',
        ]);

        $user = User::findOrFail($id);
        $user->update($request->all());

        if ($request->has('new_password')) {
            $user->password = Hash::make($request->new_password);
        }
        $user->save();

        $user->roles()->detach();
        $default_role = Role::findOrFail($request->role_id);
        $user->attachRole($default_role);

        Session::flash('message', 'User '.$user->full_name.' successfully updated.');

        return redirect()->route('users.index');
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

        Session::flash('message', 'User '.$user->full_name.' successfully deleted.');

        return redirect()->route('users.index');
    }
}
