@extends('admin.layouts.app')

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
<style>
    .table > tbody > tr > .no-line {
      border-top: none;
  }

  .table > thead > tr > .no-line {
      border-bottom: none;
  }

  .table > tbody > tr > .thick-line {
      border-top: 2px solid;
  }
  table td {
      vertical-align: top;
      padding-top: 5px;
      word-break: break-all;
  }

  @media print {
     .btn-print{ display: none; }
     .btn-back{ display: none; }
    }
</style>

@endsection

@section('navigation')
    @include('admin.navigation')
@endsection

@section('content')
    
<div id="top-wrapper" >
    <div class="container-fluid">
        <div class="col-xs-12">
            <br>
            <div class="col-xs-12 pull-right">
                <button type="button" class="btn btn-success btn-print pull-right"> <i class="fa fa-print"></i> &nbsp;&nbsp; Print&nbsp;&nbsp;&nbsp;&nbsp;</button>
                <a style="margin-right: 5px" class="btn btn-danger pull-right btn-back" href="{{ url()->previous() }}"><i class="fa fa-arrow-left"></i> &nbsp;&nbsp;Back&nbsp;&nbsp;</a>
            </div>
        </div>
        <div  class="col-xs-12">
            <div class="page-header">
                <h3 class="text-center">Job Summary for {{$job->name}}</h3>  
            </div>
        </div>
    </div>
</div>
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="col-xs-12">
            <h4>Job Info:</h4>
            <table style="width:100%">
                <tr>
                    <td width="20%"><strong>Date Entered:</strong></td>
                    <td width="30%">{{date('m/d/Y', strtotime($job->created_at))}}</td>
                    <td width="20%"><strong>Date Printed:</strong></td>
                    <td width="30%">{{$nto_printed_at}}</td>
                </tr>
                <tr>
                    <td width="20%"><strong>Job Number:</strong></td>
                    <td colspan="3">{{$job->number}}</td>
                </tr>
                <tr>
                    <td width="20%"><strong>County:</strong></td>
                    <td colspan="3">{{$job->county}}</td>
                </tr>
                <tr>
                    <td width="20%"><strong>Job Site:</strong></td>
                    <td colspan="3">{{$job->name}} {{$job->address_1}} {{$job->address_2}}, {{$job->city}}, {{$job->state}}, {{$job->zip}}, {{$job->county}}, {{$job->address_source}}</td>
                </tr>
                <tr>
                    <td width="20%"><strong>Legal Description:</strong></td>
                    <td colspan="3">
                        {{$job->legal_description}} 
                        @if($job->legal_description_source)
                        ({{$job->legal_description_source}})
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="20%"><strong>Materials/Services Provided:</strong></td>
                    <td colspan="3">{{$job->default_materials}}</td>
                </tr>
                <tr>
                    <td width="20%"><strong>Job Start Date:</strong></td>
                    <td colspan="3">{{$job->started_at ? date('m/d/Y', strtotime($job->started_at)) : ''}}</td>
                </tr>
                <tr>
                    <td width="20%"><strong>Job End Date:</strong></td>
                    <td colspan="3">{{$job->last_day ? date('m/d/Y', strtotime($job->last_day)) : ''}}</td>
                </tr>
                <tr>
                    <td width="20%"><strong>Contract Amount:</strong></td>
                    <td colspan="3">${{number_format($job->contract_amount, 2, '.', ',')}}</td>
                </tr>
            </table>
        </div>
        <div class="col-xs-12" style="margin-top: 20px">
            <h4>Balance Info:</h4>
            <table style="width:100%">
                <tr>
                    <td style="width:20%"><strong>Change Orders:</strong></td>
                    <td style="width:80%">
                        <table class="table">
                            <thead>
                                <tr>
                                    <td width="20%">Added on</td>
                                    <td width="20%">Amount</td>
                                    <td width="40%">Description</td>
                                    <td width="20%">Attachment</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($job->changes as $change)
                                <tr>
                                    <td>{{ (strlen($change->added_on) > 0) ? date('m/d/Y', strtotime($change->added_on)): 'N/A' }}</td>
                                    <td>{{ number_format($change->amount,2) }}</td>
                                    <td>{{ $change->description }}</td>
                                    <td>
                                        @if($change->attached_file)
                                        <a href="{{ route('jobchanges.showattachment',[$change->id])}}"><i class="fa fa-download"></i></a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="width:20%"><strong>Payments:</strong></td>
                    <td style="width:80%">
                        <table class="table">
                            <thead>
                                <tr>
                                    <td width="20%">Paid on</td>
                                    <td width="20%">Amount</td>
                                    <td width="40%">Description</td>
                                    <td width="20%">Attachment</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($job->payments as $pay)
                                <tr>
                                <td>{{ (strlen($pay->payed_on) > 0) ? date('m/d/Y', strtotime($pay->payed_on)): 'N/A' }}</td>
                                <td>{{ number_format($pay->amount,2) }}</td>
                                <td>{{ $pay->description }}</td>
                                    <td>
                                        @if($pay->attached_file)
                                        <a href="{{ route('jobpayments.showattachment',[$pay->id])}}"><i class="fa fa-download"></i></a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="width:20%"><strong>Balance:</strong></td>
                    <td style="width:80%">${{number_format($job->balance(), 2, '.', ',')}}</td>
                </tr>
            </table>
        </div>
        <div class="col-xs-12" style="margin-top: 20px">
            <h4>Notices:</h4>
            <table style="width:100%">
                <tr>
                    <td style="width:20%"><strong>NTO Filed Timely? </strong></td>
                    <td style="width:80%">
                        <p>{{$nto_filled_timly}}</p>
                        @foreach($work_orders as $work)
                            
                                <div class="" role="tab" id="heading{{$work->id}}">
                                    <div class="panel-title ">
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$work->id}}" aria-expanded="true" aria-controls="collapse{{$work->id}}">
                                            <h4>
                                                <i class="fa fa-plus-square"></i> #{{$work->number}} - {{$wo_types[$work->type]}}
                                            </h4>
                                        </a>
                                    </div>
                                    <div id="collapse{{$work->id}}" class="panel-collapse collapse out" role="tabpanel" aria-labelledby="heading{{$work->id}}">
                                        <div class="row">
                                            <div class="col-xs-12">
                                                @foreach($work->recipients as $recipient)
                                                <li>
                                                    {{$parties_type[$recipient->party_type]}} - {{$recipient->firm_name}} {{$recipient->address}} - <input disabled type="checkbox" {{$recipient->party->landowner_lien_prohibition ? 'checked':''}}> - {{$mailing_types[$recipient->mailing_type]}} @if($recipient->barcode) <a href="https://tools.usps.com/go/TrackConfirmAction?tRef=fullpage&tLc=2&text28777=&tLabels={{$recipient->barcode}}%2C" target="_blank">{{$recipient->barcode}}</a> @endif
                                                    @foreach($work->attachments as $attach)
                                                        @if(isset($attach->recipient) && $attach->recipient->id == $recipient->id)
                                                        - <a href="{{ route('workorders.showattachment',[$work->id,$attach->id])}}"><i class="fa fa-download"></i></a> &nbsp;&nbsp;
                                                        @endif    
                                                    @endforeach
                                                </li>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <td style="width:20%"><strong>Attachments: </strong></td>
                    <td style="width:80%">
                        @foreach($job->attachments->where('type', '!=', 'generated') as $attach)
                        <a href="{{ route('jobs.showattachment',[$job->id,$attach->id])}}"><i class="fa fa-download"></i> {{ $attach->original_name}}</a> &nbsp;&nbsp;&nbsp;
                        @endforeach
                        @foreach($work_orders as $work)
                        @foreach($work->attachments->where('type', '!=', 'generated') as $attach)
                        <a href="{{ route('workorders.showattachment',[$work->id,$attach->id])}}"><i class="fa fa-download"></i> {{ $attach->original_name}}</a> &nbsp;&nbsp;&nbsp;
                        @endforeach
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <td style="width:20%"><strong>NOC: </strong></td>
                    <td style="width:80%">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>NOC#</th>
                                    <th>NOC Recording Date</th>
                                    <th>NOC Notes</th>
                                    <th>Copy of NOC</th>
                                    <th>Expiration Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($job->nocs()->get() as $noc)
                                <tr>
                                    <td style="word-break: break-all;max-width: 200px;"> {{$noc->noc_number}}</td>
                                    <td> {{date('m/d/Y', strtotime($noc->recorded_at))}}</td>
                                    <td style="word-break: break-all;max-width: 300px;"> {{$noc->noc_notes}}</td>
                                    <td> 
                                        @if($noc->copy_noc)
                                        <a href="{{route('jobnocs.download', [$job->id, $noc->id])}}"><i class="fa fa-download"></i></a>
                                        @endif
                                    </td>
                                    <td> {{date('m/d/Y', strtotime($noc->expired_at))}}</td>
                                    <td>
                                        @if($noc->noc_number==$job->noc_number)
                                        <span class="text-primary">Current</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
   
@endsection

@section('scripts')
<script src="{{ asset('/vendor/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/payeezy/js/payeezy_us_v5.1.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/bootstrap-filestyle/js/bootstrap-filestyle.min.js') }}" type="text/javascript"></script>
<script>
$.fn.select2.defaults.set("theme", "bootstrap");
$('.btn-print').click(function(){
    $("#print_contents").show();
    window.print();
});
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
    var anchor = window.location.hash;
    if (anchor.length >0 ) {
        $(".collapse").collapse('hide');
        $(anchor).collapse('show'); 
    }
    $('.collapse').on('shown.bs.collapse', function(){
        $(this).parent().find("i.fa-plus-square").removeClass("fa-plus-square").addClass("fa-minus-square");
    }).on('hidden.bs.collapse', function(){
        $(this).parent().find(".fa-minus-square").removeClass("fa-minus-square").addClass("fa-plus-square");
    });
});
</script>
    
@endsection