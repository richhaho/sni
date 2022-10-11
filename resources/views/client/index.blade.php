
@extends('client.layouts.app')
@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('navigation')
    @include('client.navigation')
@endsection

@section('content')
            <div id="page-wrapper">
            
                <div class="row">
                    
                    
                    <div class="col-lg-12">
                        <div class="row">
  
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-briefcase fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">{{ $jobs_count }} </div>
                                    <div>Open Jobs</div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('client.jobs.index')}}">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-tasks fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"> {{ $notices_count }}</div>
                                    <div>Open Work Orders</div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('client.notices.index')}}">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-yellow">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-shopping-cart fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">{{$invoices_count }}</div>
                                    <div>Unpaid Invoices</div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('client.invoices.index')}}">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-red">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-plus-square-o fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"></div>
                                    <div>Create a Work Order</div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ Auth::user()->verified ? route('wizard2.getjobworkorder') : '' }}">
                            <div class="panel-footer">
                                <span class="pull-left">Create</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            <!-- /.container-fluid -->
            <br><br>
                        
                        @if(count($jobs) > 0 && $job_count_withoutwork>0)
                        <center><h3 style="color: red"> Jobs Needing a Work Order</h3></center>
                        
                        <div class="col-xs-12">
                            <h4>We cannot start work on your documents for these jobs until a work order is created.</h4>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Job Name</th>
                                    <th class="text-center">Job Number</th>
                                    <th class="text-center">Job Address</th>
                                    <th class="text-center">Customer</th>
                                    <th class="text-center">Created Date </th>
                                    <th class="col-xs-1"></th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                @foreach($jobs as $job)
                                <?php 
                                $name='';
                                $phone='';
                                $email='';
                                $customer=$job->parties->where('type','customer')->first();
                                if (count($customer)>0){
                                    $customer_contact= $customer->contact;
                                    if(count($customer_contact)>0){
                                        $name=$customer->firm->firm_name;
                                        $phone=$customer_contact->phone;
                                        $email=$customer_contact->email;

                                    };
                                }

                                ?>
                                <tr>
                                    <td> {{ $job->name }}</td>
                                    <td> {{ $job->number }}</td>
                                    <td> {!! nl2br($job->full_address_no_country) !!} </td>
                                    <td> {{$name}}  </td>
                                    <td> {{ (strlen($job->created_at) > 0) ? date('m/d/Y', strtotime($job->created_at)): '' }}</td>
                                     
                                     
                                    <td>
                                        
                                            <a href="{{ route('wizard2.getjobworkorder')}}?job_id={{$job->id}}" class="btn btn-success form-control" >
                                            Create WorkOrder
                                            </a>
                                       
                                            <ul  style="margin-top: 5px;list-style: none;padding: 0px">
                                            @component('client.jobs.components.closemodal')
                                            @slot('id') 
                                                {{ $job->id }}
                                            @endslot
                                            @slot('client_name') 
                                                {{ $job->name }}
                                            @endslot
                                            @endcomponent
                                        
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                         
                        @endif
            <br><br>
            <div class="col-xs-12">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="panel panel-green">
                                <div class="panel-heading">
                                    
                                    <center><h4>Notice to Owner Due Date </h4></center>
                                    <br><br>
                                    <p>Although you have 45 days from the job start date to file your notice to owner, you only have 40 days from the job start date to mail that Notice out.</p>
                                    <br>
                                    <p>What was your first day on the job?</p>
                                    <input class="form-control date-picker first_date" data-date-format="mm/dd/yyyy" data-date-autoclose="true" data-toggle="tooltip" data-placement="bottom" title="">
                                    <button type='button' class="form-control btn btn-default btn_first_date">CALCULATE</button>
                                    <p>Notice to Owner must be mailed by:</p>
                                    <input class="form-control  cal_first_date">
                                    <p>in order to have lien rights and proceed to Step 2. (Even if you are past your time, it can't hurt you to file anyway).</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    
                                    <center><h4>Claim of Lien Due Date </h4></center>
                                    <br><br>
                                    <p>You have 90 days from the last day on the job to file your Claim of Lien.</p>
                                    <br>
                                    <p>When was your last day on the job?</p>
                                    <input class="form-control date-picker second_date" data-date-format="mm/dd/yyyy" data-date-autoclose="true" data-toggle="tooltip" data-placement="bottom" title="">
                                    <button type='button' class="form-control btn btn-default btn_second_date">CALCULATE</button>
                                    <p>Claim of Lien must be recorded by:</p>
                                    <input class="form-control  cal_second_date">
                                    <p>in order to keep your Lien Rights intact and proceed to Step 3.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="panel panel-red">
                                <div class="panel-heading">
                                    
                                    <center><h4>Foreclose on Claim of Lien Due Date </h4></center>
                                    <br><br>
                                    <p>You have 1 year from the date your Claim of Lien was recorded to foreclose.</p>
                                    <br>
                                    <p>When was your Claim of Lien recorded?</p>
                                    <input class="form-control date-picker third_date" data-date-format="mm/dd/yyyy" data-date-autoclose="true" data-toggle="tooltip" data-placement="bottom" title="">
                                    <button type='button' class="form-control btn btn-default btn_third_date">CALCULATE</button>
                                    <p>Must Foreclose Claim of Lien no later than:</p>
                                    <input class="form-control  cal_third_date">
                                    <p>in order to be able to collect monies owed.</p>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>

         
            
        
@endsection
@section('scripts')
<script src="{{ asset('/vendor/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $('.date-picker').datepicker();
$('.btn_first_date').click(function(){
    var dt=new Date($('.first_date').val());
    dt.setDate(dt.getDate()+39);
    $('.cal_first_date').val((dt.toDateString()));
});
$('.btn_second_date').click(function(){
    var dt=new Date($('.second_date').val());
    dt.setDate(dt.getDate()+89);
    $('.cal_second_date').val((dt.toDateString()));
});

$('.btn_third_date').click(function(){
    var dt=new Date($('.third_date').val());
    dt.setDate(dt.getDate()+364);
    $('.cal_third_date').val((dt.toDateString()));
});

$('li .close-a').addClass('btn btn-danger form-control');

</script>
@endsection