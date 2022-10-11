<?php

namespace App\Http\Controllers\Admin;

use App\Attachment;
use App\AttachmentType;
use App\Http\Controllers\Controller;
use App\Job;
use App\WorkOrder;
use App\WorkOrderType;
use Auth;
use Illuminate\Http\Request;
use Imagick;
use Response;
use Session;
use Storage;
use App\Template;
use App\InvoiceLine;
use App\Client;

use App\WorkOrderFields;
use App\WorkOrderAnswers;


class WorkOrderFieldsController extends Controller
{
     private $wo_types;
     
     private $statuses = [ 
            'cancelled' => 'Cancelled',
            'cancelled charge' => 'Cancelled Charge',
            'cancelled duplicate' => 'Cancelled Duplicate',
            'cancelled duplicate needs credit' => 'Cancelled Duplicate Needs Credit',
            'cancelled no charge' => 'Cancelled No Charge',
            'closed' => 'Closed',
            'completed' => 'Completed',
            'data entry' => 'Data Entry',
            'edit' => 'Edit',
            'open' => 'Open',
            'payment pending' => 'Payment Pending',
            'pending' => 'Pending',
            'pending client' => 'Pending Client',
            'phone calls' => 'Phone Calls',
            'print' => 'Print',
            'qc' => 'Q/C',
            'search' => 'Search',
            'tax rolls' => 'Tax Rolls',
            'atids' => 'Title Search',
         ];
      private  $parties_type = [
            'client' => 'Client',
            'customer' => 'Customer',
            'general_contractor' => 'General Contractor',
            'bond' => 'Bond Info',
            'landowner' => 'Property Owner',
            'leaseholder' => 'Lease Holder',
            'lender' => 'Lender',
            'copy_recipient'=> "Copy Recipient",
            'sub_contractor' => "Sub Contractor",
            'sub_sub' => "Sub-Sub Contractor",
            
        ];
     
    public function __construct() {
        $this->wo_types = WorkOrderType::all()->pluck('name','slug')->toArray();
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
    
       $workfields =WorkOrderFields::query();
       
       if (session()->has('work_order_field.work_type')) {
           if(session('work_order_field.work_type') != 'all'){
               $workfields->where('workorder_type',session('work_order_field.work_type'));
           }
       }
     
       $workfields= $workfields->orderBy('workorder_type')->orderBy('field_order')->paginate(15);
       $field_type=[
            'textbox'=>'Text Box',
            'largetextbox'=>'Large Text Box',
            'date'=>'Date',
            'dropdown'=>'Dropdown',
        ];
        $required=[
            0=>'No',
            1=>'Yes',
             
        ];

  
       $data = [
           'workfields' => $workfields,
           'wo_types' => ['all' => 'All'] + $this->wo_types,
           'field_type' => $field_type,
           'required' => $required,

       ];
         
       return view('admin.workorderfields.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $field_type=[
            'textbox'=>'Text Box',
            'largetextbox'=>'Large Text Box',
            'date'=>'Date',
            'dropdown'=>'Dropdown',

        ];
        $data = [
            'wo_types' => $this->wo_types,
            'field_type' => $field_type,
        ];
        
        return view('admin.workorderfields.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
         $this->validate($request, [
            'field_order' => 'required'
        ]); 

         if (!$request->input('new_name') && $request->input('field_type')=='dropdown'){
            Session::flash('message', 'Dropdown list is required on dropdown field type. Please add dropdown lists.');
            return redirect()->route('workorderfields.create');
        };


        if ($request['required']) {$required=1;}else{$required=0;}
        $data=[
            'workorder_type'=>$request['workorder_type'],
            'required'=>$required,
            'field_order'=>$request['field_order'],
            'field_type'=>$request['field_type'],
            'field_label'=>$request['field_label'],
        ];
        $wo_fields =  WorkOrderFields::create( $data);
        if ($request->input('field_type')=='dropdown'){
            $drop_lists=array();
            foreach ($request->input('new_name') as $key => $dropdown_list)
            {
                $drop_lists[$request->new_name[$key]]=$request->new_value[$key];
                 
            }
            $wo_fields->dropdown_list=json_encode($drop_lists);
        }
        $wo_fields->created_at=\Carbon\Carbon::now();
        $wo_fields->save();


        Session::flash('message', 'New Work Order Field created');
        return redirect()->route('workorderfields.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
       
    }
    
    
     
    
     /**
     * Display the document.
     *
     * @param  int  $id
     * @return Response
     */
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $existsWorkfield=WorkOrderFields::where('id',$id)->first();
        $field_type=[
            'textbox'=>'Text Box',
            'largetextbox'=>'Large Text Box',
            'date'=>'Date',
            'dropdown'=>'Dropdown',

        ];
        $drop_lists=json_decode($existsWorkfield->dropdown_list);
        
        $answered=null;
        $exists_answer=WorkOrderAnswers::where('work_order_field_id',$id)->where('deleted_at',null)->get();

        foreach ($exists_answer as $answer) {
            $ex_WO=WorkOrder::where('id',$answer->work_order_id)->where('deleted_at',null)->get(); 

            if (count($ex_WO)>0){$answered='true';break;}
        }


        $data = [
            'wo_types' => $this->wo_types,
            'field_type' => $field_type,
            'existsWorkfield' => $existsWorkfield,
            'drop_lists' => $drop_lists,
            'answered' => $answered,
        ];
        
        return view('admin.workorderfields.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'field_order' => 'required'
        ]); 
        if (!$request->input('new_name') && $request->input('field_type')=='dropdown'){
            Session::flash('message', 'Dropdown list is required on dropdown field type. Please add dropdown lists.');
            return redirect()->route('workorderfields.edit',$id);
        };


        if ($request['required']) {$required=1;}else{$required=0;}
        $data=[
            'workorder_type'=>$request['workorder_type'],
            'required'=>$required,
            'field_order'=>$request['field_order'],
            'field_type'=>$request['field_type'],
            'field_label'=>$request['field_label'],
        ];

        $wo_fields =  WorkOrderFields::findOrFail( $id);
        $wo_fields->update($data);
        
        if ($request->input('field_type')=='dropdown'){
            $drop_lists=array();
            foreach ($request->input('new_name') as $key => $dropdown_list)
            {
                $drop_lists[$request->new_name[$key]]=$request->new_value[$key];
                 
            }
            $wo_fields->dropdown_list=json_encode($drop_lists);
            $wo_fields->save();
        }

        
        Session::flash('message', 'Work Order Field ' .$wo_fields->field_label . ' updated');
        return redirect()->route('workorderfields.edit',$wo_fields->id);
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $wo_field = WorkOrderFields::findOrFail($id);
        $wo_field->delete();
    
        Session::flash('message', 'Work Order Field ' .$wo_field->field_label . ' successfully deleted.');
        
        return redirect()->route('workorderfields.index');
        
    }
    
    
     
    public function setfilter (Request $request) {
     
         if ($request->has('work_type')) {
            if($request->work_type == "all" ) {
                session()->forget('work_order_field.work_type');
            } else {
                session(['work_order_field.work_type' => $request->work_type]);
            }
        }
       
        return redirect()->route('workorderfields.index');
    }
    
    
    public function resetfilter (Request $request) {
        return 'dfgdfgdf';
        session()->forget('work_order_field');
        return redirect()->route('workorderfields.index');
    }
    
}
