<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Client;
use App\Entity;
use App\ContactInfo;
use Session;

class ContactsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($client_id)
    {
        $client = Client::findOrFail($client_id);
        $entities = $client->entities()->orderBy('firm_name');
        //echo json_encode($entities);return;
        Session::put('backUrl',\URL::full());
        $data =[
            'client_name' => $client->company_name,
            'client_id' => $client->id,
            'entities' => $entities->paginate(10)
        ];
        
        return view('admin.contacts.index',$data);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($client_id)
    {
       
        $client = Client::findOrFail($client_id);
        $client_name = $client->company_name;
        $client_id = $client->id;
        
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
         'client_name' => $client_name,
         'client_id' => $client_id,
         'gender' => $gender,
          'types' => $types
        ];
        return view('admin.contacts.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($client_id,Request $request)
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
        if (strlen($xdata['first_name'])==0) {
            $xdata['first_name'] = " ";
        }
        if (strlen($xdata['last_name'])==0) {
           $xdata['last_name'] = " ";
        }
        $contact = ContactInfo::create($xdata);
        
        $contact->entity_id = $entity->id;
        $contact->primary = 1;
        $contact->save();
        
        if ($request->input('firm_name') == "") {
            $entity->firm_name = trim($contact->first_name . " " . $contact->last_name);
            $entity->save();
        }
            
        Session::flash('message', 'New contact have been created successfully');
        return redirect()->route('contacts.index',$client_id);
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
    public function edit($client_id,$id)
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
            'types' => $types
                
        ];
        return view('admin.contacts.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$client_id, $id)
    {
        $this->validate($request, [
            'firm_name' => 'required',
        ]);
        
        $entity = Entity::findOrFail($id);
        $temp_name = $entity->full_name;
        $entity->update($request->all());
        Session::flash('message', 'Successfully updated the client: ' .$temp_name);
        
        return redirect()->route('contacts.index',$entity->client_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($client_id,$id)
    {
        $entity = Entity::findOrFail($id);
        $temp_name = $entity->firm_name;
        $entity->delete();

        // redirect
        Session::flash('message', 'Successfully deleted the contact: ' .$temp_name);
        
        return redirect()->route('contacts.index',$client_id);
    }
}
