@extends('researcher.layouts.app')

@section('navigation')
    @include('researcher.navigation')
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
</style>
@endsection


@section('content')
            <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12">
                        <h1 class="page-header">Work Orders
                            <a class="btn btn-success pull-right" href="{{ route('workorders.create')}}"><i class="fa fa-plus"></i> New Work Order</a>
                        </h1>
                       
                    </div>
                        <div class="col-xs-12" id="filters-form">
                            {!! Form::open(['route' => 'workorders.setfilter', 'class'=>'form-inline'])!!}
                            <div class="row">
                                <div class="form-group">
                                    <label for="client_filter">Client: </label>
                                    {!! Form::select('client_filter',$clients,session('work_order_filter.client'),['class'=>'form-control','id'=>'client_filter'])!!}
                                </div>
                                <div class="form-group">
                                    <label for="client_filter">Job name: </label>
                                    {!! Form::select('job_filter',$jobs,session('work_order_filter.job'),['class'=>'form-control'])!!}
                                  </div>
                                
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="job_type_filter">Type: </label>
                                    {!! Form::select('work_type',$wo_types,session('work_order_filter.work_type'),['class'=>'form-control'])!!}
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
                                    <label for="work_rush"> Rush Type: </label>
                                    {!! Form::select('work_rush',['all' => 'All','0'=>'No','1'=>'Yes'],session('work_order_filter.work_rush'),['class'=>'form-control'])!!}
                                </div>
                                 <div class="form-group">
                                    <label for="work_status"> Status: </label>
                                    {!! Form::select('work_status',$statuses,session('work_order_filter.work_status'),['class'=>'form-control'])!!}
                                </div>
                                 <div class="form-group">
                                    <label for="work_condition"> Open/Close: </label>
                                    {!! Form::select('work_condition',$conditions,session('work_order_filter.work_condition'),['class'=>'form-control'])!!}
                                </div>
                                <div class="form-group">
                                    <label for="daterange"> Due Date: </label>
                                    {!! Form::text('daterange',session('work_order_filter.daterange'),['class'=>'form-control'])!!}
                                </div>
                            <button class="btn btn-success" type="submit" ><i class="fa fa-filter"></i> Filter</button>
                             <a href="{{ route('workorders.resetfilter') }}" class="btn btn-danger">Reset</a>
                             </div>
                            {!! Form::close() !!}
                           
                        </div>
                    
                        @if (Session::has('message'))
                            <div class="col-xs-12 message-box">
                            <div class="alert {{ Session::get('message-class','alert-info') }}">{{ Session::get('message') }}</div>
                            </div>
                        @endif
                    
                        
                        @if(count($works) > 0 )
                        <div class="col-xs-12">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>WO Number</th>
                                    <th>Type</th>
                                    <th>Job Name</th>
                                   
                                    <th>Client Name</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Rush</th>
                                    <th># Attachments</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($works as $work)
                                <tr>
                                    <td> {{ $work->number }}</td>
                                    <td> {{$wo_types[$work->type]}}</td>
                                    <td class="job_name"> 
                                        <div class="col-xs-12">
                                        <div class='job_name_align'><a href="{{ route('jobs.edit',$work->job->id)}}" class="btn btn-success btn-xs" data-toggle="tooltip" title="Edit Job" ><i class="fa fa-pencil"></i></a>{{ $work->job->name }}</div>
                                        
                                        <span >{!! nl2br($work->job->full_address_no_country) !!}</span>
                                        </div>
                                    </td>
                                    <td> {{ $work->job->client->company_name }}</td>
                                    <td> {{ (strlen($work->due_at) > 0) ? date('m/d/Y', strtotime($work->due_at)): '' }}</td>
                                    <td> {{ (array_key_exists($work->status,$statuses)) ? $statuses[$work->status]: 'None' }} </td>
                                    <td> {{ ($work->is_rush ==1 ) ? 'Yes' : 'No' }}</td>
                                    <td class="text-center"> {{ ($work->attachments->count()) }}</td>
                                    <td>
                                        <div class="btn-group pull-right dropup">
                                            <button type="button" class="btn btn-default dropdown-toggle " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-cogs"></i> Actions <span class="caret"></span>
                                            </button>
                                    @component('researcher.workorders.components.deletemodal')
                                        @slot('id') 
                                            {{ $work->id }}
                                        @endslot
                                        @slot('work_number') 
                                            {{ $work->number }}
                                        @endslot
                                    @endcomponent
                                     
                                        <li>
                                            <a class=" " href="{{ route('workorders.edit',$work->id)}}"><i class="fa fa-pencil"></i> Edit</a>
                                        </li>
                                        <li>
                                            <a class=" " href="{{ route('workorders.createinvoice',$work->id)}}"><i class="fa fa-file-text-o"></i> Create Invoice</a>
                                        </li>
                                        @if(in_array($work->type,$available_notices))
                                            @if(count($work->attachments->where('type','generated')) > 0)
                                            <li class="disabled ">
                                                <a  href="{{ route('workorders.document',$work->id)}}"><i class="fa  fa-file-pdf-o"></i> Create PDF</a>
                                            <li>
                                            <li class="">
                                                <a  href="{{ route('workorders.deletedocument',$work->id)}}"><i class="fa  fa-trash-o"></i> Delete PDF</a>
                                            <li>
                                            <li class="">
                                                <a  href="{{ route('workorders.show',$work->id)}}"><i class="fa  fa-eye"></i> View PDF</a>
                                            <li>
                                            @else
                                            <li class="">
                                                <a  href="{{ route('workorders.document',$work->id)}}"><i class="fa  fa-file-pdf-o"></i> Create PDF</a>
                                            <li>
                                            <li class="disabled">
                                                <a  href="{{ route('workorders.deletedocument',$work->id)}}"><i class="fa  fa-trash-o"></i> Delete PDF</a>
                                            <li>
                                            <li class="disabled ">
                                                <a  href="{{ route('workorders.show',$work->id)}}"><i class="fa  fa-eye"></i> View PDF</a>
                                            <li>
                                            @endif
                                            
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
    
     $('#client_filter').select2();
    
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
</script>
@endsection