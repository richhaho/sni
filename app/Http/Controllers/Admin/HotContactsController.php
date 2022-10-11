<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Entity;
use App\ContactInfo;
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
        
        $new_id='-1';
        $new_entity =array();
        if (session('hot_filter.new') == 'created'){
            $new=Entity::query()->whereNull('deleted_at')->orderBy('created_at','desc')->first();
            $new_entity = Entity::where('id',$new->id);
            $new_id=$new->id;
        }


        
        $entities = Entity::query()->whereNull('deleted_at')->where('id','!=',$new_id);
        if (session()->has('hot_filter.search')) {
           if(session('hot_filter.search') != ''){
               $entities = Entity::where('firm_name','like','%'.session('hot_filter.search').'%')->whereNull('deleted_at')->where('id','!=',$new_id);
               if ($new_id!='-1'){$new_entity=$new_entity->where('firm_name','like','%'.session('hot_filter.search').'%');}
           } 
        }
        $entities = $entities->hot()->orderBy('firm_name')->paginate(10);
        if (session('hot_filter.new') == 'created'){
            $new_entity=$new_entity->get();
        }
        session()->forget('hot_filter.new');
        Session::put('backUrl',\URL::full());
        
        $data =[
            'entities' => $entities,
            'new_entity' => $new_entity,
        ];
        
        return view('admin.hotcontacts.index',$data);
    }

    public function setfilter (Request $request) {
      
        if ($request->exists('search')) {
            if($request->search == '' ) {
                session()->forget('hot_filter.search');
            } else {
                session(['hot_filter.search' => $request->search]);
            }
        }
        
        return redirect()->route('hotcontacts.index');
    }
    public function resetfilter (Request $request) {
        //dd('enterd');
         session()->forget('hot_filter');
        return redirect()->route('hotcontacts.index');
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
         'types' => $types
        ];
        return view('admin.hotcontacts.create',$data);
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
        
        session()->forget('hot_filter.search');
        session(['hot_filter.new' => 'created']);
        $entity = Entity::create($request->all());
        $xdata = $request->all();
        if (strlen($xdata['first_name'])==0) {
            $xdata['first_name'] = " ";
        }
        if (strlen($xdata['last_name'])==0) {
           $xdata['last_name'] = " ";
        }
        $xdata['source_date'] = isset($xdata['source_date']) ? date('Y-m-d H:i:s', strtotime($xdata['source_date'])) : date('Y-m-d H:i:s');
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
        
        if ($request->input('firm_name') == "") {
            $entity->firm_name = trim($contact->first_name . " " . $contact->last_name);
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
            'types' => $types
                
        ];
        return view('admin.hotcontacts.edit',$data);
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
        //return json_encode($entity->hot_id);
         
        $temp_name = $entity->full_name;
        $entity->update($request->all());

        if($request->has('update_all') ) {
            foreach ($entity->contacts as $contact) {
                foreach ($contact->links as $xcontact) {
                    $xcontact->entity->firm_name=$request['firm_name'];
                    $xcontact->entity->save();
                }
            }
            $entity_links = Entity::where('hot_id',$id)->get();
                foreach ($entity_links as $enti) {
                    $enti->firm_name=$request['firm_name'];
                    $enti->save();
                }
             
        } 

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
    public function destroy(Request $request,$id)
    {
        $entity = Entity::findOrFail($id);
        foreach ($entity->contacts as $xc) {
           $xc->delete();
        }
        $entity->delete();

        // redirect
        Session::flash('message', 'Successfully deleted the contact: ' . $entity->firm_name);
         if ($request->has('redirects_to')) {
              return redirect()->to($request->redirects_to);
         } else {
             return redirect()->route('hotcontacts.index');
         }
       
    }
}
