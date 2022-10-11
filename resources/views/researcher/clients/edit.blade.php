@extends('researcher.layouts.app')

@section('navigation')
    @include('researcher.navigation')
@endsection

@section('content')
    {!! Form::open(['route' => ['clients.update', $client->id], 'method'=> 'PUT','autocomplete' => 'off']) !!}
        {!! Form::hidden('status',4)!!}
        {{ Form::hidden('redirects_to', URL::previous()) }}
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">Edit Client
                    <div class="pull-right">
                        <button class="btn btn-success " type="submit"> <i class="fa fa-floppy-o"></i> Save</button>
                        <a class="btn btn-danger " href="{{ route('clients.index') }}"><i class="fa fa-times-circle"></i> Exit</a> &nbsp;&nbsp;
                    </div>
                </h1>       
            </div>
            </div>
        </div>
            <div id="page-wrapper">
            
            <div class="container-fluid">
                
                
                <div class="btn-group">
                    @if($client->status == 3)
                        <a class="btn btn-default" href="{{ route('clients.enable',$client->id)  }}"><i class="fa  fa-check"></i> Enable</a>
                    @else
                        <a class="btn btn-default" href="{{ route('clients.disable',$client->id)  }}"><i class="fa  fa-ban"></i> Disable</a>
                    @endif
     
                    <a class="btn btn-default" href="{{ route('contacts.index',$client->id)  }}"><i class="fa fa-address-card-o"></i> Contacts</a>
                    <a class="btn btn-default" href="{{ route('clientusers.index',$client->id)  }}"><i class="fa fa-user"></i> Users</a>
                    <a class="btn btn-default" href="{{ route('jobs.setfilter') .'?resetfilter=true&client_filter='.$client->id  }}"><i class="fa fa-eye"></i> Jobs</a>
                    <a class="btn btn-default" href="{{ route('workorders.setfilter') . '?resetfilter=true&client_filter=' . $client->id }}"><i class="fa fa-eye"></i> Work Orders</a>
                    <a class="btn btn-default" href="{{ route('client.templates.index',$client->id)  }}"><i class="fa  fa-bar-chart-o fa-fw"></i> Billing Templates</a>
                   
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
                  
                    
                    <div class="col-xs-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Contact Info
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <label>Company Name:</label>
                                    <input name="company_name"  value="{{ old("company_name",$client->company_name)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-xs-4 form-group">
                                    <label>Title:</label>
                                    <input name="title" id="title" value="{{ old("title",$client->title)}}" class="form-control typeahead" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                
                                
                                <div class="col-xs-8 form-group">
                                    <label>First Name:</label>
                                    <input name="first_name"  value="{{ old("first_name",$client->first_name)}}" class="noucase form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <label>Last Name:</label>
                                    <input name="last_name" value="{{ old("last_name",$client->last_name)}}" class="noucase form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <label>Email:</label>
                                    <input name="old_email" value="{{ $client->email}}" type="hidden">
                                    <input name="email" value="{{ old("email",$client->email)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                </div>
                                
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <label>Gender:</label>
                                    {!!  Form::select('gender',$gender,old("gender",$client->gender), ['class' => 'form-control']) !!}
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-md-12 col-lg-4 form-group">
                                    <label>Phone:</label>
                                    <input name="phone" value="{{ old("phone",$client->phone)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                              
                                <div class="col-md-12 col-lg-4 form-group">
                                    <label>Mobile:</label>
                                    <input name="mobile" value="{{ old("mobile",$client->mobile)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                <div class="col-md-12 col-lg-4 form-group">
                                    <label>Fax:</label>
                                    <input name="fax" value="{{ old("fax",$client->fax)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
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
                                    <input id="countries" value="{{ old("country",$client->country)}}" name="country" class="form-control typeahead" data-toggle="tooltip" data-placement="top" title="" autocomplete="off">
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
                                    <input id="counties" value="{{ old("county",$client->county)}}" name="county" class="form-control typeahead" data-toggle="tooltip" data-placement="top" title="" autocomplete="off">
                                </div>
                                
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
                <div class="row">
                    <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Settings
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <label>Parent Client:</label>
                                    {!!  Form::select('parent_client_id',$clients,old("parent_client_id",$client->parent_client_id), ['class' => 'form-control']) !!}
                                </div>
                                </div>
                                <div class='row'>
                                    <div class="col-xs-12 form-group">
                                    <div class="checkbox checkbox-slider--b-flat">
                                        <label>
                                           @if($client->client_user_id == 0)
                                                <input name="create_login" type="checkbox"><span>Create Login</span>
                                           @else
                                                @if($client->admin_user)
                                                @if($client->admin_user->status == 0)
                                                    <input name="enable_login " type="checkbox"><span>Enable Login</span>
                                                @else
                                                    <input name="disable_login" type="checkbox"><span> Disable Login</span>
                                                @endif 
                                                @endif
                                           @endif
                                          
                                        </label>
                                    </div>
                                    </div>
                                </div>
                                <div class="row">
                                <div class="col-md-6 col-lg-3 form-group">
                                    <label>Printing by:</label>
                                    {!!  Form::select('print_method',$print_method,old("print_method",$client->print_method), ['class' => 'form-control']) !!}
                                </div>
                               
                                <div class="col-md-6 col-lg-3 form-group">
                                    <label>When it will be billed:</label>
                                    {!!  Form::select('billing_type',$billing_type,old("billing_type",$client->billing_type), ['class' => 'form-control']) !!}
                                </div>
                                
                                <div class="col-md-6 col-lg-3 form-group">
                                    <label>Type of Certified mail:</label>
                                    {!!  Form::select('send_certified',$send_certified,old("send_certified",$client->send_certified), ['class' => 'form-control']) !!}
                                </div>
                               
                                <div class="col-md-6 col-lg-3 form-group">
                                    <label>Interest Rate:</label>
                                    {!!  Form::number('interest_rate',old("interest_rate",$client->interest_rate), ['class' => 'form-control', 'min'=>'0', 'max'=>'100', 'step'=>'0.01']) !!}
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-md-12 form-group">
                                    <label>Default Materials:</label>
                                    {!!  Form::textarea('default_materials',old("default_materials",$client->default_materials), ['class' => 'form-control']) !!}
                                </div>
                                </div>
                                 <div class="row">
                                <div class="col-md-6 form-group">
                                    
                                    <label>Current Signature:</label> 
                                    <div>
                                        @if(strlen($client->signature)>0)
                                        <img id="currentsignature" src="{{$client->signature}}">
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 form-group">
                                    {{ Form::hidden('signature',$client->signature)}}
                                    <label>Signature:</label> <a class="btn btn-danger btn-xs" id="clear-canvas"><i class="fa fa-eraser"></i></a>
                                    <div id="signature-panel" class="signature-panel" data-name="signature" data-height="200" data-width="450"></div>
                                </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
               
            </div>
            <!-- /.container-fluid -->
            
        </div>
    {!! Form::close() !!}
@endsection

@section('scripts')
<script src="{{ asset('vendor/jqsignature/js/jq-signature.min.js') }}" type="text/javascript"></script>
<script>
 
    
    
$(function () {
  $('[data-toggle="tooltip"]').tooltip()

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
      name: 'title',
      source: titles
    });
})
</script>
    
@endsection