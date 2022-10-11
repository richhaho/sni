@extends('admin.layouts.app')

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">

<style>
  
</style>

@endsection

@section('navigation')
    @include('admin.navigation')
@endsection

@section('content')
    
         
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">{{$job->name}}
                    <div class="pull-right">
                        <a class="btn btn-success " href="{{ route('jobs.edit',$job->id)}}"> <i class="fa fa-pencil"></i> Edit</a>
                        <a class="btn btn-danger " href="{{Session::get('backUrl')}}"><i class="fa fa-chevron-left"></i> Back</a> &nbsp;&nbsp;
                    </div>
                </h1>       
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
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Job Info
                            </div>
                            <div class="panel-body">
                                <table class="table job-information">
                                    <tbody>
                                        <tr>
                                            <td style="min-width: 200px">Client name:</td>
                                            <td>{{ $job->client->company_name}}</td>
                                        </tr>
                                        <tr>
                                            <td>Job name:</td>
                                            <td>{{ $job->name}}</td>
                                        </tr>    
                                        <tr>
                                            <td>Job Address</td>
                                            <td>{!! $job->full_address !!}</td>
                                        </tr>  
                                        <tr>
                                            <td>Date Started:</td>
                                            <td>{!! date('m/d/Y', strtotime($job->started_at)) !!}</td>
                                        </tr>  
                                        <tr>
                                            <td>Folio Number:</td>
                                            <td>{!! $job->folio_number !!}</td>
                                        </tr>  
                                        <tr>
                                            <td>Contract Amount:</td>
                                            <td>{!! number_format($job->contract_number,2) !!}</td>
                                        </tr>  
                                        <tr>
                                            <td>Interest Rate:</td>
                                            <td>{!! number_format($job->interest_rate,2) !!}</td>
                                        </tr>  
                                        <tr>
                                            <td>Default Materials:</td>
                                            <td>{!! str_replace(chr(10),'<br>',$job->default_materials) !!}</td>
                                        </tr>  
                                        <tr>
                                            <td>Legal Descriptions:</td>
                                            <td>{!! str_replace(chr(10),'<br>',$job->legal_description) !!}</td>
                                        </tr>  
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xs-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Attachments
                            </div>
                            <div class="panel-body">
                             @foreach ($job->attachments as $attach)
                                @if($loop->first)
                                   <div class="row">
                               @endif
                               <div class="col-md-3 text-center">
                                    <div class="thumbnail">
                                       <img class="img-responsive" src="{{ route('jobs.showthumbnail',[$job->id,$attach->id])}}" alt="{{ $attach->type }}">
                                       <div class="caption">
                                         <h5 style="word-wrap: break-word;">{{ $attach->original_name}}</h5>
                                         <p>{{ $attach->description }}</p>
                                         <p>
                                          
                                         </p>
                                       </div>
                                     </div>
                               </div>
                                @if($loop->iteration % 4 == 0 && $loop->last)
                                   </div>
                                @else
                                   @if($loop->iteration % 4 == 0)
                                       </div>
                                       <div class="row">
                                   @else
                                       @if($loop->last)
                                          </div>
                                       @endif
                                   @endif
                               @endif
                           @endforeach   
                                
                            </div>
                        </div>
                    </div>
                    <!-- /.col-lg-12 -->
                <div class="row">
                    <div class="col-xs-12">
                       
                            <div class="col-xs-12">
                        <div class="panel panel-default">
                        <div class="panel-heading">
                            Job Parties
                        </div>
                        <div class="panel-body">
                        @foreach ($parties_type as $type_key => $type_name)
                            @if($job->parties()->ofType($type_key)->count() > 0)
                                @if($loop->first )
                                    <div class="row">
                                @endif
                                <div class="col-lg-6">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">{{ $type_name }}</h4>
                                        </div>
                                        <div class="panel-body">
                                           @foreach($job->parties()->ofType($type_key)->get() as $jobparty) 

                                                @include('admin.jobs.components.contacticon')

                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @if($loop->iteration % 2 == 0 && $loop->last)
                                   </div>
                                @else
                                   @if($loop->iteration % 2 == 0)
                                       </div>
                                       <div class="row">
                                   @else
                                       @if($loop->last)
                                          </div>
                                       @endif
                                   @endif
                               @endif
                               @endif
                            @endforeach
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
  $('#client_id').select2();
  $('.date-picker').datepicker();
   $(":file").filestyle();
   var hash = window.location.hash;
   if (hash.length > 0 ) {
        $('#page-wrapper').scrollTop($(hash).offset().top);
   }
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
        
    
  
})
</script>
    
@endsection