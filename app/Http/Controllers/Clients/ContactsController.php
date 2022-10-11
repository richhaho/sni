<?php

namespace App\Http\Controllers\Clients;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Client;
use App\Entity;
use App\ContactInfo;
use Session;
use Auth;

class ContactsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        $client = Auth::user()->client;
       //dd($client->company_name);
        $centity = $client->entities()->where('firm_name',$client->company_name);
        $new_id='-1';
        if (session('contact_filter.new') == 'created'){
            $new=$client->entities()->orderBy('created_at','desc')->first();
            if (isset($new->id)){
                $new_id=$new->id;
            }
        }
        $new_entity = $client->entities()->where('id',$new_id)->where('firm_name','!=',$client->company_name);

         
        //$entities =$client->entities()->where('firm_name','!=',$client->company_name)->orderBy('firm_name')->paginate(25);
        $entities =$client->entities()->where('firm_name','!=',$client->company_name)->where('id','!=',$new_id);

        if (session()->has('contact_filter.search')) {
           if(session('contact_filter.search') != ''){
               $entities = $entities->where('firm_name','like','%'.session('contact_filter.search').'%');
               $centity =$centity->where('firm_name','like','%'.session('contact_filter.search').'%');
               $new_entity =$new_entity->where('firm_name','like','%'.session('contact_filter.search').'%');
           } 
        }
        $entities =$entities->orderBy('firm_name');
        session()->forget('contact_filter.new');
        Session::put('backUrl',\URL::full());
        $data =[
            'client_name' => $client->company_name,
            'client_id' => $client->id,
            'centity' => $centity->paginate(1),
            'entities' => $entities->paginate(10),
            'new_entity' => $new_entity->get(),
        ];
        
        return view('client.contacts.index',$data);
        
    }
    public function setfilter (Request $request) {
      
        if ($request->exists('search')) {
            if($request->search == '' ) {
                session()->forget('contact_filter.search');
            } else {
                session(['contact_filter.search' => $request->search]);
            }
        }
        
        return redirect()->route('client.contacts.index');
    }
    public function resetfilter (Request $request) {
        //dd('enterd');
         session()->forget('contact_filter');
        return redirect()->route('client.contacts.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
  
        $client = Auth::user()->client;
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
        return view('client.contacts.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        session()->forget('contact_filter.search');

        $client_id = Auth::user()->client_id;
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
        session(['contact_filter.new' => 'created']);    
        Session::flash('message', 'New contact have been created successfully');
        return redirect()->route('client.contacts.index');
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
        if($entity->client_id <> Auth::user()->client->id) {
            abort(403);
        }
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
        return view('client.contacts.edit',$data);
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
        if($entity->client_id <> Auth::user()->client->id) {
            abort(403);
        }
        $temp_name = $entity->full_name;
        $entity->update($request->all());
        Session::flash('message', 'Successfully updated the client: ' .$temp_name);

        if (str_contains($request->input('redirects_to'),'#collapse')) {
                $xurl = $request->input('redirects_to'). "#collapse" . $entity_id;
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
    public function destroy($id)
    {
        $entity = Entity::findOrFail($id);
        if($entity->client_id <> Auth::user()->client->id) {
            abort(403);
        }
        $temp_name = $entity->firm_name;
        
        $entity->delete();

        // redirect
        Session::flash('message', 'Successfully deleted the contact: ' .$temp_name);
        
        return redirect()->route('client.contacts.index');
    }
}
