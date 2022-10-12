<?php

namespace App\Http\Controllers\Researcher;

use App\ContactInfo;
use App\Entity;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Session;

class HotAssociatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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

        return view('researcher.hotassociates.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $entity_id)
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
            DB::table('contact_infos')->where('entity_id', $entity_id)->update(['primary' => 0]);
            $contact->primary = 1;
        } else {
            $contact->primary = 0;
        }

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

        Session::flash('message', 'New associate have been created successfully');
        if (str_contains($request->input('redirects_to'), '#collapse')) {
            $xurl = $request->input('redirects_to').'#collapse'.$entity_id;
        } else {
            $xurl = $request->input('redirects_to');
        }

        return redirect()->to($xurl);
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
    public function edit($entity_id, $id)
    {
        $entity = Entity::findOrFail($entity_id);
        $associate = ContactInfo::findOrFail($id);

        $gender = [
            'none' => 'Select one..',
            'female' => 'Female',
            'male' => 'Male',
        ];

        $data = [
            'entity' => $entity,
            'associate' => $associate,
            'gender' => $gender,
        ];

        return view('researcher.hotassociates.edit', $data);
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
        $temp_name = $contact->full_name;
        if (count($contact->links) > 0) {
            if ($request->has('update_all')) {
                foreach ($contact->links as $xcontact) {
                    $xcontact->update($request->all());
                }
                $temp_name .= ' and all his links';
            } else {
                foreach ($contact->links as $xcontact) {
                    $xcontact->hot_id = 0;
                    $xcontact->save();
                }
            }
        }

        Session::flash('message', 'Successfully updated the associate: '.$temp_name);
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
    public function destroy(Request $request, $entity_id, $id)
    {
        $contact = ContactInfo::findOrFail($id);
        $temp_name = $contact->firm_name;
        $contact->delete();

        // redirect
        Session::flash('message', 'Successfully deleted the Associate: '.$temp_name);

        return redirect()->to($request->input('redirects_to'));
    }

    public function enable(Request $request, $entity_id, $id)
    {
        $contact = ContactInfo::findOrFail($id);
        $temp_name = $contact->firm_name;
        $contact->status = 1;
        $contact->save();
        // redirect
        Session::flash('message', 'Successfully enabled the Associate: '.$temp_name);

        return redirect()->to(route('hotcontacts.index').'?page='.$request->page.'#collapse'.$entity_id);
    }

    public function disable(Request $request, $entity_id, $id)
    {
        $contact = ContactInfo::findOrFail($id);
        $temp_name = $contact->firm_name;
        $contact->status = 0;
        $contact->save();
        // redirect
        Session::flash('message', 'Successfully disabled the Associate: '.$temp_name);

        return redirect()->to(route('hotcontacts.index').'?page='.$request->page.'#collapse'.$entity_id);
    }
}
