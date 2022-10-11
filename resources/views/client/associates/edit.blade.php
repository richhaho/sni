@extends('client.layouts.app')

@section('navigation')
    @include('client.navigation')
@endsection

@section('content')
    {!! Form::open(['route' => ['client.associates.update',$entity->id,$associate->id], 'method'=> 'PUT','autocomplete' => 'off']) !!}
     {{ Form::hidden('redirects_to', Session::get('backUrl'). "#collapse" . $entity->id) }}
        @if($back <> '')
            {{ Form::hidden('back',$back) }}
            {{ Form::hidden('job',$job) }}
            {{ Form::hidden('parties',$parties) }}
            {{ Form::hidden('workorder',$workorder) }}
        @endif
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">Editing {{$associate->full_name}}
                    <div class="pull-right">
                        <button class="btn btn-success " type="submit"> <i class="fa fa-floppy-o"></i> Save</button>
                        <a class="btn btn-danger " href="{{ route('client.contacts.index')}}"><i class="fa fa-times-circle"></i> Cancel</a> &nbsp;&nbsp;
                    </div>
                </h1>       
            </div>
            </div>
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h5 style="color: #46a1d8">
                    WARNING: You are modifying a contact record.  Any changes you make here will be reflected in both your contact list and ALL jobs this contact is associated with.
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
                <div class="row">
                  
                    
                    <div class="col-xs-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Contact Info
                            </div>
                            <div class="panel-body">
                                                <div class="row">
                                <div class="col-xs-6 form-group">
                                    <label>First Name:</label>
                                    <input name="first_name"  value="{{ old("first_name", $associate->first_name)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="" maxlength="50">
                                </div>
                                
                                <div class="col-xs-6 form-group">
                                    <label>Last Name:</label>
                                    <input name="last_name" value="{{ old("last_name", $associate->last_name)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="" maxlength="50">
                                </div>
                                </div>
                                
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <label>Email:</label>
                                    <input name="email" value="{{ old("email",$associate->email)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="" maxlength="100">
                                </div>
                                </div>
                                
                                <div class="row">
                                <div class="col-md-12 col-lg-4 form-group">
                                    <label>Phone:</label>
                                    <input name="phone" value="{{ old("phone", $associate->phone)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="" maxlength="20">
                                </div>
                              
                                <div class="col-md-12 col-lg-4 form-group">
                                    <label>Mobile:</label>
                                    <input name="mobile" value="{{ old("mobile", $associate->mobile)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="" maxlength="20">
                                </div>
                                <div class="col-md-12 col-lg-4 form-group">
                                    <label>Fax:</label>
                                    <input name="fax" value="{{ old("fax", $associate->fax)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="" maxlength="20">
                                </div>
                                </div>
                                
                                <div class="row">
                                <!--<div class="col-md-6 form-group">
                                    <label>Gender:</label>
                                    {!!  Form::select('gender',$gender,old("gender", $associate->gender), ['class' => 'form-control']) !!}
                                </div>-->
                                @if($associate->primary == 0 && count($entity->contacts->where('primary',2))==0 )   
                                <div class="col-md-6 form-group">
                                    <label>&nbsp;</label>
                                    <div class="checkbox checkbox-slider--b-flat">
                                        <label>
                                        <input name="primary_contact" type="checkbox" ><span>Primary Contact</span>
                                        </label>
                                    </div>
                                    </div>    
                                 @endif
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
                                    <input name="address_1" value="{{ old("address_1", $associate->address_1)}}" placeholder="Street and number" class="form-control" data-toggle="tooltip" data-placement="top" title="" maxlength="150">
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <input name="address_2" value="{{ old("address_2", $associate->address_2)}}" placeholder="Apartment, suite, unit, building, floor, etc." class="form-control" data-toggle="tooltip" data-placement="top" title="" maxlength="150">
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-md-12 col-lg-6 form-group">
                                    <label>Country:</label>
                                    <input id="countries" value="{{ old("country",$associate->country)}}" name="country" class="form-control typeahead" data-toggle="tooltip" data-placement="top" title="" autocomplete="off" maxlength="50">
                                </div>
                             
                                <div class="col-md-12 col-lg-6 form-group">
                                    <label>State / Province / Region:</label>
                                    <input id="states" value="{{ old("state",$associate->state)}}" name="state" class="form-control typeahead" data-toggle="tooltip" data-placement="top" title=""  autocomplete="off" maxlength="50">
                                </div>
                                </div>
                                
                                <div class="row">
                                <div class="col-md-12 col-lg-6 form-group">
                                    <label>City:</label>
                                    <input name="city"  value="{{ old("city", $associate->city)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="" maxlength="150">
                                </div>
                             
                                <div class="col-md-12 col-lg-6 form-group">
                                    <label>Zip code:</label>
                                    <input name="zip"  value="{{ old("zip", $associate->zip)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="" maxlength="50">
                                </div>
                                </div>
                                
                            </div>
                        </div>
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
<script>
$('.btn-success').click(function(){
    $('.btn-success').addClass("disabled");
    $('.btn-success').css('pointer-events','none');
});  
    
    
$(function () {
  $('[data-toggle="tooltip"]').tooltip()

 
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