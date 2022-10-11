@extends('researcher.layouts.app')

@section('navigation')
    @include('researcher.navigation')
@endsection
@section('css')
<style>
.new-template {
  display:none;
}

.delete-line {

}
</style>
@endsection
@section('content')
    {!! Form::open(['route' => ['client.templates.update',$client->id,$template->id],'method' => 'PUT','autocomplete' => 'off']) !!}
        {!! Form::hidden('status',4)!!}
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">Edit Template for {{$client->company_name}}
                    <div class="pull-right">
                        <button class="btn btn-success " type="submit"> <i class="fa fa-floppy-o"></i> Save</button>
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
                                   {{ Form::select('type',$types,old('type',$template->type->slug),['class' =>"form-control"])}}
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Items
                                <div class="pull-right"><a href="#" class="btn btn-success btn-xs add-line"><i class="fa fa-plus"></i> New Line</a></div>
                            </div>
                         
                                <table class="table lines">
                                     @foreach($template->lines as $line)
                                    <tr class="existent-line-{{$line->id}}">
                                        <td>
                                           
                                            <div class="col-xs-3 form-group">
                                                <label>Type</label>
                                               {{ Form::select('line_type[' . $line->id . ']',$line_types,$line->type,['class'=>'form-control'])}}
                                            </div>
                                            <div class="col-xs-5 form-group">
                                                <label>Description</label>
                                                <input name="description[{{$line->id}}]" value="{{ old("description." . $line->id,$line->description)}}" placeholder="Item Descriptions" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                            </div>
                                            <div class="col-xs-1 form-group">
                                                <label>Quantity</label>
                                                <input type="number" min="1" name="quantity[{{$line->id}}]" value="{{ old("quantity." . $line->id,$line->quantity)}}" placeholder="Qty" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                            </div>
                                            <div class="col-xs-2 form-group">
                                                <label>Price</label>
                                                <input type="number" name="price[{{$line->id}}]" value="{{ old("price." . $line->id, $line->price)}}" placeholder="price" min="0" step="0.01" class="form-control">
                                            </div>
                                            <div class="col-xs-1 form-group">
                                                <label>&nbsp;&nbsp;&nbsp;</label>
                                                <a href="#" class="btn btn-danger delete-existent-line"  data-id="{{$line->id}}"><i class="fa fa-trash"></i> Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @if(old('new_line_type'))
                                    @foreach (old('new_line_type') as $key => $val)
                                    <tr class="new-line-{{$key}}"><td>
                                        <div class="col-xs-3 form-group">
                                           <label>Type</label>
                                              {{ Form::select('new_line_type['.$key.']',$line_types,$val,['class'=>'form-control'])}}
                                           </div>
                                       <div class="col-xs-5 form-group">
                                           <label>Description</label>
                                           <input name="new_description[{{$key}}]" value="{{old('new_description')[$key]}}" placeholder="Item Descriptions" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                       </div>
                                       <div class="col-xs-1 form-group">
                                           <label>Quantity</label>
                                           <input type="number" min="1" name="new_quantity[{{$key}}]" value="{{old('new_quantity')[$key]}}" placeholder="Qty" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                       </div>
                                       <div class="col-xs-2 form-group">
                                           <label>Price</label>
                                           <input type="number" name="new_price[{{$key}}]" value="{{old('new_price')[$key]}}" placeholder="price" min="0" step="0.01" class="form-control">
                                       </div>
                                       <div class="col-xs-1 form-group">
                                           <label>&nbsp;&nbsp;&nbsp;</label>
                                           <a href="#" class="btn btn-danger delete-line"  data-id="{{$key}}"><i class="fa fa-trash"></i> Delete</a>
                                       </div>
                                      </td></tr>
                                    @endforeach
                                    @endif
                                </table>
                                
                        </div>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
   
               
            </div>
            <!-- /.container-fluid -->
            
        </div>
    {!! Form::close() !!}
    
    
    <div class="new-template">
       
        <tr class="new_line@i"><td>
         <div class="col-xs-3 form-group">
            <label>Type</label>
               {{ Form::select('new_line_type[@i]',$line_types,'',['class'=>'form-control'])}}
            </div>
        <div class="col-xs-5 form-group">
            <label>Description</label>
            <input name="new_description[@i]" value="" placeholder="Item Descriptions" class="form-control" data-toggle="tooltip" data-placement="top" title="">
        </div>
        <div class="col-xs-1 form-group">
            <label>Quantity</label>
            <input type="number" min="1" name="new_quantity[@i]" value="" placeholder="Qty" class="form-control" data-toggle="tooltip" data-placement="top" title="">
        </div>
        <div class="col-xs-2 form-group">
            <label>Price</label>
            <input type="number" name="new_price[@i]" value="" placeholder="price" min="0" step="0.01" class="form-control">
        </div>
        <div class="col-xs-1 form-group">
            <label>&nbsp;&nbsp;&nbsp;</label>
            <a href="#" class="btn btn-danger delete-line"  data-id="@i"><i class="fa fa-trash"></i> Delete</a>
        </div>
       </td></tr>
    </div>
@endsection

@section('scripts')
<script>
var contador = 1;
    
    
$(function () {
  $('[data-toggle="tooltip"]').tooltip()

    $('a.add-line').on('click',function() {
        
        var new_line = $('.new-template').html();
        var nhtml = new_line.replace(/@i/g, contador.toString());
        nhtml = '<tr class="new-line-' + contador.toString() + '"><td>' + nhtml + '</td></tr>'
        $('table.lines').append(nhtml);
        contador++;
    });
    
     $('table.lines').on('click','a.delete-line',function(){ 
        console.log('click');
        var xid  = $(this).data('id');
        $('table tr[class="new-line-' + xid + '"]').remove();
    });
  
     $('table.lines').on('click','a.delete-existent-line',function(){ 
        var xid  = $(this).data('id');
        $.ajax({
            url: '{{url("researcher/templates/lines")}}/' + xid,
            type: 'DELETE',
            success: function(result) {
                if (result == 'DELETED') {
                    $('table tr[class="existent-line-' + xid + '"]').remove();
                }
            }
        });
    });
  
  
})
</script>
    
@endsection