@extends('client.layouts.app')

@section('navigation')
    @include('client.navigation')
@endsection

@section('css')
<link href="{{asset('vendor/bootstrap-datarange/css/daterangepicker.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<style>
    #filters-form {
        margin-bottom: 15px;
        *margin-top: 15px;
    }
     #filters-form div.row {
         margin-top: 15px;
     }
         
    input[name="daterange"] {
            min-width: 180px;
    }
    
    td.job_name  {
       line-height: 0.8!important;
    }

    td.job_name span{
        font-size: 0.8em;
    }
    .job_name_align{
        word-break: break-all;
    }
    .job_address{
        width: 400px !important;
    }
    #job_filter{
        width: 400px !important;
    }
    input[type="checkbox"]{
        margin-top: -14px !important;
    }
</style>
@endsection


@section('content')
            <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12">
                        <h1 class="page-header">Work Orders
                            @if(Auth::user()->verified)
                            <a class="btn btn-success pull-right" href="{{ route('wizard2.getjobworkorder')}}"><i class="fa fa-plus"></i> New Work Orders</a>
                            @endif
                        </h1>
                       
                    </div>
                        <div class="col-xs-12" id="filters-form">
                            {!! Form::open(['route' => 'client.notices.setfilter', 'class'=>'form-inline'])!!}
                            <div class="row">
                                 <div class="form-group">
                                    <label for="term_filter">Search Terms: </label>
                                    {!! Form::text('term_filter',session('work_order_filter.term'),['class'=>'form-control','id'=>'term_search'])!!}
                                  </div>
                                <div class="form-group">
                                    <label for="job_type_filter">Type: </label>
                                    {!! Form::select('work_type',$wo_types,session('work_order_filter.work_type'),['class'=>'form-control'])!!}
                                </div>
                                
                                
                            </div>
                            <div class="row">
                                
                                
                                <div class="form-group">
                                    <label for="client_filter">Job name: </label>
                                    {!! Form::select('job_filter',$jobs,session('work_order_filter.job'),['class'=>'form-control','id'=>'job_filter'])!!}
                                </div>
                                <div class="form-group">
                                    <label for="job_address_filter">Job Address: </label>
                                    {!! Form::text('job_address',session('work_order_filter.job_address'),['class'=>'form-control job_address'])!!}
                                </div>
                                <div class="form-group">
                                    <label for="job_address_filter">Job County: </label>
                                    {!! Form::text('job_county',session('work_order_filter.job_county'),['class'=>'form-control job_county_filter'])!!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="customer_name_filter">Customer Name: </label>
                                    {!! Form::select('customer_name',$customers,session('work_order_filter.customer_name'),['class'=>'form-control','id'=>'customer_name','style'=>'width:350px'])!!}
                                </div>
                                <div class="form-group">
                                    <label for="job_number_filter">Job Number: </label>
                                    {!! Form::text('job_number',session('work_order_filter.job_number'),['class'=>'form-control'])!!}
                                </div>

                                <div class="form-group">
                                    <label for="work_number_filter">WO Number: </label>
                                    {!! Form::text('work_number',session('work_order_filter.work_number'),['class'=>'form-control'])!!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="job_rush_filter"> Rush Type: </label>
                                    {!! Form::select('work_rush',['all' => 'All','0'=>'No','1'=>'Yes'],session('work_order_filter.work_rush'),['class'=>'form-control'])!!}
                                </div>
                            
                                
                                 <div class="form-group">
                                    <label for="job_status_filter"> Status: </label>
                                    {!! Form::select('work_status',$statuses,session('work_order_filter.work_status'),['class'=>'form-control'])!!}
                                </div>
                                 <div class="form-group">
                                    <label for="work_condition"> Open/Close: </label>
                                    {!! Form::select('work_condition',$conditions,session('work_order_filter.work_condition'),['class'=>'form-control'])!!}
                                </div>
                                <div class="form-group hidden" >
                                    <label for="job_type_filter"> Due Date: </label>
                                    {!! Form::text('daterange',session('work_order_filter.daterange'),['class'=>'form-control'])!!}
                                </div>
                            <button class="btn btn-success" type="submit" ><i class="fa fa-filter"></i> Enter</button>
                             <a href="{{ route('client.notices.resetfilter') }}" class="btn btn-danger">Clear</a>
                             </div>
                            {!! Form::close() !!}
                           
                        </div>
                    
                        @if (Session::has('message'))
                            <div class="col-xs-12 message-box">
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                            </div>
                        @endif
                    
                        
                        @if(count($works) > 0 )
                        {!! Form::open(['route' => 'client.attachment.printSelected', 'class'=>'form-inline','method'=>'GET'])!!}
                        <div class="col-xs-12">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>WO Number</th>
                                    <th>Customer Name</th>
                                    <th>Job Number</th>
                                    <th>Job Start Date</th>
                                    <th>Job Name</th>
                                    <th><button class="btn btn-xs btn-warning" type="submit">Print <br>Selected</button></th>
                                    <th>Work Order Type</th>
                                    <th>Work Order Status</th>
                                    <th>Service</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($works as $work)
                                <?php 
                                $name='';
                                $phone='';
                                $email='';
                                $customer=$work->job->parties->where('type','customer')->first();
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
                                    <td> {{ $work->number }}<p><center>{{ ($work->is_rush ==1 ) ? 'RUSH' : '' }}</center></p></td>
                                    <td>{{$name}}</td>
                                    <td>{{ $work->job->number}}</td>
                                    <td> {{ (strlen($work->job->started_at) > 0) ? date('m/d/Y', strtotime($work->job->started_at)): '' }}</td>

                                    
                                    <td class="job_name"> 
                                        <div class="col-xs-12">
                                        <div class="job_name_align"><a href="{{ route('client.jobs.edit',$work->job->id)}}" class="btn btn-success btn-xs" data-toggle="tooltip" title="Edit Job" ><i class="fa fa-pencil"></i></a>{{ $work->job->name }} 
                                        </div>
                                        <span >{!! nl2br($work->job->full_address_no_country) !!}</span>
                                        </div>
                                    </td>
                                    <td> 
                                        @if(count($work->invoicesPaid)>0 ||  $work->job->client->billing_type == "invoiced")
                                            @if (count($work->attachments)>0)
<?php 
$attach_id=null;
foreach ($work->attachments as $attach ) {
     
    if($attach->recipient['party_type'] =="customer" ){
            $attach_id=$attach->id;
    }
}
if (!$attach_id){
foreach ($work->attachments as $attach ) {    
    if($attach->type == 'generated'){
            $attach_id=$attach->id;break;
    }
}
}

if ($work->job->client->billing_type !== 'invoiced'){
    foreach ($work->invoices as $wo_invoices ) {
        if ($wo_invoices->status == "open" || $wo_invoices->status == "unpaid"){
            $attach_id=null;break;
        }
    }
}
//echo $attach_id;
if ($attach_id){
?>
                                                <div class="checkbox checkbox-slider--b-flat">
                                                <label>
                                                    <input name="attach[{{$attach_id}}]"  type="checkbox" class="selection"><span></span>
                                                </label>
                                                </div>
<?php } ?>
                                            @endif
                                        @endif
                                    </td>
                                    <td>  {{$wo_types[$work->type]}} </td>
                                    <td> {{ (array_key_exists($work->status,$statuses)) ? $statuses[$work->status]: 'None' }} </td>
                                    <td> {{ $work->service=='self' ? 'Self-Service' : 'Full-Service' }} </td>
                                    <td>
                                        @if($work->service=='self')
                                            @component('client.workorders.components.cancelmodal')
                                                @slot('id') 
                                                    {{ $work->id }}
                                                @endslot
                                                @slot('work_number') 
                                                    {{ $work->number }}
                                                @endslot
                                            @endcomponent
                                        @endif

                                        <div class="btn-group pull-right dropup">
                                            <button type="button" class="btn btn-default dropdown-toggle " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-cogs"></i> Actions <span class="caret"></span>
                                            </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li>
                                            <a class=" " href="{{ route('client.notices.edit',$work->id)}}"><i class="fa fa-pencil"></i> Edit</a>
                                        </li>
                                        <li><a href="{{ route('client.jobs.summary',$work->job->id)}}"><i class="fa fa-book"></i> View Job Summary</a><li>
                                        @if (!in_array($work->status,['cancelled','cancelled charge','completed','cancelled no charge']) && $work->job->status != 'closed')
                                            @if($work->service=='self')
                                            <li><a href="#" data-toggle="modal" data-target="#modal-workorder-cancel-{{ $work->id }}"><i class="fa fa-times"></i> Cancel Work Order</a></li>
                                            <li><a class=" " href="{{ route('client.notices.requestService',$work->id)}}"><i class="fa fa-folder-open-o"></i> Request Additional Services</a></li>
                                            @else
                                            <li>
                                                <a class=" " href="{{ route('client.notices.cancel',$work->id)}}"><i class="fa fa-times"></i> Cancel Work Order</a>
                                            </li>
                                            @endif
                                        @endif
                                        @if($work->attachments->count() > 0)
                                        <li>
                                            <a class=" " href="{{ route('client.notices.edit',$work->id)}}#attachments"><i class="fa fa-eye"></i> View Notices/Attachments</a>
                                        </li>
                                        @endif
                                        
                                    </ul>
                                      </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        {!! Form::close() !!}
                        <div class="col-xs-12 text-center">
                            {{ $works->links() }}
                        </div>
                        @else
                        <div class="col-xs-12">
                            <h5>No Work Orders found</h5>
                        </div>
                        @endif
                    
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
@endsection

@section('scripts')
<script src="{{ asset('/vendor/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{asset('vendor/bootstrap-datarange/js/daterangepicker.js')}}" type="text/javascript"></script>
<script>
$.fn.select2.defaults.set("theme", "bootstrap");    
    
$(function () {
    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
    
    $('#customer_name').select2();
    $("#job_filter").select2({
        theme:'bootstrap',
        minimumInputLength: 2,
        ajax: {
            url: '{{url("/client/jobs/joblist")}}',
            dataType: 'json',
            type: "GET",
            delay: 50,
            data: function (params) {
                $(".select2-dropdown").find('.searching').remove();
                $(".select2-dropdown").prepend('<span class="searching">&nbsp;Searching...</span>');
                return params;
            },
            processResults: function (data) {
                $(".select2-dropdown").find('.searching').remove();   
                return {
                    results: $.map(data, function (item) {
                        return {
                            name: item.name,
                            id: item.id,
                            number: item.number,
                            text: item.name,
                        }
                    })
                };
            }
        },
        templateResult: formatJob
    });
    function formatJob (job) {
        var str = '<span>' + job.name + '</span>';
        var $state = $(str);
        return $state;
    }

    var start = moment().subtract(29, 'days');
    var end = moment();
    
    function cb(start, end) {
        if ( $('input[name="daterange"] span').html() =='') {
            $('input[name="daterange"] span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }
    }
    
    var auto;

    $('input[name="daterange"]').daterangepicker({
        timePicker: false,
        autoUpdateInput: false,
        locale: {
            format: 'MM-DD-YYYY',
        },
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
        
    }, function(start, end, label) {
            
            $('input[name="daterange"]').val(start.format('MM-DD-YYYY') + ' - ' + end.format('MM-DD-YYYY'));
       });
    cb(start, end);
});
function cancelWorkorder(id) {
    location.href = "{{url('')}}/client/notices/" + id + "/cancel";
    $('#modal-workorder-cancel-' + id).modal('hide');
}
</script>
@endsection