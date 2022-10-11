<?php

namespace App\Http\Controllers\Researcher;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use App\Job;
use App\JobPaymentHistory;

class JobPaymentsController extends Controller
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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$job_id)
    {
        $this->validate($request, [
            'payed_on' => 'required',
            'amount' => 'required|numeric',
        ]);
        
        
        
        $payment = new JobPaymentHistory;
        $payment->payed_on = date('Y-m-d', strtotime($request->payed_on));
        $payment->amount = $request->amount;
        $payment->description = $request->description;
        $payment->job_id = $job_id;
            
        $payment->save();
        Session::flash('message', 'New Payment added');
    
        return redirect()->to(route('jobs.edit',$job_id) .'?#payments');
        
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
    public function edit($job_id,$id)
    {
       $payment = JobPaymentHistory::findOrFail($id);
       return redirect()->to(route('jobs.edit',$job_id) .'?#payments')->with('payment', $payment);
       
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $job_id, $id)
    {
                
       
      $this->validate($request, [
            'payed_on' => 'required',
            'amount' => 'required|numeric',
        ]);
        $job =Job::findOrFail($job_id);

        $payment = JobPaymentHistory::findOrFail($id);
        $payment->payed_on = date('Y-m-d', strtotime($request->payed_on));
        $payment->amount = $request->amount;
        $payment->description = $request->input('description');
        
        $payment->save();
        Session::flash('message', 'Payment Updated');
       
        return redirect()->to(route('jobs.edit',$job_id) .'?#payments');
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($job_id,$id)
    {
        $payment = JobPaymentHistory::findOrFail($id);
        $payment->delete();
        
        return redirect()->to(route('jobs.edit',$job_id) .'?#payments');
     
    }
}
