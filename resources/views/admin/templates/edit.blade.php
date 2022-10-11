@extends('admin.layouts.app')

@section('navigation')
    @include('admin.navigation')
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
    {!! Form::open(['route' => ['templates.update',$template->id],'method' => 'PUT','autocomplete' => 'off']) !!}
        {!! Form::hidden('status',4)!!}
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">Edit Template
                    <div class="pull-right">
                        <button class="btn btn-success " type="submit"> <i class="fa fa-floppy-o"></i> Save</button>
                        <a class="btn btn-danger " href="{{ route('templates.index')}}"><i class="fa fa-times-circle"></i> Cancel</a> &nbsp;&nbsp;
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
                                            <div class="row">
                                                <div class="col-xs-3 form-group">
                                                    <label>Type</label>
                                                    {{ Form::select('line_type[' . $line->id . ']',$line_types,$line->type,['class'=>'form-control line_type'])}}
                                                </div>
                                                <div class="col-xs-5 form-group">
                                                    <label>Description</label>
                                                    <input name="description[{{$line->id}}]" value="{{ old("description." . $line->id,$line->description)}}" placeholder="Item Descriptions" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                                </div>
                                                <div class="col-xs-1 form-group">
                                                    <label>Quantity</label>
                                                    <input type="number" min="1" name="quantity[{{$line->id}}]" {{ old("line_type." . $line->id,$line->type) == 'additional-service' ? 'readonly' :'' }} value="{{ old("quantity." . $line->id,$line->quantity)}}" placeholder="Qty" class="form-control quantity" data-toggle="tooltip" data-placement="top" title="">
                                                </div>
                                                <div class="col-xs-2 form-group">
                                                    <label>Price</label>
                                                    <input type="number" name="price[{{$line->id}}]" value="{{ old("price." . $line->id, $line->price)}}" placeholder="price" min="0" step="0.01" class="form-control">
                                                </div>
                                                <div class="col-xs-1 form-group">
                                                    <label>&nbsp;&nbsp;&nbsp;</label>
                                                    <a href="#" class="btn btn-danger delete-existent-line"  data-id="{{$line->id}}"><i class="fa fa-trash"></i> Delete</a>
                                                </div>
                                            </div>
                                            
                                            <div class="row todo-group {{ old("line_type." . $line->id,$line->type) != 'additional-service' ? 'hidden' :'' }}">
                                                <div class="col-xs-3 form-group">
                                                    <label>To Do Name</label>
                                                    <input name="todo_name[{{$line->id}}]" value="{{ old("todo_name." . $line->id,$line->todo_name)}}" placeholder="To Do Name" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                                </div>
                                                <div class="col-xs-5 form-group">
                                                    <label>Summary</label>
                                                    <textarea name="summary[{{$line->id}}]" placeholder="Summary" class="form-control" rows="1">{{ old("summary." . $line->id,$line->summary)}}</textarea>
                                                </div>
                                                <div class="col-xs-1 form-group">
                                                    <label>To Do Uploads</label>
                                                    <div class="checkbox checkbox-slider--b-flat">
                                                        <label>
                                                            <input name="todo_uploads[{{$line->id}}]" id="todo_uploads[{{$line->id}}]" type="checkbox" {{ old("todo_uploads." . $line->id,$line->todo_uploads) ? 'checked' :''}}><span></span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-xs-2 form-group">
                                                    <label>To Do Instructions</label>
                                                    <div class="checkbox checkbox-slider--b-flat">
                                                        <label>
                                                            <input name="todo_instructions[{{$line->id}}]" id="todo_instructions[{{$line->id}}]" type="checkbox" {{ old("todo_instructions." . $line->id,$line->todo_instructions) ? 'checked' :''}}><span></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @if(old('new_line_type'))
                                    @foreach (old('new_line_type') as $key => $val)
                                    <tr class="new-line-{{$key}}"><td>
                                        <div class="row">
                                            <div class="col-xs-3 form-group">
                                                <label>Type</label>
                                                    {{ Form::select('new_line_type['.$key.']',$line_types,$val,['class'=>'form-control new_line_type'])}}
                                                </div>
                                            <div class="col-xs-5 form-group">
                                                <label>Description</label>
                                                <input name="new_description[{{$key}}]" value="{{old('new_description')[$key]}}" placeholder="Item Descriptions" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                            </div>
                                            <div class="col-xs-1 form-group">
                                                <label>Quantity</label>
                                                <input type="number" min="1" name="new_quantity[{{$key}}]" {{ old('new_line_type')[$key] == 'additional-service' ? 'readonly' :'' }} value="{{old('new_quantity')[$key]}}" placeholder="Qty" class="form-control new_quantity" data-toggle="tooltip" data-placement="top" title="">
                                            </div>
                                            <div class="col-xs-2 form-group">
                                                <label>Price</label>
                                                <input type="number" name="new_price[{{$key}}]" value="{{old('new_price')[$key]}}" placeholder="price" min="0" step="0.01" class="form-control">
                                            </div>
                                            <div class="col-xs-1 form-group">
                                                <label>&nbsp;&nbsp;&nbsp;</label>
                                                <a href="#" class="btn btn-danger delete-line"  data-id="{{$key}}"><i class="fa fa-trash"></i> Delete</a>
                                            </div>
                                        </div>
                                        <div class="row todo-group {{ old('new_line_type')[$key] != 'additional-service' ? 'hidden' :'' }}">
                                            <div class="col-xs-3 form-group">
                                                <label>To Do Name</label>
                                                <input name="new_todo_name[{{$key}}]" value="{{old('new_todo_name')[$key]}}" placeholder="To Do Name" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                            </div>
                                            <div class="col-xs-5 form-group">
                                                <label>Summary</label>
                                                <textarea name="new_summary[{{$key}}]" placeholder="Summary" class="form-control" rows="1">{{old('new_summary')[$key]}}</textarea>
                                            </div>
                                            <div class="col-xs-1 form-group">
                                                <label>To Do Uploads</label>
                                                <div class="checkbox checkbox-slider--b-flat">
                                                    <label>
                                                        <input name="new_todo_uploads[{{$key}}]" id="new_todo_uploads[{{$key}}]" type="checkbox" {{ old('new_todo_uploads')[$key] ? 'checked' :''}}><span></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-xs-2 form-group">
                                                <label>To Do Instructions</label>
                                                <div class="checkbox checkbox-slider--b-flat">
                                                    <label>
                                                        <input name="new_todo_instructions[{{$key}}]" id="new_todo_instructions[{{$key}}]" type="checkbox" {{old('new_todo_instructions')[$key] ? 'checked' :''}}><span></span>
                                                    </label>
                                                </div>
                                            </div>
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
            <div class="row">
                <div class="col-xs-3 form-group">
                    <label>Type</label>
                    {{ Form::select('new_line_type[@i]',$line_types,'',['class'=>'form-control new_line_type', 'onchange'=>'changeNewLine(this)'])}}
                    </div>
                <div class="col-xs-5 form-group">
                    <label>Description</label>
                    <input name="new_description[@i]" value="" placeholder="Item Descriptions" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                </div>
                <div class="col-xs-1 form-group">
                    <label>Quantity</label>
                    <input type="number" min="1" name="new_quantity[@i]" value="1" placeholder="Qty" class="form-control new_quantity" data-toggle="tooltip" data-placement="top" title="">
                </div>
                <div class="col-xs-2 form-group">
                    <label>Price</label>
                    <input type="number" name="new_price[@i]" value="" placeholder="price" min="0" step="0.01" class="form-control">
                </div>
                <div class="col-xs-1 form-group">
                    <label>&nbsp;&nbsp;&nbsp;</label>
                    <a href="#" class="btn btn-danger delete-line"  data-id="@i"><i class="fa fa-trash"></i> Delete</a>
                </div>
            </div>
            <div class="row todo-group hidden">
                <div class="col-xs-3 form-group">
                    <label>To Do Name</label>
                    <input name="new_todo_name[@i]" value="" placeholder="To Do Name" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                </div>
                <div class="col-xs-5 form-group">
                    <label>Summary</label>
                    <textarea name="new_summary[@i]" placeholder="Summary" class="form-control" rows="1"></textarea>
                </div>
                <div class="col-xs-1 form-group">
                    <label>To Do Uploads</label>
                    <div class="checkbox checkbox-slider--b-flat">
                        <label>
                            <input name="new_todo_uploads[@i]" type="checkbox"><span></span>
                        </label>
                    </div>
                </div>
                <div class="col-xs-2 form-group">
                    <label>To Do Instructions</label>
                    <div class="checkbox checkbox-slider--b-flat">
                        <label>
                            <input name="new_todo_instructions[@i]" type="checkbox"><span></span>
                        </label>
                    </div>
                </div>
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
            url: '{{url("admin/templates/lines")}}/' + xid,
            type: 'DELETE',
            success: function(result) {
                if (result == 'DELETED') {
                    $('table tr[class="existent-line-' + xid + '"]').remove();
                }
            }
        });
    });

    $('.line_type').change(function() {
        const type = $(this).val();
        if (type == 'additional-service') {
            $(this).parent().parent().parent().find('.todo-group').removeClass('hidden');
            $(this).parent().parent().parent().find('.quantity').attr('readonly', true);
        } else {
            $(this).parent().parent().parent().find('.todo-group').addClass('hidden');
            $(this).parent().parent().parent().find('.quantity').attr('readonly', false);
        }
    })

    $('.new_line_type').change(function() {
        const type = $(this).val();
        if (type == 'additional-service') {
            $(this).parent().parent().parent().find('.todo-group').removeClass('hidden');
            $(this).parent().parent().parent().find('.new_quantity').attr('readonly', true);
        } else {
            $(this).parent().parent().parent().find('.todo-group').addClass('hidden');
            $(this).parent().parent().parent().find('.new_quantity').attr('readonly', false);
        }
    })
})
function changeNewLine(e) {
    const type = $(e).val();
    if (type == 'additional-service') {
        $(e).parent().parent().parent().find('.todo-group').removeClass('hidden');
        $(e).parent().parent().parent().find('.new_quantity').attr('readonly', true);
    } else {
        $(e).parent().parent().parent().find('.todo-group').addClass('hidden');
        $(e).parent().parent().parent().find('.new_quantity').attr('readonly', false);
    }
}
</script>
    
@endsection