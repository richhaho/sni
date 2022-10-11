@extends('researcher.layouts.app')

@section('navigation')
    @include('researcher.navigation')
@endsection

@section('content')
    {!! Form::open(['route' => ['client.templates.store',$client->id],'autocomplete' => 'off']) !!}
        {!! Form::hidden('client_id',$client->id)!!}
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">New Template for {{ $client->company_name }}
                    <div class="pull-right">
                        <button class="btn btn-success " type="submit"> <i class="fa fa-floppy-o"></i> Create</button>
                        <a class="btn btn-danger " href="{{ route('client.templates.index',$client->id)}}"><i class="fa fa-times-circle"></i> Cancel</a> &nbsp;&nbsp;
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
                  
                    
                    <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                               Type
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                   {{ Form::select('type',$types,old('type',$type),['class' =>"form-control"])}}
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Items
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                <div class="col-xs-2 form-group">
                                    <label>Type</label>
                                   {{ Form::select('line_type',$line_types,'',['class'=>'form-control'])}}
                                </div>
                                <div class="col-xs-7 form-group">
                                    <label>Description</label>
                                    <input name="description" value="{{ old("description")}}" placeholder="Item Descriptions" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                <div class="col-xs-1 form-group">
                                    <label>Quantity</label>
                                    <input type="number" min="1" name="quantity" value="{{ old("quantity",1)}}" placeholder="Qty" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                <div class="col-xs-2 form-group">
                                    <label>Price</label>
                                    <input type="number" name="price" value="{{ old("price")}}" placeholder="price" min="0" step="0.01" class="form-control">
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
 
    
    
$(function () {
  $('[data-toggle="tooltip"]').tooltip()

 
  
        
    
  
})
</script>
    
@endsection