@extends('researcher.layouts.app')

@section('navigation')
    @include('researcher.navigation')
@endsection

@section('content')
        {!! Form::open(['route' => ['contacts.update',$entity->client_id,$entity->id], 'method'=> 'PUT','autocomplete' => 'off']) !!}
        
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">Edit {{$entity->firm_name}}
                    <div class="pull-right">
                        <button class="btn btn-success btn-save" type="submit"> <i class="fa fa-floppy-o"></i> Save</button>
                        <a class="btn btn-danger " href="{{ route('contacts.index',$entity->client_id)}}"><i class="fa fa-times-circle"></i> Cancel</a> &nbsp;&nbsp;
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
                  
                    
                    <div class="col-md-6 col-md-offset-3">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Firm Info
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                <div class="col-md-12 form-group">
                                    <label>Firm Name:</label>
                                    <input name="firm_name"  value="{{ old("firm_name",$entity->firm_name)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
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
$('.btn-save').click(function(){
    $('.btn-save').addClass("disabled");
});
    
    
$(function () {
  $('[data-toggle="tooltip"]').tooltip();
    
  
})
</script>
    
@endsection