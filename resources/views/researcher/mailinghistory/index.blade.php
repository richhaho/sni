@extends('researcher.layouts.app')

@section('navigation')
    @include('researcher.navigation')
@endsection
@section('css')
<link href="{{asset('vendor/bootstrap-datarange/css/daterangepicker.css')}}" rel="stylesheet" type="text/css"/>
<style>
    h1.with-buttons {
        display: block;
        width: 100%;
        float: left;
    }
    .page-header h1 { margin-top: 0; }
    
     #filters-form {
        margin-bottom: 15px;
        margin-top: 15px;
    }
    
    input[name="daterange"] {
            min-width: 180px;
    }
    
  

    td.address  {
       line-height: 0.8!important;
    }

    td.address span{
        font-size: 0.8em;
    }
</style>
@endsection

@section('content')
            <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="page-header col-xs-12">
                        <div class="col-xs-12">
                            <h1 class="" > Mail to Resend</h1>
                            
                        </div>
                        
                        
                    </div>
                       
                    
                         @if (Session::has('message'))
                            <div class="col-xs-12 message-box">
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                            </div>
                        @endif
                       <div class="col-xs-12" id="filters-form">
                            {!! Form::open(['route' => 'mailinghistory.setfilter', 'class'=>'form-inline'])!!}
                                @if(count($clients) > 0)
                                <div class="form-group">
                                    <label for="mailing_type_filter">Client: </label>
                                    {!! Form::select('client_filter',$clients,session('mailinghistory_filter.client'),['class'=>'form-control'])!!}
                                </div>
                                @endif
                                <div class="form-group">
                                    <label for="mailing_type_filter"> Mailing Type: </label>
                                    {!! Form::select('mailing_type',$mailing_types,session('mailinghistory_filter.mailing_type'),['class'=>'form-control'])!!}
                                </div>
                                
                                <div class="form-group">
                                    <label for="wo_type_filter"> Work Order Type: </label>
                                    {!! Form::select('wo_types',$wo_types,session('mailinghistory_filter.wo_types'),['class'=>'form-control'])!!}
                                </div>
                            
                                 <div class="form-group">
                                    <label for="job_filter"> Job: </label>
                                    {!! Form::select('job',$jobs,session('mailinghistory_filter.job'),['class'=>'form-control select2'])!!}
                                </div>
                            
                                 <div class="form-group">
                                    <label for="job_filter"> Barcode: </label>
                                    {!! Form::text('barcode',session('mailinghistory_filter.barcode'),['class'=>'form-control'])!!}
                                </div>

                                <div class="form-group">
                                        <label for="mailinghistory_filter"> Mailing Date: </label>
                                        {!! Form::text('daterange',session('mailinghistory_filter.daterange'),['class'=>'form-control'])!!}
                                    </div>

                            <button class="btn btn-success" type="submit" ><i class="fa fa-filter"></i> Filter</button>
                             <a href="{{ route('mailinghistory.resetfilter') }}" class="btn btn-danger">Reset</a>
                            {!! Form::close() !!}

                        </div>
                        
                        @if(count($mailings) > 0 )
                        <div class="col-xs-12">
                      
                            
                            
                            
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    
                                    <th>Recipient</th>
                                    <th>Barcode</th>
                                    <th>Date</th>
                                    <th>Mailing Type</th>
                                    <th>Work Order ID</th>
                                    <th>Work Order Type</th>
                                    
                                    <th>Job Name</th>
                                    <th>Resent Date</th>
                                    
                                    <th class="col-xs-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                @foreach($mailings as $mailing)
                                @if(!$mailing->resent_at)
                                <tr>
                                     <td class="address">
                                        {{$mailing->recipient->firm_name}}<br />
                                        <span >{!! nl2br($mailing->recipient->address) !!}
                                        </span>
                                    </td>
                                    <td>@if(strlen($mailing->recipient->barcode)){{ $mailing->recipient->barcode }} @else N/A @endif</td>
                                    <td>{{  $mailing->printed_at ? $mailing->printed_at->format('m-d-Y') : ''}}</td>                
                                    <td> @if($mailing->recipient){{ $mailing_types[$mailing->recipient->mailing_type] }}@endif</td>
                                    <td> {{  $mailing->attachable->number  }}</td>
                                    <td> {{  $mailing->attachable->order_type->name  }}</td> 

                                     <td> {{  $mailing->attachable->job->name  }}</td> 
                                    
                                    <td> {{  $mailing->resent_at ? $mailing->resent_at->format('m-d-Y') : '' }} </td>
                                    <td>
                                        @if( $mailing->resent_at)
                                        @else
                                        @component('researcher.mailinghistory.components.deletemodal')
                                        @slot('id') 
                                            {{ $mailing->id }}
                                        @endslot
                                        @endcomponent
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        <div class="col-xs-12 text-center">
                            {{ $mailings->links() }}
                        </div>
                        @else
                        <div class="col-xs-12">
                            <h5>No Mailing Items found</h5>
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
<script src="{{asset('vendor/bootstrap-datarange/js/daterangepicker.js')}}" type="text/javascript"></script>
<script>

$('.btn-resend').click(function(){
    $('.btn-resend').addClass("disabled");
});
        
$(function () {
    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
    
    var start = moment().subtract(29, 'days');
    var end = moment();
    
    function cb(start, end) {
        if ( $('input[name="daterange"] span').html() =='') {
            $('input[name="daterange"] span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }
    }
    
    $('input[name="daterange"]').daterangepicker({
        timePicker: false,
        autoUpdateInput: false,
        locale: {
            format: 'MM-DD-YYYY'
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