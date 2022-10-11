@extends('client.layouts.app')

@section('navigation')
    @include('client.navigation')
@endsection

@section('content')
        {!! Form::open(['route' => ['client.contacts.update',$entity->id], 'method'=> 'PUT','autocomplete' => 'off']) !!}
        {{ Form::hidden('redirects_to', Session::get('backUrl')) }}
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">Edit {{$entity->firm_name}}
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
                  
                    
                    <div class="col-md-6 col-md-offset-3">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Firm Info
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                <div class="col-md-12 form-group">
                                    <label>Firm Name:</label>
                                    <input name="firm_name"  value="{{ old("firm_name",$entity->firm_name)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="" maxlength="200">
                                </div>
                                
                                </div>
                               
                                <div class="row">
                                
                                <div class="col-md-12 form-group">
                                    <label>Default Type:</label>
                                    {!!  Form::select('latest_type',$types,old("latest_type",$entity->latest_type), ['class' => 'form-control']) !!}
                                </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                
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
  $('[data-toggle="tooltip"]').tooltip();
    
  
})
</script>
    
@endsection