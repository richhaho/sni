<?php

namespace App\Http\Controllers\Researcher;

use App\ContactInfo;
use App\Entity;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;

class HotContactsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entities = Entity::hot()->orderBy('firm_name')->paginate(10);
        Session::put('backUrl', \URL::full());
        $data = [
            'entities' => $entities,
        ];

        return view('researcher.hotcontacts.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $gender = [
            'none' => 'Select one..',
            'female' => 'Female',
            'male' => 'Male',
        ];

        $types = [
            'none' => 'Select one..',
            'customer' => 'Order by',
            'gc' => 'General Contractor',
            'bond' => 'Bond Firm',
            'owner' => 'Property Owner',
            'leaser' => 'Lease Holder',
            'copy' => 'Copy Recipients',
        ];

        $data = [
            'gender' => $gender,
            'types' => $types,
        ];

        return view('researcher.hotcontacts.create', $data);
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
            'firm_name' => 'required_without_all:first_name,last_name',

            'address_1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
        ]);

        $entity = Entity::create($request->all());
        $xdata = $request->all();
        if (strlen($xdata['first_name']) == 0) {
            $xdata['first_name'] = ' ';
        }
        if (strlen($xdata['last_name']) == 0) {
            $xdata['last_name'] = ' ';
        }
        $contact = ContactInfo::create($xdata);

        $contact->entity_id = $entity->id;
        $contact->primary = 1;

        if ($request->has('sni_client')) {
            $contact->sni_client = 1;
        } else {
            $contact->sni_client = 0;
        }

        if ($request->has('use_on_client')) {
            $contact->use_on_client = 1;
        } else {
            $contact->use_on_client = 0;
        }
        $contact->save();

        if ($request->input('firm_name') == '') {
            $entity->firm_name = trim($contact->first_name.' '.$contact->last_name);
            $entity->save();
        }

        Session::flash('message', 'New contact have been created successfully');

        return redirect()->to(($request->input('redirects_to')));
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
        $entity = Entity::findOrFail($id);

        $types = [
            'none' => 'Select one..',
            'customer' => 'Order by',
            'gc' => 'General Contractor',
            'bond' => 'Bond Firm',
            'owner' => 'Property Owner',
            'leaser' => 'Lease Holder',
            'copy' => 'Copy Recipients',
        ];
        $data = [
            'entity' => $entity,
            'types' => $types,

        ];

        return view('researcher.hotcontacts.edit', $data);
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
            'firm_name' => 'required',
        ]);

        $entity = Entity::findOrFail($id);
        $temp_name = $entity->full_name;
        $entity->update($request->all());
        Session::flash('message', 'Successfully updated the client: '.$temp_name);
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
    public function destroy(Request $request, $id)
    {
        $entity = Entity::findOrFail($id);
        foreach ($entity->contacts as $xc) {
            $xc->delete();
        }
        $entity->delete();

        // redirect
        Session::flash('message', 'Successfully deleted the contact: '.$entity->firm_name);
        if ($request->has('redirects_to')) {
            return redirect()->to($request->redirects_to);
        } else {
            return redirect()->route('hotcontacts.index');
        }
    }
}
