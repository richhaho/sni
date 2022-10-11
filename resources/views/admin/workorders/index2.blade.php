@extends('admin.layouts.app')

@section('navigation')
    @include('admin.navigation')
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
    .phonecall{
         word-break: break-all;
    }
    .job_address_filter{
        width: 400px !important;
    }
    #job_filter{
        width: 400px !important;
    }
</style>
@endsection


@section('content')
            <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12">
                        <h1 class="page-header">Self Service Work Orders
                            <a class="btn btn-success pull-right" href="{{ route('workorders.create')}}"><i class="fa fa-plus"></i> New Work Order</a>
                        </h1>
                       
                    </div>
                        <div class="col-xs-12" id="filters-form">
                            {!! Form::open(['route' => 'workorders.setfilter2', 'class'=>'form-inline'])!!}
                            <div class="row">
                                <div class="form-group">
                                    <label for="client_filter">Client: </label>
                                    {!! Form::select('client_filter',$clients,session('work_order_filter2.client'),['class'=>'form-control','id'=>'client_filter'])!!}
                                </div>
                                <div class="form-group">
                                    <label for="job_type_filter">Type: </label>
                                    {!! Form::select('work_type',$wo_types,session('work_order_filter2.work_type'),['class'=>'form-control'])!!}
                                </div>
                                
                                
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="job_filter">Job name: </label>
                                    {!! Form::select('job_filter',$jobs,session('work_order_filter2.job'),['class'=>'form-control','id'=>'job_filter'])!!}
                                  </div>
                                
                                <div class="form-group">
                                    <label for="job_address_filter">Job Address: </label>
                                    {!! Form::text('job_address',session('work_order_filter2.job_address'),['class'=>'form-control job_address_filter'])!!}
                                </div>
                                <div class="form-group">
                                    <label for="job_address_filter">Job County: </label>
                                    {!! Form::text('job_county',session('work_order_filter2.job_county'),['class'=>'form-control job_county_filter'])!!}
                                </div>
                            </div>
                            <div class="row">    
                                <div class="form-group">
                                    <label for="customer_name_filter">Customer Name: </label>
                                    {!! Form::select('customer_name',$customers,session('work_order_filter2.customer_name'),['class'=>'form-control','id'=>'customer_name','style'=>'width:350px'])!!}
                                </div>
                                <div class="form-group">
                                    <label for="work_number_filter">WO Number: </label>
                                    {!! Form::text('work_number',session('work_order_filter2.work_number'),['class'=>'form-control'])!!}
                                </div>
                                <div class="form-group">
                                    <label for="job_number_filter">Job Number: </label>
                                    {!! Form::text('job_number',session('work_order_filter2.job_number'),['class'=>'form-control'])!!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="work_rush"> Rush Type: </label>
                                    {!! Form::select('work_rush',['all' => 'All','0'=>'No','1'=>'Yes'],session('work_order_filter2.work_rush'),['class'=>'form-control'])!!}
                                </div>
                                 <div class="form-group">
                                    <label for="work_status"> Status: </label>
                                    {!! Form::select('work_status',$statuses,session('work_order_filter2.work_status'),['class'=>'form-control'])!!}
                                </div>
                                 <div class="form-group">
                                    <label for="work_condition"> Open/Close: </label>
                                    {!! Form::select('work_condition',$conditions,session('work_order_filter2.work_condition'),['class'=>'form-control'])!!}
                                </div>
                                <div class="form-group">
                                    <label for="daterange"> Due Date: </label>
                                    {!! Form::text('daterange',session('work_order_filter2.daterange'),['class'=>'form-control'])!!}
                                </div>
                            <button class="btn btn-success" type="submit" ><i class="fa fa-filter"></i> Enter</button>
                             <a href="{{ route('workorders.resetfilter2') }}" class="btn btn-danger">Clear</a>
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
                                    <th>Customer Name</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    @if (session('work_order_filter2.work_status')=="phone calls")
                                    <th>Customer Phone</th>
                                    @endif
                                    <th>Rush</th>
                                    <th># Attachments</th>

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
                                         
                                        //$name=$customer_contact->first_name.' '.$customer_contact->last_name;
                                        $name=$customer->firm->firm_name;
                                        $phone=$customer_contact->phone;
                                        $email=$customer_contact->email;

                                    };
                                }
                                $style='';
                                $diff=0;
                                $status=['completed','cancelled','cancelled charge','cancelled no charge','closed'];
                                    if ($work->has_todo){
                                        $style='background-color: #FFE4E1';
                                    }
                                ?>
                                <tr style="{{$style}}">
                                    <td> {{ $work->number }}</td>
                                    <td> {{$wo_types[$work->type]}}</td>
                                    <td class="job_name"> 
                                        <div class="col-xs-12">
                                        <div class='job_name_align'><a href="{{ route('jobs.edit',$work->job->id)}}" class="btn btn-success btn-xs" data-toggle="tooltip" title="Edit Job" ><i class="fa fa-pencil"></i></a>{{ $work->job->name }}</div>
                                        
                                        <span >{!! nl2br($work->job->full_address_no_country) !!}</span>
                                        </div>
                                    </td>
                                    <td> {{ $work->job->client->company_name }}</td>
                                    <td> {{ $name  }}</td>
                                    <td> {{ (strlen($work->mailing_at) > 0) ? date('m/d/Y', strtotime($work->mailing_at)): '' }}</td>
                                    <td> {{ (array_key_exists($work->status,$statuses)) ? $statuses[$work->status]: 'None' }} </td>

                                    @if (session('work_order_filter2.work_status')=="phone calls")
                                    <td class="phonecall">{{$name}}<br>{{$phone}}
                                       
                                        <br>
                                        <a href="mailto:{{$email}}?subject={{str_replace('&','%26',$work->job->name)}} @ {{str_replace('<br>',' ',str_replace('&','%26',$work->job->full_address_no_country))}} WO #{{ $work->number }} on behalf of {{str_replace('&','%26',$work->job->client->company_name)}}&body=We need to know who your company is contracted by (Working for?) on the above referenced job - please include their Name, address and phone number.%0D%0A%0D%0AThanks.%0D%0A%0D%0AIf there is anything that Sunshine Notices can do for you in the way of protecting your lien rights or tracking your jobs to help you get paid, please do not hesitate to call us at 800-774-9888 or go to www.SunshineNotices.com %0D%0A%0D%0A">{{$email}}</a> </td>
                                    @endif


                                    <td> {{ ($work->is_rush ==1 ) ? 'Yes' : 'No' }}</td>
                                    <td class="text-center"> {{ ($work->attachments->count()) }}</td>
                                    <td>
                                        <div class="btn-group pull-right dropup">
                                            <button type="button" class="btn btn-default dropdown-toggle " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-cogs"></i> Actions <span class="caret"></span>
                                            </button>
                                    @component('admin.workorders.components.deletemodal')
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
                                            <a class=" " href="{{ route('workorders.newinvoice',$work->id)}}?fromindex=1"><i class="fa fa-file-text-o"></i> Create Invoice</a>
                                        </li>
                                        <li class="">
                                            <a href="{{ route('jobs.summary',$work->job->id)}}"><i class="fa fa-book"></i> View Job Summary</a>
                                        <li>
                                        @if(in_array($work->type,$available_notices))
                                            @if(count($work->attachments->where('type','generated')) > 0)
                                            @if (Session::get('user_role')=='admin')
                                            <li class="disabled ">
                                                <a  href="{{ route('workorders.document',$work->id)}}"><i class="fa  fa-file-pdf-o"></i> Create PDF</a>
                                            <li>
                                            <li class="">
                                                <a  href="{{ route('workorders.deletedocument',$work->id)}}"><i class="fa  fa-trash-o"></i> Delete PDF</a>
                                            <li>
                                            @endif
                                            <li class="">
                                                <a  href="{{ route('workorders.show',$work->id)}}"><i class="fa  fa-eye"></i> View PDF</a>
                                            <li>
                                            @else
                                            
                                            <li class="">
                                                <a  href="{{ route('workorders.document',$work->id)}}"><i class="fa  fa-file-pdf-o"></i> Create PDF</a>
                                            <li>
                                            @if (Session::get('user_role')=='admin')
                                            <li class="disabled">
                                                <a  href="{{ route('workorders.deletedocument',$work->id)}}"><i class="fa  fa-trash-o"></i> Delete PDF</a>
                                            <li>
                                            @endif
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
    $("#customer_name").select2({
        theme:'bootstrap',
        minimumInputLength: 2,
        ajax: {
            url: '{{url("/admin/customerlist")}}',
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
                            name: item.firm_name,
                            id: item.id,
                            number: item.id,
                            text: item.firm_name,
                        }
                    })
                };
            }
        }
    });
    $('#client_filter').change(function() {
        jobFilterEvent();
    });
    jobFilterEvent();
    function jobFilterEvent() {
        const client_id = $('#client_filter').val() || 0;
        $("#job_filter").select2({
            theme:'bootstrap',
            minimumInputLength: 2,
            ajax: {
                url: '{{url("/admin/clients")}}/'+ client_id + '/joblist',
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
    }
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
</script>
@endsection