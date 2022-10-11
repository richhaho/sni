<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Imagick;
use Response;
use Session;
use Storage;

use App\Reminders;

use App\Client;
use Mail;
use App\Mail\SendReminder;

class RemindersController extends Controller
{
     
    public function __construct() {
     
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
       $reminder =Reminders::query();
       $reminder= $reminder->orderBy('reminder_name')->paginate(10);
       $period=[
                    'Daily'=>'Day(s)',
                    'Weekly'=>'Week(s)',
                    'Monthly'=>'Month(s)'    
                ];
       $data = [
           'reminder' => $reminder,
           'period' => $period,

       ];
        
       return view('admin.reminders.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $period=[
                    'Daily'=>'Day(s)',
                    'Weekly'=>'Week(s)',
                    'Monthly'=>'Month(s)'    
                ];
        $data=[
            'period'=>$period
        ];
        return view('admin.reminders.create',$data);
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
            'reminder_name' => 'required'
        ]); 
        $now=date('Y-m-d H:i:s',strtotime(\Carbon\Carbon::now()));
        $first_send_date=date('Y-m-d H:i:s',strtotime($request['first_send_date']));

        if($now>=$first_send_date){
            Session::flash('message', 'First Send Date/Time must be future than current time. Please pick another Date/Time.');
            return redirect()->route('reminders.create');
        }

        if ($request['status']) {$status=1;}else{$status=0;}
        $data=[
            'reminder_name'=>$request['reminder_name'],
            'email_subject'=>$request['email_subject'],
            'email_message'=>$request['email_message'],
            'sms_message'=>$request['sms_message'],
            'status'=>$status,
            'first_send_date'=>date('Y-m-d H:i:s',strtotime($request['first_send_date'])),
            'period'=>$request['period'],
            'send_frequency'=>$request['send_frequency'],
        ];
        $reminder =  Reminders::create( $data);
        $reminder->period=$request['period'];
        $reminder->end_send_date=null;
        $reminder->next_send_date=$reminder->first_send_date;
        $reminder->save();

        Session::flash('message', 'New Reminder created.');
        return redirect()->route('reminders.index');
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
        $reminder=Reminders::where('id',$id)->first();
        if (count($reminder)==0){
            Session::flash('message', 'This Reminder has been deleted already.');
            return redirect()->route('reminders.index');
        }
        $period=[
                    'Daily'=>'Day(s)',
                    'Weekly'=>'Week(s)',
                    'Monthly'=>'Month(s)'    
                ];
        $data = [
            'reminder' => $reminder,
            'period' => $period,
        ];
        
        return view('admin.reminders.edit',$data);
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
            'reminder_name' => 'required'
        ]); 
        $now=date('Y-m-d H:i:s',strtotime(\Carbon\Carbon::now()));

        $first_time=strtotime($request['first_send_date']);
        $first_send_date=date('Y-m-d H:i:s',$first_time);
        if($now>=$first_send_date){
            Session::flash('message', 'First Send Date/Time must be future than current time. Please pick another Date/Time.');
            return redirect()->route('reminders.edit',$id);
        }


        $reminder=Reminders::where('id',$id)->first();
        if ($request['status']) {$status=1;}else{$status=0;}
        $data=[
            'reminder_name'=>$request['reminder_name'],
            'email_subject'=>$request['email_subject'],
            'email_message'=>$request['email_message'],
            'sms_message'=>$request['sms_message'],
            'status'=>$status,
            'first_send_date'=>$first_send_date,
            'send_frequency'=>$request['send_frequency'],
        ];
        $reminder->update($data);
        $reminder->period=$request['period'];

        $reminder->end_send_date=null;
        $reminder->next_send_date=$reminder->first_send_date;
        $reminder->save();

        // $client=Client::findOrFail('100024');
        // Mail::to('richhaho@gmail.com')->send(new SendReminder($client, "This is a test email."));

        Session::flash('message', 'New Reminder Updated.');
        return redirect()->route('reminders.index');
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $reminder = Reminders::findOrFail($id);
        $reminder->delete();
    
        Session::flash('message', 'Reminder (' .$reminder->reminder_name . ') successfully deleted.');
        
        return redirect()->route('reminders.index');
        
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
