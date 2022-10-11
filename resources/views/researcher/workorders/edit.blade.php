@extends('researcher.layouts.app')

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
<style>
    .tab-pane {
        margin-top: 10px;
    }
</style>

@endsection

@section('navigation')
    @include('researcher.navigation')
@endsection

@section('content')
    
        
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">Edit Work Order {{ $work->number }}<br>
                    <small style="font-size:12px;"> Created: {{ $work->created_at->format('m-d-Y h:i:s a')}}</small>
                    <div class="pull-right">
                        
                        <a class="btn btn-danger " href="{{ route('workorders.index')}}"><i class="fa fa-times-circle"></i> Exit</a> &nbsp;&nbsp;
                    </div>
                </h1>       
            </div>
            </div>
        </div>
            <div id="page-wrapper">
            
            <div class="container-fluid">
                <div class="btn-group">
                     
                  
                  <a class="btn btn-default " href="{{ route('workorders.createinvoice',$work->id)}}"><i class="fa fa-file-text-o"></i> Create Invoice</a>
                        
                        @if(in_array($work->type,$available_notices))
                            @if(count($work->attachments->where('type','generated')) > 0)
                            <a class="btn btn-default disabled" href=""><i class="fa  fa-file-pdf-o"></i> Create PDF</a>
                            <a class="btn btn-default" href="{{ route('workorders.deletedocument',$work->id)}}"><i class="fa  fa-trash-o"></i> Delete PDF</a>
                            <a class="btn btn-default" href="{{ route('workorders.show',$work->id)}}"><i class="fa  fa-eye"></i> View PDF</a>
                           
                            @else
                           <a class="btn btn-default " href="{{ route('workorders.document',$work->id)}}"><i class="fa  fa-file-pdf-o"></i> Create PDF</a>
                            <a class="btn btn-default disabled" href=""><i class="fa  fa-trash-o"></i> Delete PDF</a>
                            <a class="btn btn-default disabled" href=""><i class="fa  fa-eye"></i> View PDF</a>
                            
                            @endif

                        
                        @endif
                  
                </div>
                <div>&nbsp;</div>
                @if (count($errors) > 0)
                    <div class="alert alert-danger">            
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
  
              
                 <div class="row">
                   
                 @if (Session::has('message'))
                     <div class="col-xs-12 message-box">
                     <div class="alert alert-info">{{ Session::get('message') }}</div>
                     </div>
                 @endif
                   @php 
                        $job = $work->job
                    @endphp
                  <div class="col-xs-12">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist" id="wo_tabs" data-tabs="tabs">
                          <li role="presentation" class="active"><a href="#workorderinfo" aria-controls="profile" role="tab" data-toggle="tab">Work Order Info</a></li> 
                          <li role="presentation"><a href="#job" aria-controls="profile" role="tab" data-toggle="tab">Job Info</a></li>
                          <li role="presentation"><a href="#parties" aria-controls="messages" role="tab" data-toggle="tab">Job Parties</a></li>
                          <li role="presentation"><a href="#notes" aria-controls="messages" role="tab" data-toggle="tab">Notes</a></li>
                          <li role="presentation"><a href="#attachments" aria-controls="attachments" role="tab" data-toggle="tab">Attachments</a></li>
                          <li role="presentation"><a href="#invoices" aria-controls="invoices" role="tab" data-toggle="tab">Invoices</a></li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane  active" id="workorderinfo">
                              @include('researcher.workorders.components.orderinfo-tab')
                            </div>
                            <div role="tabpanel" class="tab-pane" id="attachments">
                              @include('researcher.workorders.components.attachments-tab')
                            </div>
                            <div role="tabpanel" class="tab-pane " id="job">
                              @include('researcher.workorders.components.jobinfo-tab')
                            </div>
                            <div role="tabpanel" class="tab-pane" id="parties">
                                @include('researcher.workorders.components.jobparties-tab')
                            </div>
                            <div role="tabpanel" class="tab-pane" id="notes">
                               @include('researcher.notes.index', ['notes' => $work->notes,'e_name' => 'workorders','e_id' => $work->id])
                            </div>
                            <div role="tabpanel" class="tab-pane" id="invoices">
                                @include('researcher.workorders.components.invoices-tab')
                            </div>
                        </div>


                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                   
                        </div>
                  </div>
                
            </div>
            <!-- /.container-fluid -->
            
        </div>
    
@endsection

@section('scripts')
<script src="{{ asset('/vendor/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/bootstrap-filestyle/js/bootstrap-filestyle.min.js') }}" type="text/javascript"></script>
<script>
$.fn.select2.defaults.set("theme", "bootstrap");

$(function () {
    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
    $('[data-toggle="tooltip"]').tooltip()
    $('.date-picker').datepicker();
    $(":file").filestyle();

   
    $('#wo_tabs a').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
      })
      
    var hash = window.location.hash;
    //console.log(hash);
    if (hash.length > 0 ) {
       
            $('#wo_tabs a[href="' + hash + '"]').tab('show')
       
         $('#page-wrapper').scrollTop($(hash).offset().top);
    }
    
    $('.collapse').on('shown.bs.collapse', function(){
        $(this).prev('tr').find("i.fa-plus-circle:last").removeClass("fa-plus-circle").addClass("fa-minus-circle");
    }).on('hidden.bs.collapse', function(){
        $(this).prev('tr').find(".fa-minus-circle:last").removeClass("fa-minus-circle").addClass("fa-plus-circle");
    });
    
});
</script>
    
@endsection