@extends('admin.layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('/vendor/bootstrap-multiselect/bootstrap-multiselect.css') }}">
@endsection
@section('navigation')
    @include('admin.navigation')
@endsection

@section('content')
    {!! Form::open(['route' => ['hotcontacts.store'],'autocomplete' => 'off']) !!}
        {{ Form::hidden('redirects_to', Session::get('backUrl')) }}
        {!! Form::hidden('client_id',0) !!}}
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">New HotList Contact 
                    <div class="pull-right">
                        <button class="btn btn-success btn-save" type="submit"> <i class="fa fa-floppy-o"></i> Save</button>
                        <a class="btn btn-danger " href="{{  Session::get('backUrl')}}"><i class="fa fa-times-circle"></i> Cancel</a> &nbsp;&nbsp;
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
                                    <div class="col-md-12 form-group">
                                        <label>Firm Name:</label>
                                        <input name="firm_name"  value="{{ old("firm_name")}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-6 form-group">
                                        <label>First Name:</label>
                                        <input name="first_name"  value="{{ old("first_name")}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                    </div>
                                    
                                    <div class="col-xs-6 form-group">
                                        <label>Last Name:</label>
                                        <input name="last_name" value="{{ old("last_name")}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-xs-12 form-group">
                                        <label>Email:</label>
                                        <input name="email" value="{{ old("email")}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12 col-lg-4 form-group">
                                        <label>Phone:</label>
                                        <input name="phone" value="{{ old("phone")}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                    </div>
                                
                                    <div class="col-md-12 col-lg-4 form-group">
                                        <label>Mobile:</label>
                                        <input name="mobile" value="{{ old("mobile")}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                    </div>
                                    <div class="col-md-12 col-lg-4 form-group">
                                        <label>Fax:</label>
                                        <input name="fax" value="{{ old("fax")}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-4 form-group">
                                        <label>Source Date:</label>
                                        <input name="source_date" value="{{old('source_date') ? date('m/d/Y', strtotime(old('source_date'))):''}}" data-date-autoclose="true" class="form-control date-picker" data-date-format = "mm/dd/yyyy" data-toggle="tooltip" data-placement="top" title="">
                                    </div>
                                    <div class="col-md-12 col-lg-4 form-group">
                                        <label>Source:</label>
                                        <br>
                                        <?php $source = old('source') ? explode(',', old('source')) : []; ?>
                                        {!! Form::select('source_select',['TR' => 'TR', 'NOC' => 'NOC', 'CL' => 'CL', 'OTHR' => 'OTHR', 'SBZ' => 'SBZ'], $source ,['class' => 'multi-select-source form-control', 'multiple'=>'multiple']) !!}
                                        <input type="hidden" name="source" class="source-content" value="{{ old('source')}}">
                                    </div>
                      
                                    <div class="col-md-12 col-lg-4 form-group">
                                        <label>Default Type:</label>
                                        {!!  Form::select('latest_type',$types,old("latest_type"), ['class' => 'form-control']) !!}
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label>&nbsp;</label>
                                        <div class="checkbox checkbox-slider--b-flat">
                                            <label>
                                            <input name="sni_client" type="checkbox"><span>Is SNI Client?</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-4 form-group">
                                        <label>&nbsp;</label>
                                        <div class="checkbox checkbox-slider--b-flat">
                                            <label>
                                            <input name="use_on_client" type="checkbox"><span>Usable By Client</span>
                                            </label>
                                        </div>
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
                                    <input name="address_1" value="{{ old("address_1")}}" placeholder="Street and number" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <input name="address_2" value="{{ old("address_2")}}" placeholder="Apartment, suite, unit, building, floor, etc." class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-md-12 col-lg-6 form-group">
                                    <label>Country:</label>
                                    <input id="countries" value="{{ old("country",'USA')}}" name="country" class="form-control typeahead" data-toggle="tooltip" data-placement="top" title="" autocomplete="off">
                                </div>
                             
                                <div class="col-md-12 col-lg-6 form-group">
                                    <label>State / Province / Region:</label>
                                    <input id="states" value="{{ old("state","FL")}}" name="state" class="form-control typeahead" data-toggle="tooltip" data-placement="top" title=""  autocomplete="off">
                                </div>
                                </div>
                                
                                <div class="row">
                                <div class="col-md-12 col-lg-6 form-group">
                                    <label>City:</label>
                                    <input name="city"  value="{{ old("city")}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                             
                                <div class="col-md-12 col-lg-6 form-group">
                                    <label>Zip code:</label>
                                    <input name="zip"  value="{{ old("zip")}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
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
<script src="{{ asset('/vendor/bootstrap-multiselect/bootstrap-multiselect.js') }}"></script>
<script>
 
$('.btn-save').click(function(){
    $('.btn-save').addClass("disabled");
    $('.btn-save').css('pointer-events','none');
});      
    
$(function () {
    $('.multi-select-source').multiselect({
        includeSelectAllOption: true,
        buttonWidth:'100%',
    });
    $('.multi-select-source').change(function() {
        $('.source-content').val($(this).val().join(','))
    });
    
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