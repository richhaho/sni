@extends('admin.layouts.app')

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
<style>
  
</style>

@endsection

@section('navigation')
    @include('admin.navigation')
@endsection

@section('content')
    {!! Form::open(['route' => ['workorderfields.update',$existsWorkfield->id], 'method'=> 'POST','autocomplete' => 'off']) !!}
        {!! Form::hidden('status','open')!!}
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">Edit Work Order Field
                    <div class="pull-right">
                        <button class="btn btn-success btn-save" type="submit"> <i class="fa fa-floppy-o"></i> Save</button>
                        <a class="btn btn-danger " href="{{ route('workorderfields.index')}}"><i class="fa fa-times-circle"></i> Cancel</a> &nbsp;&nbsp;
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
                            <div class="alert {{ Session::get('message-class','alert-info') }}">{{ Session::get('message') }}</div>
                            </div>
                @endif
                <div class="row">
                    <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                               Field Details
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12 col-md-6 form-group">
                                        <label>Work Order Type:</label>
                                        
                                        {!!  Form::select('workorder_type',$wo_types,old("workorder_type",$existsWorkfield->workorder_type), ['class' => 'form-control','id'=>'workorder_type']) !!}
                                         

                                    </div>
                                    <div class="col-xs-12 col-md-6 form-group">
                                    <label>&nbsp;</label>
                                      <div class="checkbox checkbox-slider--b-flat">
                                          @if ($existsWorkfield->required)
                                          <label>
                                              <input name="required" type="checkbox" checked><span>Field Required?</span>
                                          </label>
                                          @else
                                          <label>
                                              <input name="required" type="checkbox"><span>Field Required?</span>
                                          </label>
                                          @endif
                                      </div>
                                    </div>
                                
                                </div>

                                <div class="row">
                                    <div class="col-xs-12 col-md-6 form-group">
                                     <label>Field Order:</label>
                                          {!!  Form::number('field_order',old("field_order",$existsWorkfield->field_order), ['class' => 'form-control', 'min'=>'0',  'step'=>'1']) !!}
                                    </div>
                                    <div class="col-xs-12 col-md-6 form-group">
                                        <label>Field Type:</label>
                                        @if ($answered=='true')
                                        {!!  Form::select('field_type',$field_type,old("field_type",$existsWorkfield->field_type), ['class' => 'form-control','id'=>'field_type','style'=>'pointer-events:none;background-color:#f5f5f5']) !!}
                                        @else
                                        {!!  Form::select('field_type',$field_type,old("field_type",$existsWorkfield->field_type), ['class' => 'form-control','id'=>'field_type']) !!}
                                        @endif
                                    </div>
                                    <div class="col-xs-12 col-md-6 form-group">
                                        <label>Field Label:</label>
                                        <input id="field_label" type="text" class="form-control noucase" name="field_label" value="{{$existsWorkfield->field_label}}" required autofocus>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <br>
                        <div class="panel panel-default dropdown_list" style="display: none">
                            <div class="panel-heading">
                               Dropdown Lists
                               <div class="pull-right"><a href="#" class="btn btn-success btn-xs add-line"><i class="fa fa-plus"></i> New Dropdown item</a></div>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                  <table class="table lines">
                                    <?php $n=0;
                                    if ($drop_lists){
                                    foreach ($drop_lists as $key => $value) 
                                    {    
                                    $n++;
                                    ?>
                                    <tr class="new-line-{{$n}}"><td>
                                       <div class="col-xs-5 form-group">
                                           <label>Value</label>
                                           <input name="new_name[{{$n}}]" value="{{$key}}" placeholder="Item's name" class="form-control new_name noucase" data-toggle="tooltip" data-placement="top" title="" required autofocus>
                                       </div>
                                       <div class="col-xs-5 form-group">
                                           <label>Label</label>
                                           <input name="new_value[{{$n}}]" value="{{$value}}" placeholder="Item's Value" class="form-control new_value noucase" data-toggle="tooltip" data-placement="top" title="" required autofocus>
                                       </div>
                                       
                                       <div class="col-xs-1" form-group">
                                           <label>&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                           <a href="#" class="btn btn-danger delete-line"  data-id="{{$n}}"><i class="fa fa-trash"></i> Delete</a>
                                       </div>
                                      </td></tr>
                                      <?php }} ?>
                                    </table>
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
 
<script>
 
$('.btn-save').click(function(){
    $('.btn-save').addClass("disabled");
    $('.btn-save').css('pointer-events','none');
});
$('input').mousedown(function () {
  $('.btn-save').removeClass("disabled");
  $('.btn-save').css('pointer-events','auto');
});
$('input').keydown(function () {
  $('.btn-save').removeClass("disabled");
  $('.btn-save').css('pointer-events','auto');
});

$(function () {
    var items_id={{$n}};
    $('#field_type').change(function(){
      if ($('#field_type').val()=='dropdown'){
        $('.dropdown_list').css('display','block');
      }else{
        $('.dropdown_list').css('display','none');
        $('table.lines').empty();
      }
   });

    if ($('#field_type').val()=='dropdown'){
        $('.dropdown_list').css('display','block');
      }else{
        $('.dropdown_list').css('display','none');
        $('table.lines').empty();
    }
    $(".message-box").fadeTo(3000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });



    $('table.lines').on('click','a.delete-line',function(){ 
        var xid  = $(this).data('id');
        $('tr.new-line-'+xid).remove();
   });

   $('.add-line').click(function(){
      items_id++;
      var new_lineItem=`<tr class="new-line-`+items_id+`"><td>
       <div class="col-xs-5 form-group">
           <label>Value</label>
           <input name="new_name[`+items_id+`]" value="" placeholder="Item's name" class="form-control new_name noucase" data-toggle="tooltip" data-placement="top" title="" required autofocus>
       </div>
       <div class="col-xs-5 form-group">
           <label>Label</label>
           <input name="new_value[`+items_id+`]" value="" placeholder="Item's Value" class="form-control new_value noucase" data-toggle="tooltip" data-placement="top" title="" required autofocus>
       </div>
       
       <div class="col-xs-1" form-group">
           <label>&nbsp;&nbsp;&nbsp;&nbsp;</label>
           <a href="#" class="btn btn-danger delete-line"  data-id="`+items_id+`"><i class="fa fa-trash"></i> Delete</a>
       </div>
      </td></tr>`;
    $('table.lines').append(new_lineItem);  


   });
});
</script>
    
@endsection
