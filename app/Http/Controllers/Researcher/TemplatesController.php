<?php

namespace App\Http\Controllers\Researcher;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Template;
use App\TemplateLine;
use App\WorkOrderType;
use Session;

class TemplatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         
        $templates = Template::defaults()->paginate(15);
        $existent_types = Template::defaults()->pluck('type_slug')->toArray();
        $types = WorkOrderType::all()->pluck('name','slug')->toArray();
        foreach($existent_types as $key) {
            unset($types[$key]);
        }
      
        $data = [
            'templates' =>$templates,
            'types' =>$types
        ];
        return view('researcher.templates.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if($request->has('type')) {
            $type = $request->type;
        } else {
            $type ="";
        }
           
         $line_types = [
             'apply-always' => 'Apply Always',
             'aply-when-rush' =>'Apply when Rush',
             'standard-mail' => 'Apply when Regular Mail',
             'certified-green' => 'Apply when Certfied Green RR',   
             'certified-nongreen' => 'Apply when Certfied Non Green', 
             'registered-mail' => 'Apply when Registered Mail',
             'express-mail' => 'Apply when Express Mail',
             'other-mail' => 'Apply when eMail',
             'return-mail' => 'Apply when Return Recipient',
             ];
         $existent_types = Template::defaults()->pluck('type_slug')->toArray();
        $types = WorkOrderType::all()->pluck('name','slug')->toArray();
        foreach($existent_types as $key) {
            unset($types[$key]);
        }
      
         $data = [
             'types' => $types,
             'type' => $type,
             'line_types' => $line_types
         ];
         
         return view('researcher.templates.create',$data);
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
            'type' => 'required',
            'line_type' => 'required',
            'description' => 'required',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
        ]); 
         
        $template = new Template();
        $template->type_slug = $request->type;
        $template->enabled = 1;
        $template->save();
        
        
        $line = new TemplateLine();
        $line->type = $request->line_type;
        $line->description = $request->description;
        $line->quantity = $request->quantity;
        $line->price = $request->price;
       
        $template->lines()->save($line);
                
         Session::flash('message', 'New Template created');
        return redirect()->route('templates.edit',$template->id);
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
        $template = Template::findOrFail($id);
        $line_types = [
            'apply-always' => 'Apply Always',
             'aply-when-rush' =>'Apply when Rush',
             'standard-mail' => 'Apply when Regular Mail',
             'certified-green' => 'Apply when Certfied Green RR',   
             'certified-nongreen' => 'Apply when Certfied Non Green', 
             'registered-mail' => 'Apply when Registered Mail',
             'express-mail' => 'Apply when Express Mail',
             'other-mail' => 'Apply when eMail',
             'return-mail' => 'Apply when Return Recipient',
            ];
        //only types not assigned + current type
        $existent_types = Template::defaults()->pluck('type_slug')->toArray();
        $types = WorkOrderType::all()->pluck('name','slug')->toArray();
        foreach($existent_types as $key) {
            unset($types[$key]);
        }
        $types[$template->type_slug] =  $template->type->name;
        $data = [
             'types' => $types,
             'line_types' => $line_types,
             'template' => $template
        ];
        
        return view ('researcher.templates.edit',$data);
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
            'new_description.*' => 'required',
            'new_quantity.*' => 'required|numeric',
            'new_price.*' => 'required|numeric'
        ],[
            'new_description.*' => 'The Description is required',
            'new_quantity.*' => 'The quantity must be numeric',
            'new_price.*' => 'The price must be numeric'
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
            $template->lines()->save($line);
        }
        }
        return redirect()->route('templates.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $template = Template::findOrFail($id);
        $old_name = $template->type->name;
        $template->delete();
        
        Session::flash('message', 'Template deleted');
        return redirect()->route('templates.index');
    }
}
