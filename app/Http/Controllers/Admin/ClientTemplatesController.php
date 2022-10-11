<?php

namespace App\Http\Controllers\Admin;

use App\Client;
use App\Http\Controllers\Controller;
use App\Template;
use App\TemplateLine;
use App\WorkOrderType;
use Illuminate\Http\Request;
use Session;

class ClientTemplatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($client_id)
    {
        $client = Client::findOrFail($client_id);
        $templates = Template::where("client_id",$client_id)->paginate(15);
        $existent_types = Template::where("client_id",$client_id)->pluck('type_slug')->toArray();
        $types = WorkOrderType::all()->pluck('name','slug')->toArray();
        foreach($existent_types as $key) {
            unset($types[$key]);
        }
        $data = [
            'client' => $client,
            'templates' =>$templates,
            'types' =>$types
        ];
        return view('admin.clients.templates.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request,$client_id)
    {
        if($request->has('type')) {
            $type = $request->type;
        } else {
            $type ="";
        }
        $client = Client::findOrFail($client_id);
        $line_types = [
             'apply-always' => 'Apply Always',
             'apply-always-ss' => 'Apply Always (SS)',
             'aply-when-rush' =>'Apply when Rush',
             'apply-during-docgen' => 'Apply During DocGen',
             'apply-during-docgen-rush' => 'Apply During DocGen RUSH',
             'standard-mail' => 'Apply when Regular Mail',
             'certified-green' => 'Apply when Certfied Green RR',   
             'certified-nongreen' => 'Apply when Certfied Non Green', 
             'registered-mail' => 'Apply when Registered Mail',
             'express-mail' => 'Apply when Express Mail',
             'other-mail' => 'Apply when eMail',
             'standard-mail-ss' => 'Apply when Regular Mail (SS)',
             'certified-green-ss' => 'Apply when Certfied Green RR (SS)',   
             'certified-nongreen-ss' => 'Apply when Certfied Non Green (SS)', 
             'registered-mail-ss' => 'Apply when Registered Mail (SS)',
             'express-mail-ss' => 'Apply when Express Mail (SS)',
             'other-mail-ss' => 'Apply when eMail (SS)',
             'return-mail' => 'Apply when Return Recipient',
             'additional-service' => 'Additional Service'
             ];
         $existent_types = Template::where("client_id",$client_id)->pluck('type_slug')->toArray();
        $types = WorkOrderType::all()->pluck('name','slug')->toArray();
        foreach($existent_types as $key) {
            unset($types[$key]);
        }
      
         $data = [
             'client' => $client,
             'types' => $types,
             'type' => $type,
             'line_types' => $line_types
         ];
         
         return view('admin.clients.templates.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$client_id)
    {
     //echo json_encode($request['allow_default_template']);return;
    if (!isset($request['allow_default_template'])){
        $this->validate($request, [
            'type' => 'required',
            'line_type' => 'required',
            'description' => 'required',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'todo_name'=>'required_if:line_type,additional-service',
            'summary'=>'required_if:line_type,additional-service',
        ]); 
        
        
        $template = new Template();
        $template->type_slug = $request->type;
        $template->enabled = 1;
        $template->client_id = $client_id;
        $template->save();
        
        
        $line = new TemplateLine();
        $line->type = $request->line_type;
        $line->description = $request->description;
        $line->quantity = $request->quantity;
        $line->price = $request->price;
        if ($request->line_type == 'additional-service') {
            $line->todo_name = $request->todo_name;
            $line->summary = $request->summary;
            $line->todo_instructions = $request->has('todo_instructions') ? 1 : 0;
            $line->todo_uploads = $request->has('todo_uploads') ? 1 : 0;
        }       
        $template->lines()->save($line);
                
         Session::flash('message', 'New Template created');
        return redirect()->route('client.templates.edit',[$client_id,$template->id]);
    }else{
        $default_template =Template::where([['client_id',0],['type_slug',$request->type]])->first();
        if (!$default_template){
            
            return 'Default template does not exist on db. Please add default of this type first.';
        }

        $default_template_lines=$default_template->lines;

        $template = new Template();
        $template->type_slug = $request->type;
        $template->enabled = 1;
        $template->client_id = $client_id;
        $template->save();

        
        foreach ($default_template_lines as $each_line) {
            $line = new TemplateLine();
            $line->type = $each_line->type;
            $line->description = $each_line->description;
            $line->quantity = $each_line->quantity;
            $line->price = $each_line->price;
            if ($each_line->type == 'additional-service') {
                $line->todo_name = $each_line->todo_name;
                $line->summary = $each_line->summary;
                $line->todo_instructions = $each_line->todo_instructions ? 1 : 0;
                $line->todo_uploads = $each_line->todo_uploads ? 1 : 0;
            }
            $template->lines()->save($line);
        }

        Session::flash('message', 'New Template created from Default.');
        return redirect()->route('client.templates.edit',[$client_id,$template->id]);

    }

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
    public function edit($client_id,$id)
    {
        $client = Client::findOrFail($client_id);
        $template = Template::findOrFail($id);
        $line_types = [
            'apply-always' => 'Apply Always',
            'apply-always-ss' => 'Apply Always (SS)',
            'aply-when-rush' =>'Apply when Rush',
            'apply-during-docgen' => 'Apply During DocGen',
            'apply-during-docgen-rush' => 'Apply During DocGen RUSH',
            'standard-mail' => 'Apply when Regular Mail',
            'certified-green' => 'Apply when Certfied Green RR',   
            'certified-nongreen' => 'Apply when Certfied Non Green', 
            'registered-mail' => 'Apply when Registered Mail',
            'express-mail' => 'Apply when Express Mail',
            'other-mail' => 'Apply when eMail',
            'standard-mail-ss' => 'Apply when Regular Mail (SS)',
            'certified-green-ss' => 'Apply when Certfied Green RR (SS)',   
            'certified-nongreen-ss' => 'Apply when Certfied Non Green (SS)', 
            'registered-mail-ss' => 'Apply when Registered Mail (SS)',
            'express-mail-ss' => 'Apply when Express Mail (SS)',
            'other-mail-ss' => 'Apply when eMail (SS)',
            'return-mail' => 'Apply when Return Recipient',
            'additional-service' => 'Additional Service'
            ];
        $existent_types = Template::where("client_id",$client_id)->pluck('type_slug')->toArray();
        $types = WorkOrderType::all()->pluck('name','slug')->toArray();
        foreach($existent_types as $key) {
            unset($types[$key]);
        }
        $types[$template->type_slug] =  $template->type->name;
        $data = [
             'client' => $client,
             'types' => $types,
             'line_types' => $line_types,
             'template' => $template
        ];
        
        return view ('admin.clients.templates.edit',$data);
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
            'new_description.*' => 'required',
            'new_quantity.*' => 'required|numeric',
            'new_price.*' => 'required|numeric',
            'new_todo_name.*'=>'required_if:line_type,additional-service',
            'new_summary.*'=>'required_if:line_type,additional-service'
        ],[
            'new_description.*' => 'The Description is required',
            'new_quantity.*' => 'The quantity must be numeric',
            'new_price.*' => 'The price must be numeric',
            'new_todo_name.*'=>'To Do Name is required if line type is Additional Service.',
            'new_summary.*'=>'Summary is required if line type is Additional Service.'
        ]);
        
        //dd('valido');
        $template = Template::findOrFail($id);
        $template->type_slug = $request->type;
        $template->save();   
        
        if ($request->input('line_type')) {
            foreach ($request->input('line_type') as $key => $linetype) {
                $line =  TemplateLine::findOrFail($key);
                $line->type = $request->line_type[$key];
                $line->description = $request->description[$key];
                $line->quantity = $request->quantity[$key];
                $line->price = $request->price[$key];
                if ($request->line_type[$key] == 'additional-service') {
                    $line->todo_name = $request->todo_name[$key];
                    $line->summary = $request->summary[$key];
                    $line->todo_instructions = isset($request->todo_instructions[$key]) ? 1:0;
                    $line->todo_uploads = isset($request->todo_uploads[$key]) ? 1:0;
                }
                $line->save();
            }
        }
        
        if ($request->input('new_line_type')) {
            foreach ($request->input('new_line_type') as $key => $linetype) {
                $line = new TemplateLine();
                $line->type = $request->new_line_type[$key];
                $line->description = $request->new_description[$key];
                $line->quantity = $request->new_quantity[$key];
                $line->price = $request->new_price[$key];
                if ($request->new_line_type[$key] == 'additional-service') {
                    $line->todo_name = $request->new_todo_name[$key];
                    $line->summary = $request->new_summary[$key];
                    $line->todo_instructions = isset($request->new_todo_instructions[$key]) ? 1:0;
                    $line->todo_uploads = isset($request->new_todo_uploads[$key]) ? 1 : 0;
                }
                $template->lines()->save($line);
            }
        }
        return redirect()->route('client.templates.index',$template->client_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($client_id,$id)
    {
        $template = Template::findOrFail($id);
        $old_name = $template->type->name;
        $template->delete();
        
        Session::flash('message', 'Template deleted');
        return redirect()->route('client.templates.index',$template->client_id);
    }
}
