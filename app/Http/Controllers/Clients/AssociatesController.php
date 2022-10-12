<?php

namespace App\Http\Controllers\Clients;

use App\ContactInfo;
use App\Entity;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\Request;
use Session;

class AssociatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($entity_id)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($entity_id)
    {
        $entity = Entity::findOrFail($entity_id);

        $gender = [
            'none' => 'Select one..',
            'female' => 'Female',
            'male' => 'Male',
        ];
        $data = [
            'gender' => $gender,
            'entity' => $entity,
        ];

        return view('client.associates.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($entity_id, Request $request)
    {
        $this->validate($request, [

            'address_1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
        ]);
        $entity = Entity::findOrFail($entity_id);
        $xdata = $request->all();
        if (strlen($xdata['first_name']) == 0) {
            $xdata['first_name'] = ' ';
        }
        if (strlen($xdata['last_name']) == 0) {
            $xdata['last_name'] = ' ';
        }
        $contact = ContactInfo::create($xdata);

        $contact->entity_id = $entity_id;
        //here we check for Primary
        if ($request->has('primary_contact')) {
            //remove all primary contacts fro entity
            DB::table('contact_infos')->where('entity_id', $entity_id)->where('primary', '1')->update(['primary' => 0]);
            $contact->primary = 1;
        } else {
            $contact->primary = 0;
        }
        $contact->save();

        Session::flash('message', 'New associate have been created successfully');

        return redirect()->route('client.contacts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($entity_id, $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $entity_id, $id)
    {
        $entity = Entity::findOrFail($entity_id);
        $associate = ContactInfo::findOrFail($id);
        if ($entity->client_id != Auth::user()->client->id) {
            abort(403);
        }
        if ($associate->entity->client_id != Auth::user()->client->id) {
            abort(403);
        }

        //$this->authorize('wizard',  $entity);

        $gender = [
            'none' => 'Select one..',
            'female' => 'Female',
            'male' => 'Male',
        ];

        if ($request->has('back')) {
            $back = $request->back;
            $job = $request->job;
            $parties = $request->parties;
            $workorder = $request->workorder;
        } else {
            $back = '';
            $job = '';
            $parties = '';
            $workorder = '';
        }
        $data = [
            'back' => $back,
            'job' => $job,
            'parties' => $parties,
            'entity' => $entity,
            'associate' => $associate,
            'workorder' => $workorder,
            'gender' => $gender,
        ];

        return view('client.associates.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $entity_id, $id)
    {
        $this->validate($request, [

            'address_1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
        ]);

        $entity = Entity::findOrFail($entity_id);
        $contact = ContactInfo::findOrFail($id);
        $xdata = $request->all();
        if (strlen($xdata['first_name']) == 0) {
            $xdata['first_name'] = ' ';
        }
        if (strlen($xdata['last_name']) == 0) {
            $xdata['last_name'] = ' ';
        }
        $contact->update($xdata);

        if ($request->has('primary_contact')) {
            //remove all primary contacts fro entity
            DB::table('contact_infos')->where('entity_id', $entity_id)->update(['primary' => 0]);
            $contact->primary = 1;
        } else {
            $contact->primary = 0;
        }
        $contact->save();

        $temp_name = $contact->full_name;

        Session::flash('message', 'Successfully updated the associate: '.$temp_name);

        if ($request->has('back')) {
            if ($request->back != '') {
                return redirect()->route('parties.edit', [$request->job, $request->parties]);
            }
        }

        //return redirect()->route('client.contacts.index');
        if (str_contains($request->input('redirects_to'), '#collapse')) {
            $xurl = $request->input('redirects_to').'#collapse'.$entity_id;
        } else {
            $xurl = $request->input('redirects_to');
        }

        return redirect()->to($xurl);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($entity_id, $id)
    {
        $entity = Entity::findOrFail($entity_id);
        $contact = ContactInfo::findOrFail($id);
        $temp_name = $contact->firm_name;
        $contact->delete();

        // redirect
        Session::flash('message', 'Successfully deleted the Associate: '.$temp_name);

        return redirect()->route('client.contacts.index');
    }

    public function enable(Request $request, $entity_id, $id)
    {
        $entity = Entity::findOrFail($entity_id);
        $contact = ContactInfo::findOrFail($id);
        $temp_name = $contact->firm_name;
        $contact->status = 1;
        $contact->save();
        // redirect
        Session::flash('message', 'Successfully enabled the Associate: '.$temp_name);

        return redirect()->to(route('client.contacts.index').'?page='.$request->page.'#collapse'.$entity_id);
    }

    public function disable(Request $request, $entity_id, $id)
    {
        $entity = Entity::findOrFail($entity_id);
        $contact = ContactInfo::findOrFail($id);
        $temp_name = $contact->firm_name;
        $contact->status = 0;
        $contact->save();
        // redirect
        Session::flash('message', 'Successfully disabled the Associate: '.$temp_name);

        return redirect()->to(route('client.contacts.index').'?page='.$request->page.'#collapse'.$entity_id);
    }
}
