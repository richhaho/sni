@extends('client.layouts.app')

@section('navigation')
    @include('client.navigation')
@endsection
@section('css')
<style>
    .override_one_weekly,.override_one_payment,.override_one_notice,.override_one_note,.override_one_attachments,.override_one_smsReminder,.override_one_emailReminder,.one_override_lastday_over{
        background-color:ghostwhite;
        border-radius: 3px;
        padding: 5px; 
        margin: 5px;
        border: 1px solid gainsboro;
        float:left !important;
    }
</style>
@endsection
@section('content')
    {!! Form::open(['route' => ['client.update', $client->id], 'method'=> 'PUT','autocomplete' => 'off']) !!}
        {!! Form::hidden('status',4)!!}
     
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">Company Information
                    <div class="pull-right">
                        <button class="btn btn-success " type="submit"> <i class="fa fa-floppy-o"></i> Save</button>
                        
                    </div>
                </h1>       
            </div>
            </div>
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h5 class="page-header">
                    Everything below will pre-fill into one or all of the forms at one time or another so try to complete what is being asked. If you don't have the ability to add your signature it will pre-fill with a signed version of your name.
                </h5>       
            </div>
            </div>
        </div>
            <div id="page-wrapper">
            
            <div class="container-fluid">
                
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (Session::has('message'))
                   <div class="col-xs-12 message-box">
                   <div class="alert alert-info">{{ Session::get('message') }}</div>
                   </div>
               @endif
                <div class="row">
                  
                    
                    <div class="col-xs-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Contact Info
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <label>Company Name:</label>
                                    <input type="hidden" name="company_name" id="company_name"  value="{{ old("company_name",$client->company_name)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                    <input type="text" value="{{ old("company_name",$client->company_name)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="" readonly>
                                </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label>Title:</label>
                                        <input name="title" id="title" value="{{ old("title",$client->title)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">

                                    </div>
                                    <div class="col-md-6 form-group">
                                    <label>Gender:</label>
                                    {!!  Form::select('gender',$gender,old("gender",$client->gender), ['class' => 'form-control','data-toggle'=>'tooltip', 'data-placement'=>'top', 'title'=>'This question prefills some forms ie Claim of Lien']) !!}
                                    </div>

                               
                                </div>  
                               
                         
                                <div class="row">
                                     <div class="col-md-6 form-group">
                                    <label>First Name:</label>
                                    <input name="first_name"  value="{{ old("first_name",$client->first_name)}}" class="noucase form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Last Name:</label>
                                    <input name="last_name" value="{{ old("last_name",$client->last_name)}}" class="noucase form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <label>Email:</label>
                                    <input name="old_email" value="{{ $client->email}}" type="hidden">
                                    <input name="email" value="{{ old("email",$client->email)}}" class="form-control noucase" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                </div>
                                
                                
                                <div class="row">
                                <div class="col-md-12 col-lg-3 form-group">
                                    <label>Phone:</label>
                                    <input name="phone" value="{{ old("phone",$client->phone)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                              
                                <div class="col-md-12 col-lg-3 form-group">
                                    <label>Mobile:</label>
                                    <input name="mobile" value="{{ old("mobile",$client->mobile)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                <div class="col-md-12 col-lg-3 form-group">
                                    <label>Fax:</label>
                                    <input name="fax" value="{{ old("fax",$client->fax)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                 <div class="col-md-12 col-lg-3 form-group">
                                    <label>Interest Rate:</label>
                                    {!!  Form::number('interest_rate',old("interest_rate",$client->interest_rate), ['class' => 'form-control', 'min'=>'0', 'max'=>'100', 'step'=>'0.01','data-toggle'=>'tooltip', 'data-placement'=>'top', 'title'=>'If your contract has interest included for late fees, you would put the interest rate here.']) !!}
                                </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Email Settings
                            </div>
                            <div class="panel-body">
                                
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label>Work Order Completion Nofications:</label> (Choose Immediate to receive upon completion and Off to receive no notication.)
                                    </div>
                                    <div class="col-md-6 form-group">
                                        
                                        {!!  Form::select('notification_setting',$notification_setting,old('notification_setting',$client->notification_setting), ['class' => 'form-control','data-toggle'=>'tooltip', 'data-placement'=>'top', 'title'=>'']) !!}
                                    </div>

                               
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label>Notification Email Overrides:</label>
                                    </div>
                                </div>
                                
                                <!-- //////////////////////-->
                                <div class="row WeeklyOverrides">
                                    <div class="col-md-6 form-group">
                                        <label>- Weekly Invoice:</label>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <input id="new_override_weekly" type="text" placeholder="example@mail.com" class="noucase" data-toggle="tooltip" data-placement="top" title="Add one or more override emails." >
                                        <button type="button" class="btn btn-warning add_override_weekly" >Add</button>
                                        <p class="validation_error_weekly" style="color: red;display: none">Valid email type required.</p>
                                    </div>
                                    <div class="col-md-12 form-group override_emails_weekly"><?php $nm=0; ?> 
                                    @if(json_encode($override_weekly)!="false" && json_encode($override_weekly)!="null")
                                    @foreach ($override_weekly as $email)
                                    <?php $nm++; ?> 
                                        <div class="override_one_weekly {{$nm}}" id="{{$nm}}">{{$email}}<input type="hidden" value="{{$email}}" name="override_weekly[{{$nm}}]" />
                                            &nbsp;&nbsp;<a class="close_one_override_weekly" onclick="closeoverride_weekly({{$nm}})"><i class="fa fa-close"></i></a>
                                        </div>
                                        @endforeach 
                                        @endif
                                    </div>
                                </div> 
                                <!-- //////////////////////-->
                                <div class="row PaymentOverrides">
                                    <div class="col-md-6 form-group">
                                        <label>- Payment Receipts:</label>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <input id="new_override_payment" type="text" placeholder="example@mail.com" class="noucase" data-toggle="tooltip" data-placement="top" title="Add one or more override emails." >
                                        <button type="button" class="btn btn-warning add_override_payment" >Add</button>
                                        <p class="validation_error_payment" style="color: red;display: none">Valid email type required.</p>
                                    </div>
                                    <div class="col-md-12 form-group override_emails_payment"><?php $nm=0; ?> 
                                    @if(json_encode($override_payment)!="false" && json_encode($override_payment)!="null")
                                    @foreach ($override_payment as $email)
                                    <?php $nm++; ?> 
                                        <div class="override_one_payment {{$nm}}" id="{{$nm}}">{{$email}}<input type="hidden" value="{{$email}}" name="override_payment[{{$nm}}]" />
                                            &nbsp;&nbsp;<a class="close_one_override_payment" onclick="closeoverride_payment({{$nm}})"><i class="fa fa-close"></i></a>
                                        </div>
                                        @endforeach 
                                        @endif
                                    </div>
                                </div>
                                <!-- //////////////////////--> 
                                <!-- //////////////////////-->
                                <div class="row NoticeOverrides">
                                    <div class="col-md-6 form-group">
                                        <label>- Notice Completion:</label>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <input id="new_override_notice" type="text" placeholder="example@mail.com" class="noucase" data-toggle="tooltip" data-placement="top" title="Add one or more override emails." >
                                        <button type="button" class="btn btn-warning add_override_notice" >Add</button>
                                        <p class="validation_error_notice" style="color: red;display: none">Valid email type required.</p>
                                    </div>
                                    <div class="col-md-12 form-group override_emails_notice"><?php $nm=0; ?> 
                                    @if(json_encode($override_notice)!="false" && json_encode($override_notice)!="null")
                                    @foreach ($override_notice as $email)
                                    <?php $nm++; ?> 
                                        <div class="override_one_notice {{$nm}}" id="{{$nm}}">{{$email}}<input type="hidden" value="{{$email}}" name="override_notice[{{$nm}}]" />
                                            &nbsp;&nbsp;<a class="close_one_override_notice" onclick="closeoverride_notice({{$nm}})"><i class="fa fa-close"></i></a>
                                        </div>
                                        @endforeach 
                                        @endif
                                    </div>
                                </div> 
                                <!-- //////////////////////-->

                                <div class="row OverrideLastdayOver">
                                    <div class="col-md-12 form-group">
                                        <label>- Job last day is over 30, 45, 60, 75 days:</label>
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <input id="new_override_lastday_over" type="text" placeholder="example@mail.com" class="noucase" data-toggle="tooltip" data-placement="top" title="Add one or more override emails." >
                                        <button type="button" class="btn btn-warning add_override_lastday_over" >Add</button>
                                        <p class="validation_error_override_lastday_over" style="color: red;display: none">Valid email type required.</p>
                                    </div>
                                    <div class="col-md-12 form-group override_lastday_over"><?php $nm=0; ?> 
                                    @if(json_encode($override_lastday_over)!="false" && json_encode($override_lastday_over)!="null")
                                    @foreach ($override_lastday_over as $email)
                                    <?php $nm++; ?> 
                                        <div class="one_override_lastday_over {{$nm}}" id="{{$nm}}">{{$email}}<input type="hidden" value="{{$email}}" name="override_lastday_over[{{$nm}}]" />
                                            &nbsp;&nbsp;<a class="close_one_override_lastday_over" onclick="closeoverride_lastday_over({{$nm}})"><i class="fa fa-close"></i></a>
                                        </div>
                                        @endforeach 
                                        @endif
                                    </div>
                                </div> 

                                <div class="row">
                                    <div class="col-md-12 form-group">
                                        <label>- Turn on/off job reminders</label>
                                    </div>
                                    <div class="col-xs-12 form-group">
                                        <div class="checkbox checkbox-slider--b-flat">
                                            <label>
                                                <input name="turn_job_reminder"  type="checkbox" class="turn_job_reminder selection" {{ ($client->turn_job_reminder ==1) ? 'checked' :''}}><span>Turn Off/On</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                 
                               
                            </div>
                                
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Reminder's Email/SMS Settings
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label>Allow Email Reminders:</label>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <div class="checkbox checkbox-slider--b-flat">
                                            <label>
                                                <input name="allow_emailReminder"  type="checkbox" class="allow_emailReminder selection" {{ ($client->allow_email_reminder ==1) ? 'checked' :''}}><span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- //////////////////////-->
                                <div class="row emailReminder">
                                    <div class="col-md-6 form-group">
                                        <label>- Reminder Email:</label>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <input id="new_override_emailReminder" type="text" placeholder="example@mail.com" class="noucase" data-toggle="tooltip" data-placement="top" title="Add one or more override emails." >
                                        <button type="button" class="btn btn-warning add_override_emailReminder" >Add</button>
                                        <p class="validation_error_emailReminder" style="color: red;display: none">Valid email type required.</p>
                                    </div>
                                    <div class="col-md-12 form-group override_emails_emailReminder"><?php $nm=0; ?> 
                                    @if(json_encode($override_emailReminder)!="false" && json_encode($override_emailReminder)!="null")
                                    @foreach ($override_emailReminder as $email)
                                    <?php $nm++; ?> 
                                        <div class="override_one_emailReminder {{$nm}}" id="{{$nm}}">{{$email}}<input type="hidden" value="{{$email}}" name="override_emailReminder[{{$nm}}]" />
                                            &nbsp;&nbsp;<a class="close_one_override_emailReminder" onclick="closeoverride_emailReminder({{$nm}})"><i class="fa fa-close"></i></a>
                                        </div>
                                        @endforeach 
                                        @endif
                                    </div>
                                </div> 
                                <!-- //////////////////////-->
                                <br><br>
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label>Allow SMS/Text Reminders:</label>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <div class="checkbox checkbox-slider--b-flat">
                                            <label>
                                                <input name="allow_smsReminder"  type="checkbox" class="allow_smsReminder selection" {{ ($client->allow_sms_reminder ==1) ? 'checked' :''}}><span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row smsReminder">
                                    <div class="col-md-6 form-group">
                                        <label>- Reminder SMS Phone Number(s):</label>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <input id="new_override_smsReminder" type="text" placeholder="+1XXXXXXXXX" class="noucase" data-toggle="tooltip" data-placement="top" title="Add one or more override Phone Numbers." >
                                        <button type="button" class="btn btn-warning add_override_smsReminder" >Add</button>
                                        <p class="validation_error_smsReminder" style="color: red;display: none">Valid Phone Number type required.</p>
                                    </div>
                                    <div class="col-md-12 form-group override_emails_smsReminder"><?php $nm=0; ?> 
                                    @if(json_encode($override_smsReminder)!="false" && json_encode($override_smsReminder)!="null")
                                    @foreach ($override_smsReminder as $phone)
                                    <?php $nm++; ?> 
                                        <div class="override_one_smsReminder {{$nm}}" id="{{$nm}}">{{$phone}}<input type="hidden" value="{{$phone}}" name="override_smsReminder[{{$nm}}]" />
                                            &nbsp;&nbsp;<a class="close_one_override_smsReminder" onclick="closeoverride_smsReminder({{$nm}})"><i class="fa fa-close"></i></a>
                                        </div>
                                        @endforeach 
                                        @endif
                                    </div>
                                </div>
                                <!-- //////////////////////--> 
                               
                            </div>
                                
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Allow to close jobs automatically
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-6 form-group">
                                        <div class="checkbox checkbox-slider--b-flat">
                                            <label>
                                                <input name="allow_jobclose"  type="checkbox" class="allow_jobclose selection" {{ ($client->allow_jobclose ==1) ? 'checked' :''}}><span>Allow Job Close Automatically</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xs-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Address
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <label>Street Address:</label>
                                    <input name="address_1" value="{{ old("address_1",$client->address_1)}}" placeholder="Street and number" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <input name="address_2" value="{{ old("address_2",$client->address_2)}}" placeholder="Apartment, suite, unit, building, floor, etc." class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-md-12 col-lg-4 form-group">
                                    <label>Country:</label>
                                    <input id="countries" value="{{ old("country",strlen($client->country) == 0 ? 'USA': $client->country )}}" name="country" class="form-control typeahead" data-toggle="tooltip" data-placement="top" title="" autocomplete="off">
                                </div>
                               <div class="col-md-6 col-lg-4 form-group">
                                    <label>City:</label>
                                    <input name="city"  value="{{ old("city",$client->city)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label>State / Province / Region:</label>
                                    <input id="states" value="{{ old("state",$client->state)}}" name="state" class="form-control typeahead" data-toggle="tooltip" data-placement="top" title=""  autocomplete="off">
                                </div>
                                   
                                </div>
                                
                                <div class="row">
                              
                             
                                <div class="col-md-12 col-lg-6 form-group">
                                    <label>Zip code:</label>
                                    <input name="zip"  value="{{ old("zip",$client->zip)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                      <div class="col-md-12 col-lg-6 form-group">
                                    <label>County:</label>
                                    <input id="counties" value="{{ old("county",$client->county)}}" name="county" class="form-control typeahead" data-toggle="tooltip" data-placement="top" title="What county is your company based in? If out of state put your county and state here" autocomplete="off">
                                </div>
                                </div>
                                
                            </div>
                        </div>
                        @if(1!==1)
                        <div class="panel panel-default hidden">
                            <div class="panel-heading">
                                Subscription Rate
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12 col-md-6 form-group">
                                        <label>Service Type:</label>
                                        {!!  Form::select('service', ['' => '', 'full' => 'Full-Service', 'self' => 'Self-Service'], $client->service, ['class' => 'form-control', 'required' => 'true']) !!}
                                    </div>
                                    <div class="col-xs-12 col-md-6 form-group">
                                        <label>Subscription:</label>
                                        {!!  Form::select('subscription', ['' => '', '30' => '30-day rate', '365' => '365-day rate'], $client->subscription, ['class' => 'form-control', 'required' => 'true']) !!}
                                    </div>
                                    <div class="col-xs-12 col-md-6 form-group">
                                        <label>Self-Service 30-day rate:</label>
                                        <input readonly type="number" name='self_30day_rate' class="form-control" value="{{$client->self_30day_rate}}" step="0.01" min="0">
                                    </div>
                                    <div class="col-xs-12 col-md-6 form-group">
                                        <label>Self-Service 365-day rate:</label>
                                        <input readonly type="number" name='self_365day_rate' class="form-control" value="{{$client->self_365day_rate}}" step="0.01" min="0">
                                    </div>
                                    <div class="col-xs-12 col-md-6 form-group">
                                        <label>Full-Service 30-day rate:</label>
                                        <input readonly type="number" name='full_30day_rate' class="form-control" value="{{$client->full_30day_rate}}" step="0.01" min="0">
                                    </div>
                                    <div class="col-xs-12 col-md-6 form-group">
                                        <label>Full-Service 365-day rate:</label>
                                        <input readonly type="number" name='full_365day_rate' class="form-control" value="{{$client->full_365day_rate}}" step="0.01" min="0">
                                    </div>
                                    @if($client->service && $client->subscriptionRate && $client->expiration && $client->expiration >= date('Y-m-d H:i:s'))
                                    <div class="col-xs-12 col-md-12 form-group">
                                        <a href="{{route('client.subscription.cancel', $client->id)}}" class="btn btn-warning pull-right" data-toggle="tooltip" data-placement="top" title="Canceling your subscription will prevent the auto-renewal at the end of your subscription period.">Cancel subscription</a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                     <div class="col-xs-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Default Materials
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    {{ Form::textarea('default_materials', old("address_1",$client->default_materials),['class'=>'form-control','data-toggle'=>'tooltip', 'data-placement'=>'top', 'title'=>'Default Materials and/or Services. This is a description of what you normally would provide on your jobs. (ie Electrical Contractor would supply Electrical Materials and Services)'])}}
                                    
                                </div>
                                </div>
                              
                            </div>
                             
                        </div>
                        <!-- <div class="panel panel-default">
                            <div class="panel-heading">
                                Auto Batch for Non-Batched Unpaid Invoices  
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-6 form-group">
                                        <div class="checkbox checkbox-slider--b-flat">
                                            <label>
                                                <input name="autobatch"  type="checkbox" class="autobatch selection" {{ ($client->autobatch ==1) ? 'checked' :''}}><span>Allow AutoBatch</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 form-group">
                                        <p>(AutoBatch would have your non-batched unpaid invoices batched every Friday at 5pm.)</p>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Signature
                            </div>
                            <div class="panel-body">
                                <div class="row">

                                    <div class="col-md-12 form-group">
                                        {{ Form::hidden('signature',$client->signature)}}
                                        <label>Signature Panel:</label> <a class="btn btn-danger btn-xs" id="clear-canvas"><i class="fa fa-eraser"></i></a>
                                        <div id="signature-panel" class="signature-panel" data-name="signature" data-height="120" data-width="400"></div>
                                    </div>
                                    <div class="col-md-12 form-group">

                                        <label>Current Signature:</label>
                                        <div>
                                            @if(strlen($client->signature)>0)
                                            <img id="currentsignature" src="{{$client->signature}}"> @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xs-12">
                        
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
      
               
            </div>
            <!-- /.container-fluid -->
            
        </div>
    {!! Form::close() !!}
@endsection

@section('scripts')
 
    

<script src="{{ asset('vendor/jqsignature/js/jq-signature.min.js') }}" type="text/javascript"></script>
<script>
var company_name=$('#company_name').val();
var error_count="{{count($errors)}}";
 
if (company_name.trim()=="" || error_count!='0'){
    
    $('.sidebar').css('pointer-events','none');
}



////////////////////////////////////////////////////////////// 
function closeoverride_weekly(id){
    $('.override_emails_weekly .'+id).remove();
}
$('#new_override_weekly').keydown(function(){
    $('.validation_error_weekly').css('display','none');
});
$('.add_override_weekly').click(function(){
    var email=$('#new_override_weekly').val();
    if (!echeck(email)) {$('.validation_error_weekly').css('display','block');return};
    var nm=parseInt($('.override_emails_weekly div').last().attr('id'));
    if ($('.override_emails_weekly div').last().attr('id')==null) nm=0; 
    nm=nm+1;
    var new_element='<div class="override_one_weekly '+nm+'" id="'+nm+'">'+email+'<input type="hidden" value="'+email+'" name="override_weekly['+nm+']" />                                            &nbsp;&nbsp;<a class="close_one_override" onclick="closeoverride_weekly('+nm+')"><i class="fa fa-close"></i></a>  </div>'
    $('.override_emails_weekly').append(new_element);
    $('#new_override_weekly').val("");
});
///////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////// 
function closeoverride_payment(id){
    $('.override_emails_payment .'+id).remove();
}
$('#new_override_payment').keydown(function(){
    $('.validation_error_payment').css('display','none');
});
$('.add_override_payment').click(function(){
    var email=$('#new_override_payment').val();
    if (!echeck(email)) {$('.validation_error_payment').css('display','block');return};
    var nm=parseInt($('.override_emails_payment div').last().attr('id'));
    if ($('.override_emails_payment div').last().attr('id')==null) nm=0; 
    nm=nm+1;
    var new_element='<div class="override_one_payment '+nm+'" id="'+nm+'">'+email+'<input type="hidden" value="'+email+'" name="override_payment['+nm+']" />                                            &nbsp;&nbsp;<a class="close_one_override" onclick="closeoverride_payment('+nm+')"><i class="fa fa-close"></i></a>  </div>'
    $('.override_emails_payment').append(new_element);
    $('#new_override_payment').val("");
});
///////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////// 
function closeoverride_notice(id){
    $('.override_emails_notice .'+id).remove();
}
$('#new_override_notice').keydown(function(){
    $('.validation_error_notice').css('display','none');
});
$('.add_override_notice').click(function(){
    var email=$('#new_override_notice').val();
    if (!echeck(email)) {$('.validation_error_notice').css('display','block');return};
    var nm=parseInt($('.override_emails_notice div').last().attr('id'));
    if ($('.override_emails_notice div').last().attr('id')==null) nm=0; 
    nm=nm+1;
    var new_element='<div class="override_one_notice '+nm+'" id="'+nm+'">'+email+'<input type="hidden" value="'+email+'" name="override_notice['+nm+']" />                                            &nbsp;&nbsp;<a class="close_one_override" onclick="closeoverride_notice('+nm+')"><i class="fa fa-close"></i></a>  </div>'
    $('.override_emails_notice').append(new_element);
    $('#new_override_notice').val("");
});
///////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////// 
function closeoverride_emailReminder(id){
    $('.override_emails_emailReminder .'+id).remove();
}
$('#new_override_emailReminder').keydown(function(){
    $('.validation_error_emailReminder').css('display','none');
});
$('.add_override_emailReminder').click(function(){
    var email=$('#new_override_emailReminder').val();
    if (!echeck(email)) {$('.validation_error_emailReminder').css('display','block');return};
    var nm=parseInt($('.override_emails_emailReminder div').last().attr('id'));
    if ($('.override_emails_emailReminder div').last().attr('id')==null) nm=0; 
    nm=nm+1;
    var new_element='<div class="override_one_emailReminder '+nm+'" id="'+nm+'">'+email+'<input type="hidden" value="'+email+'" name="override_emailReminder['+nm+']" />                                            &nbsp;&nbsp;<a class="close_one_override" onclick="closeoverride_emailReminder('+nm+')"><i class="fa fa-close"></i></a>  </div>'
    $('.override_emails_emailReminder').append(new_element);
    $('#new_override_emailReminder').val("");
});
///////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////// 
function closeoverride_smsReminder(id){
    $('.override_emails_smsReminder .'+id).remove();
}
$('#new_override_smsReminder').keydown(function(){
    $('.validation_error_smsReminder').css('display','none');
});
$('.add_override_smsReminder').click(function(){
    var email=$('#new_override_smsReminder').val();
    if (!pcheck(email)) {$('.validation_error_smsReminder').css('display','block');return};
    var nm=parseInt($('.override_emails_smsReminder div').last().attr('id'));
    if ($('.override_emails_smsReminder div').last().attr('id')==null) nm=0; 
    nm=nm+1;
    var new_element='<div class="override_one_smsReminder '+nm+'" id="'+nm+'">'+email+'<input type="hidden" value="'+email+'" name="override_smsReminder['+nm+']" />                                            &nbsp;&nbsp;<a class="close_one_override" onclick="closeoverride_smsReminder('+nm+')"><i class="fa fa-close"></i></a>  </div>'
    $('.override_emails_smsReminder').append(new_element);
    $('#new_override_smsReminder').val("");
});
///////////////////////////////////////////////////////////////

function closeoverride_lastday_over(id){
    $('.override_lastday_over .'+id).remove();
}
$('#new_override_lastday_over').keydown(function(){
    $('.validation_error_override_lastday_over').css('display','none');
});
$('.add_override_lastday_over').click(function(){
    var email=$('#new_override_lastday_over').val();
    if (!echeck(email)) {$('.validation_error_override_lastday_over').css('display','block');return};
    var nm=parseInt($('.override_lastday_over div').last().attr('id'));
    if ($('.override_lastday_over div').last().attr('id')==null) nm=0; 
    nm=nm+1;
    var new_element='<div class="one_override_lastday_over '+nm+'" id="'+nm+'">'+email+'<input type="hidden" value="'+email+'" name="override_lastday_over['+nm+']" />                                            &nbsp;&nbsp;<a class="close_one_override" onclick="closeoverride_notice('+nm+')"><i class="fa fa-close"></i></a>  </div>'
    $('.override_lastday_over').append(new_element);
    $('#new_override_lastday_over').val("");
});

///////////////////////////////////////////////////////////////

function pcheck(str) {
var phoneno = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im;
  if(str.match(phoneno))
    {
      return true;
    }
  else
    {
    return false;
    }
}

function echeck(str) {

        var at="@"
        var dot="."
        var lat=str.indexOf(at)
        var lstr=str.length
        var ldot=str.indexOf(dot)
        if (str.indexOf(at)==-1){
           //alert("Invalid E-mail ID")
           return false
        }

        if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){
           //alert("Invalid E-mail ID")
           return false
        }

        if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){
            //alert("Invalid E-mail ID")
            return false
        }

         if (str.indexOf(at,(lat+1))!=-1){
            //alert("Invalid E-mail ID")
            return false
         }

         if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){
            //alert("Invalid E-mail ID")
            return false
         }

         if (str.indexOf(dot,(lat+2))==-1){
            //alert("Invalid E-mail ID")
            return false
         }
        
         if (str.indexOf(" ")!=-1){
            //alert("Invalid E-mail ID")
            return false
         }

         return true                    
    }    
    
    
$(function () {


// var update_message="{{ Session::get('message') }}";
// if (update_message.indexOf('Successfully')>-1){
//     $('.sidebar').css('pointer-events','auto');
// }else{
//   $('.sidebar').css('pointer-events','none');
// }



  $('[data-toggle="tooltip"]').tooltip()
  $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
 
  var s = $('#signature-panel').jqSignature().on('jq.signature.changed',function() {
      $("input[name='signature']").val($(this).jqSignature('getDataURL'));
  }); // Setup
  
  $('#clear-canvas').on('click',function() {
      $('#signature-panel').jqSignature('clearCanvas');
      $("input[name='signature']").val('');
      $("#currentsignature").attr('src','');
  });
 
 
  var countries = new Bloodhound({
  datumTokenizer: Bloodhound.tokenizers.whitespace,
  queryTokenizer: Bloodhound.tokenizers.whitespace,
  // url points to a json file that contains an array of country names, see
  // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
  //local: ['Afghanistan','Albania','Algeria','American Samoa','Andorra','Angola','Anguilla','Antarctica'],
  prefetch:  { url: '{{ route('list.countries') }}' , cache: false }
});

    // passing in `null` for the `options` arguments will result in the default
    // options being used
    $('#countries').typeahead(null, {
      name: 'countries',
      source: countries
    });
    
    
    var states = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.whitespace,
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        prefetch: {
            url: '{{ route('list.states') }}/%QUERY',
            prepare: function(settings) {
                if ($('#countries').val().length > 0) { 
                    return settings.url.replace('%QUERY',  $('#countries').val());
                } else { 
                    return settings.url.replace('%QUERY',  'none');              
                }
            },
            cache:false
        }
    });
    
     $('#states').typeahead(null, {
      name: 'states',
      source: states
    });
    
    $('#states').focus(function () {
        states.initialize(true);
    }); 
        
    var counties = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.whitespace,
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        // url points to a json file that contains an array of country names, see
        // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
        //local: ['Afghanistan','Albania','Algeria','American Samoa','Andorra','Angola','Anguilla','Antarctica'],
        prefetch:  { url: '{{ route('list.counties') }}' , cache: false }
      });
      
    $('#counties').typeahead(null, {
      name: 'counties',
      source: counties
    });
    
     var titles = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.whitespace,
         queryTokenizer: Bloodhound.tokenizers.whitespace,
            // url points to a json file that contains an array of country names, see
            // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
            local: ['President','Vice President','Secretary','Treasurer','Credit Mgr','Owner\'s Rep'],
            //prefetch:  { url: '{{ route('list.countries') }}' , cache: false }
        });
  
    $('#title').typeahead(null, {
      minLength: 0,
      name: 'title',
      source: titles
    });
  
})
</script>
    
@endsection