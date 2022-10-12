<?php

namespace App\Http\Controllers\Researcher;

use App\Http\Controllers\Controller;
use App\Role;
use Illuminate\Http\Request;
use Session;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::All();

        $types = ['Administration' => 'Administration', 'Clients' => 'Clients', 'Researcher' => 'Researcher'];
        $data = [
            'types' => $types,
            'roles' => $roles,

        ];

        return view('researcher.roles.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'new_name' => 'required|alpha_dash|unique:roles,name',
            'new_display_name' => 'required|unique:roles,display_name',
        ]);

        $role = new Role();
        $role->name = $request->new_name;
        $role->display_name = $request->new_display_name;
        $role->description = $request->new_description;
        $role->type = $request->new_type;
        $role->save();

        Session::flash('message', 'Role '.$role->display_name.' successfully created.');

        return redirect()->route('roles.index');
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
            'name' => 'required|alpha_dash|unique:roles,name,'.$id,
            'display_name' => 'required|unique:roles,display_name,'.$id,
        ]);
        $role = Role::findOrFail($id);
        $role->update($request->all());

        Session::flash('message', 'Role '.$role->display_name.' successfully updated.');

        return redirect()->route('roles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $temp_name = $role->display_name;
        $role->delete();

        Session::flash('message', 'Role '.$temp_name.' successfully deleted.');

        return redirect()->route('roles.index');
    }
}
