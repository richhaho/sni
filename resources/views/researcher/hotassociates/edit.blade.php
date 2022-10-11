@extends('researcher.layouts.app')

@section('navigation')
    @include('researcher.navigation')
@endsection

@section('content')
    {!! Form::open(['route' => ['hotassociates.update',$entity->id,$associate->id], 'method'=> 'PUT','autocomplete' => 'off']) !!}
     {{ Form::hidden('redirects_to', Session::get('backUrl'). "#collapse" . $entity->id) }}
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">Editing {{$associate->full_name}}
                    <div class="pull-right">
                        <button class="btn btn-success btn-save" type="submit"> <i class="fa fa-floppy-o"></i> Save</button>
                        <a class="btn btn-danger " href="{{  str_contains(Session::get('backUrl'),'#collapse')? Session::get('backUrl') : Session::get('backUrl'). "#collapse" . $entity->id}}"><i class="fa fa-times-circle"></i> Cancel</a> &nbsp;&nbsp;
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
                  
                    
                    <div class="col-xs-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Contact Info
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                <div class="col-xs-6 form-group">
                                    <label>First Name:</label>
                                    <input name="first_name"  value="{{ old("first_name", $associate->first_name)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                
                                <div class="col-xs-6 form-group">
                                    <label>Last Name:</label>
                                    <input name="last_name" value="{{ old("last_name", $associate->last_name)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                </div>
                                
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <label>Email:</label>
                                    <input name="email" value="{{ old("email",$associate->email)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                </div>
                                
                                <div class="row">
                                <div class="col-md-12 col-lg-4 form-group">
                                    <label>Phone:</label>
                                    <input name="phone" value="{{ old("phone", $associate->phone)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                              
                                <div class="col-md-12 col-lg-4 form-group">
                                    <label>Mobile:</label>
                                    <input name="mobile" value="{{ old("mobile", $associate->mobile)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                <div class="col-md-12 col-lg-4 form-group">
                                    <label>Fax:</label>
                                    <input name="fax" value="{{ old("fax", $associate->fax)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label>&nbsp;</label>
                                        <div class="checkbox checkbox-slider--b-flat">
                                            <label>
                                            <input name="use_on_client" type="checkbox" {{ ($associate->use_on_client == 1) ? 'checked' : ''}}><span>Usable By Client</span>
                                            </label>
                                        </div>
                                        </div>  
                                <div class="col-md-3 form-group">
                                    <label>&nbsp;</label>
                                    <div class="checkbox checkbox-slider--b-flat">
                                        <label>
                                        <input name="sni_client" type="checkbox" {{ ($associate->sni_client == 1) ? 'checked' : ''}}><span>Is SNI Client?</span>
                                        </label>
                                    </div>
                                    </div>        
                                    
                                    
                                 @if($associate->primary == 0)  
                                <div class="col-md-3 form-group">
                                    <label>&nbsp;</label>
                                    <div class="checkbox checkbox-slider--b-flat">
                                        <label>
                                        <input name="primary_contact" type="checkbox" ><span>Primary Contact</span>
                                        </label>
                                    </div>
                                    </div>    
                                 @endif
                                  @if(count($associate->links) > 0)  
                                    <div class="col-md-3 form-group">
                                    <label>&nbsp;</label>
                                    <div class="checkbox checkbox-slider--b-flat">
                                        <label>
                                        <input name="update_all" type="checkbox" ><span>Update Linked contacts</span>
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
                                    <input name="address_1" value="{{ old("address_1", $associate->address_1)}}" placeholder="Street and number" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <input name="address_2" value="{{ old("address_2", $associate->address_2)}}" placeholder="Apartment, suite, unit, building, floor, etc." class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-md-12 col-lg-6 form-group">
                                    <label>Country:</label>
                                    <input id="countries" value="{{ old("country",$associate->country)}}" name="country" class="form-control typeahead" data-toggle="tooltip" data-placement="top" title="" autocomplete="off">
                                </div>
                             
                                <div class="col-md-12 col-lg-6 form-group">
                                    <label>State / Province / Region:</label>
                                    <input id="states" value="{{ old("state",$associate->state)}}" name="state" class="form-control typeahead" data-toggle="tooltip" data-placement="top" title=""  autocomplete="off">
                                </div>
                                </div>
                                
                                <div class="row">
                                <div class="col-md-12 col-lg-6 form-group">
                                    <label>City:</label>
                                    <input name="city"  value="{{ old("city", $associate->city)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                             
                                <div class="col-md-12 col-lg-6 form-group">
                                    <label>Zip code:</label>
                                    <input name="zip"  value="{{ old("zip", $associate->zip)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
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
$('.btn-save').click(function(){
    $('.btn-save').addClass("disabled");
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