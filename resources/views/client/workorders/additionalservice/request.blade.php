@extends('client.layouts.app')

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
<style>
    .tab-pane {
        margin-top: 10px;
    }
</style>

@endsection

@section('navigation')
    @include('client.navigation')
@endsection

@section('content')
{!! Form::open(['route' => ['client.notices.purchaseService',$work->id],'method' => 'POST','files' => true, 'autocomplete' => 'off']) !!}
<div id="top-wrapper" >
    <div class="container-fluid">
    <div  class="col-xs-12">
        <h1 class="page-header">Request Additional Services<br>
            <div class="pull-right">
                <button class="btn btn-success btn-purchase {{empty($templateLines) ? 'hidden' : ''}}" type="submit" disabled> Purchase</button>
                <a class="btn btn-danger " href="{{ route('client.notices.edit',$work->id)}}"><i class="fa fa-times"></i> Back</a>
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
            @if (Session::has('message'))
            <div class="col-xs-12 message-box">
                <div class="alert alert-info">{{ Session::get('message') }}</div>
            </div>
            @endif
            
            <div class="col-xs-12">
                <table class="table lines">
                    @foreach($templateLines as $line)
                    <tr class="existent-line-{{$line->id}}">
                        <td>
                            <div class="row">
                                <div class="col-xs-1 form-group">
                                    <label><br></label>
                                    <div class="checkbox checkbox-slider--b-flat">
                                        <label>
                                            <?php
                                                $unpaidInvoiceId = null;
                                                $pendingTodo = $work->unpaidLastTodo($line->todo_name);
                                                if ($pendingTodo) {
                                                    $unpaidInvoice = $pendingTodo->invoice();
                                                    if ($unpaidInvoice) {
                                                        $unpaidInvoiceId = $unpaidInvoice->id;
                                                    }
                                                }
                                            ?>
                                            <input name="choiceTodos[{{$line->id}}]" class="choiceTodos" data="{{$line->id}}" type="checkbox" unpaid-invoice="{{$unpaidInvoiceId}}"><span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-xs-4 form-group">
                                    <label>Description</label>
                                    <input readonly name="description[{{$line->id}}]" value="{{$line->description}}" class="form-control">
                                </div>
                                <div class="col-xs-4 form-group">
                                    <label>Summary</label>
                                    <textarea readonly name="summary[{{$line->id}}]" class="form-control" rows="1">{{$line->summary}}</textarea>
                                </div>
                                <div class="col-xs-1 form-group">
                                    <label>Quantity</label>
                                    <input type="number" min="1" name="quantity[{{$line->id}}]" value="{{$line->quantity}}" class="form-control quantity">
                                </div>
                                <div class="col-xs-2 form-group">
                                    <label>Price</label>
                                    <input type="hidden" name="price_item[{{$line->id}}]" value="{{$line->price}}">
                                    <input readonly type="number" name="price[{{$line->id}}]" value="{{$line->price * $line->quantity}}" data="{{$line->price}}" min="0" step="0.01" class="form-control price">
                                </div>
                                <input type="hidden" name="todo_name[{{$line->id}}]" value="{{$line->todo_name}}">
                                <input type="hidden" name="todo_uploads[{{$line->id}}]" value="{{$line->todo_uploads}}">
                                <input type="hidden" name="todo_instructions[{{$line->id}}]" value="{{$line->todo_instructions}}">
                            </div>
                            <div class="row uploads_instructions hidden">
                                <div class="col-xs-1 form-group"></div>
                                <div class="col-xs-11 form-group">
                                    @if($line->todo_uploads)
                                    <div class="col-md-6 panel panel-default" style="padding-bottom:6px;">
                                        <h4>Add attachment</h4>
                                        <div class="row">
                                            <div class="col-xs-12 form-group">
                                                <label>Upload File:</label>
                                                {!!  Form::file('upload['.$line->id.']','', ['class' => 'form-control']) !!}
                                            </div>
                                            <div class="col-xs-12 form-group">
                                                <label>Description:</label>
                                                {!!  Form::textarea('upload_description['.$line->id.']','', ['class' => 'form-control', 'rows'=>'6']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @if($line->todo_instructions)
                                    <div class="col-md-6 panel panel-default">
                                        <h4>Provide Additional Instructions</h4>
                                        <div class="row">
                                            <div class="col-xs-12 form-group">
                                                <label>Enter Instruction:</label>
                                                {!!  Form::textarea('instruction['.$line->id.']','', ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </table>
            </div>
            <div class="col-xs-12">
                <button class="btn btn-success btn-purchase pull-right {{empty($templateLines) ? 'hidden' : ''}}" type="submit" disabled> Purchase</button>
            </div>
        </div>
    </div>
</div>
{!! Form::close() !!}
<div class="modal fade" id="modal-unpaid-todo-exist" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-left">Existing Unpaid Service</h4>
      </div>
      <div class="modal-body text-left">
          <p>You have previously requested this service but did not pay the invoice. Would you like to pay it now or request a new additional service?</p>
      </div>
      {!! Form::open(['route' => 'client.invoices.payment'])!!}
      <div class="modal-footer">
            <button type="button" class="btn btn-success" data-dismiss="modal">Request New Additional Service</button>&nbsp;&nbsp;
            <button class="btn btn-warning" type="submit"> <i class="fa fa-money"></i>&nbsp;&nbsp; Pay Now</button> 
            <div class="modal-unpaid-todo-invoice-group"></div>
      </div>
      {!! Form::close() !!}
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('/vendor/bootstrap-filestyle/js/bootstrap-filestyle.min.js') }}" type="text/javascript"></script>
<script>
$('.btn-purchase').click(function(){
    $(this).addClass("disabled");
    $(this).css('pointer-events','none');
});
$(function () {
    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
    $(":file").filestyle();
    $("input[type='file']").attr('accept', '.pdf,.jpg,.jpeg,.tiff,.tif,.doc,.xls,.docx,.xlsx');
    $('.choiceTodos').change(function () {
        let isChecekd = false;
        $('.choiceTodos').each(function(e) {
            if ($(this).prop("checked")) {
                isChecekd = true;
            }
        })
        if (isChecekd) {
            $('.btn-purchase').removeAttr("disabled");
        } else {
            $('.btn-purchase').attr("disabled", true);
        }
    })
    $('.quantity').change(function () {
        const price = (parseFloat($(this).val()) || 0) * (parseFloat($(this).parent().parent().find('.price').attr('data')) || 0);
        $(this).parent().parent().find('.price').val(price);
    });
    $('.choiceTodos').click(function() {
        const lineId = $(this).attr('data');
        if ($(this).is(":checked")) {
            $('.existent-line-' + lineId + ' .uploads_instructions').removeClass('hidden');
        } else {
            $('.existent-line-' + lineId + ' .uploads_instructions').addClass('hidden');
        }

        const unpaidInvoiceId = $(this).attr('unpaid-invoice');
        if ($(this).is(":checked") && unpaidInvoiceId) {
            $('.modal-unpaid-todo-invoice-group').html('<input name="pay['+ unpaidInvoiceId +']"  type="hidden" value="1">');
            $('#modal-unpaid-todo-exist').modal('show');
        }
    });
});
</script>
    
@endsection