@extends('researcher.layouts.app')

@section('navigation')
    @include('researcher.navigation')
@endsection

@section('content')
    {!! Form::open(['route' => ['mailingtype.update'], 'method'=> 'POST','autocomplete' => 'off']) !!}
        {
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">Mailing Type Definitions
                    <div class="pull-right">
                        <button class="btn btn-success " type="submit"> <i class="fa fa-floppy-o"></i> Save</button>
                        
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
                @if (Session::has('message'))
                    <div class="col-xs-12 message-box">
                    <div class="alert alert-info">{{ Session::get('message') }}</div>
                    </div>
                @endif
                <div class="row">
                  
                    
                    <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Definitions
                            </div>
                           <table class="table">
                               <thead>
                                   <tr>
                                       <th>Name</th>
                                       <th>Default Postage</th>
                                       <th>Default Fee</th>
                                       <th>STC Code</th>
                                   </tr>
                               </thead>
                               <tbody>
                                   @foreach($definitions as $definition)
                                   <tr>
                                   <td>{{ $mailing_types[$definition->type] }}
                                   <input type="hidden" name="type[]" value="{{$definition->type}}">
                                    </td>
                                       <td><input type="number" name="postage[{{$definition->type}}]" value="{{$definition->postage}}" step="0.01" min="0"></td>
                                       <td><input type="number" name="fee[{{$definition->type}}]" value="{{$definition->fee}}" step="0.01" min="0"></td>
                                       <td><input type="text" name="stc[{{$definition->type}}]" value="{{$definition->stc}}" id=""></td>
                                   </tr>
                                   @endforeach
                               </tbody>
                           </table>
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
 
    
    
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
$(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });

})
</script>
    
@endsection