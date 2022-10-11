@extends('researcher.layouts.app')

@section('css')
<style>
.new-template {
  display:none;
}

.new-item-line {
    display:none;
}
.delete-line {

}
</style>
@endsection

@section('content')
    {!! Form::open(['route' => ['invoices.store'],'autocomplete' => 'off']) !!}
        {!! Form::hidden('from','document-generator') !!}
        
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">Mailing Invoice
                    <div class="pull-right">
                        <button class="btn btn-success " type="submit"> <i class="fa fa-floppy-o"></i> Save</button>
                 
                           <a href="{{route('workorders.index')}}" class="btn  btn-danger " ><i class="fa fa-times-circle"></i> Cancel</a> &nbsp;&nbsp;

                    </div>
                </h1>       
            </div>
            </div>
        
         
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
                               Info
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                <div class="col-xs-6 form-group">
                                    <label>Client:</label>
                                    
                                   {!! Form::hidden('client',$client->id)!!}
                                   {!! $client->company_name!!}
                                </div>
                                <div class="col-xs-6 form-group">
                                    <label>Work Order Number:</label>
                                   {{ Form::hidden('work_order_id',$work_order_id)}}
                                    {{$work_order_number}}
                                </div>
                                </div>
                                <div class="row">
                                    @foreach($recipients as $rep)
                                    <div class="col-xs-3">
                                        <div class="panel panel-default">
                                            <div class="panel-body">
                                                 {{ $rep->firm_name }} 
                                                 @if(strlen($rep->attention_name)>0 ) - ATTN: {{ $rep->attention_name }}@endif
                                                 <br /> {{ preg_replace('/\<br(\s*)?\/?\>/i', "\n", $rep->address) }}
                                                 <br /><strong>Mailing Type: {{ $mailing_types[$rep->mailing_type] }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 hidden negative-total">
                    <div class="alert alert-danger ">
                        <ul>
                           <li>Postage to be charged is less than previous postage paid, No invoice will be created.</li>
                           
                        </ul>
                    </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Item Lines
                                <div class="pull-right"><a href="#" class="btn btn-success btn-xs add-line"><i class="fa fa-plus"></i> New Line</a></div>
                            </div>
                         
                                <table class="table lines">
                                    @foreach ($new_lines as $key => $val)
                                    <tr class="new-line-{{$loop->index}}"><td>
                                       {!! Form::hidden ('new_recipient[' . $loop->index .']' ,$val['recipient_id']) !!}
                                       {!! Form::hidden ('line_recipient_type[' . $loop->index .']' ,$val['mailing_type']) !!}
                                       <div class="col-xs-5 form-group">
                                           <label>Description</label>
                                           <input name="new_description[{{$loop->index}}]" data-newid="{{$loop->index}}" value="{{$val['description']}}" placeholder="Item Descriptions" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                       </div>
                                       <div class="col-xs-2 form-group">
                                           <label>Quantity</label>
                                           <input type="number" data-newid="{{$loop->index}}"  min="1" name="new_quantity[{{$loop->index}}]" value="{{$val['quantity']}}" placeholder="Qty" class="form-control new_qty" data-toggle="tooltip" data-placement="top" title="">
                                       </div>
                                       <div class="col-xs-2 form-group">
                                           <label>Price</label>
                                           <input type="number" data-newid="{{$loop->index}}"  name="new_price[{{$loop->index}}]" value="{{$val['price']}}" placeholder="price" step="0.01" class="form-control new_price">
                                       </div>
                                        <div class="col-xs-2 form-group">
                                            <label>Amount</label>
                                            <input disabled data-newid="{{$loop->index}}"  type="number" name="new_amount[{{$loop->index}}]" value="{{ number_format($val['price'] * $val['quantity'],2)}}" placeholder="Amount" step="0.01" class="form-control new_amount">
                                        </div>
                                       <div class="col-xs-1" form-group">
                                           <label>&nbsp;&nbsp;&nbsp;</label>
                                           <a href="#" class="btn btn-danger delete-line"  data-id="{{$loop->index}}"><i class="fa fa-trash"></i> Delete</a>
                                       </div>
                                      </td></tr>
                                    @endforeach
                                    
                                    
                                    
                                    @if(old('new_line_type'))
                                    @foreach (old('new_line_type') as $key => $val)
                                    <tr class="new-line-{{$key}}"><td>
                                       {!! Form::hidden ('new_recipient[' . $key .']' ,old('new_recipient')[$key]);!!}
                                       <div class="col-xs-6 form-group">
                                           <label>Description</label>
                                           <input name="new_description[{{$key}}]" data-newid="{{$key}}" value="{{old('new_description')[$key]}}" placeholder="Item Descriptions" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                       </div>
                                       <div class="col-xs-1 form-group">
                                           <label>Quantity</label>
                                           <input type="number" data-newid="{{$key}}"  min="1" name="new_quantity[{{$key}}]" value="{{old('new_quantity')[$key]}}" placeholder="Qty" class="form-control new_qty" data-toggle="tooltip" data-placement="top" title="">
                                       </div>
                                       <div class="col-xs-2 form-group">
                                           <label>Price</label>
                                           <input type="number" data-newid="{{$key}}"  name="new_price[{{$key}}]" value="{{old('new_price')[$key]}}" placeholder="price"  step="0.01" class="form-control new_price">
                                       </div>
                                        <div class="col-xs-2 form-group">
                                            <label>Amount</label>
                                            <input disabled data-newid="{{$key}}"  type="number" name="new_amount[{{$line->id}}]" value="{{ old("new_amount." . $line->id, $line->amount)}}" placeholder="Amount" step="0.01" class="form-control new_amount">
                                        </div>
                                       <div class="col-xs-1" form-group">
                                           <label>&nbsp;&nbsp;&nbsp;</label>
                                           <a href="#" class="btn btn-danger delete-line"  data-id="{{$key}}"><i class="fa fa-trash"></i> Delete</a>
                                       </div>
                                      </td></tr>
                                    @endforeach
                                    @endif
                                </table>
                            <div class="panel-footer ">&nbsp;<div class="col-xs-4 col-xs-offset-7"><strong class="">Invoice Total: <span class="invoice_total">0.00</span></strong></div></div>  
                        </div>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
   
               
            </div>
            <!-- /.container-fluid -->
            
       
    {!! Form::close() !!}
    
    
    <div class="new-item-line">
       
        <tr class="new_line@i"><td>
        <input type="hidden" name ="new_recipient[@i]" value="0">
        <div class="col-xs-6 form-group">
            <label>Description</label>
            <input name="new_description[@i]" data-newid="@i"  value=""  placeholder="Item Descriptions" class="form-control" data-toggle="tooltip" data-placement="top" title="">
        </div>
        <div class="col-xs-1 form-group">
            <label>Quantity</label>
            <input type="number" min="1" data-newid="@i"  name="new_quantity[@i]" value="" placeholder="Qty" class="form-control new_qty" data-toggle="tooltip" data-placement="top" title="">
        </div>
        <div class="col-xs-2 form-group">
            <label>Price</label>
            <input type="number" data-newid="@i"  name="new_price[@i]" value="" placeholder="price"  step="0.01" class="form-control new_price">
        </div>
        <div class="col-xs-2 form-group">
            <label>Amount</label>
            <input disabled data-newid="@i"  type="number" name="new_amount[@i]" value="" placeholder="Amount" step="0.01" class="form-control new_amount">
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
var contador = {{ count($new_lines)}};
    
    
$(function () {
  $('[data-toggle="tooltip"]').tooltip()

    $('a.add-line').on('click',function() {
        
        var new_line = $('.new-item-line').html();
        var nhtml = new_line.replace(/@i/g, contador.toString());
        nhtml = '<tr class="new-line-' + contador.toString() + '"><td>' + nhtml + '</td></tr>'
        $('table.lines').append(nhtml);
        $('input[name="new_description['+ contador +']"]').typeahead(null, {
                name: 'description',
                source: items,
                display: 'description'
              }).on('typeahead:select', function(ev, suggestion) {
                  var xid = $(ev.target).data('newid');
                  $('input[name="new_price[' + xid +']"]').val(d[resultList.indexOf(suggestion)].price).trigger( "change" );
              });
        contador++;
    });
    
     $('table.lines').on('click','a.delete-line',function(){ 
        console.log('click');
        var xid  = $(this).data('id');
        $('table tr[class="new-line-' + xid + '"]').remove();
       CalculateTotal();
    });
  
     $('table.lines').on('click','a.delete-existent-line',function(){ 
        var xid  = $(this).data('id');
        $.ajax({
            url: '{{url("researcher/invoices/lines")}}/' + xid,
            type: 'DELETE',
            success: function(result) {
                if (result == 'DELETED') {
                    $('table tr[class="existent-line-' + xid + '"]').remove();
                   CalculateTotal();
                }
            }
        });
    });
  
    $('select[name="client"]').on('change',function(){ 
        console.log('changed');
        var xid  = $(this).val();
        $('select[name="work_order_id"]').load('{{url("researcher/clients")}}/'+ xid +"/workorders");
    });
  
  
     $('table.lines').on('change',".new_price, .new_qty", function() {
         var xid = $(this).data('newid');
         var nprice =  $('input[name="new_price['+ xid + ']"]').val();
         var nqty =  $('input[name="new_quantity['+ xid + ']"]').val();
         $('input[name="new_amount['+ xid + ']"]').val(parseFloat(nprice * nqty).toFixed(2));
         CalculateTotal();
         
     });
     
     
     
     CalculateTotal();
     $('table.lines').on('change',".price, .qty", function() {
         var xid = $(this).data('id');
         var nprice =  $('input[name="price['+ xid + ']"]').val();
         var nqty =  $('input[name="quantity['+ xid + ']"]').val();
         $('input[name="amount['+ xid + ']"]').val(parseFloat(nprice * nqty).toFixed(2));
         CalculateTotal();
     });
     
             var  d, resultList;
         var items = new Bloodhound({
            datumTokenizer: function (datum) {
              return Bloodhound.tokenizers.whitespace(datum.description);   
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,

            // url points to a json file that contains an array of country names, see
            // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
            //local: ['Afghanistan','Albania','Algeria','American Samoa','Andorra','Angola','Anguilla','Antarctica'],
            prefetch:  { url: '{{ route('list.items') }}' , 
                         cache: false,
                         filter: function(data) {
                            d = data;
                            resultList = $.map(data, function(item) {
                                return {
                                    description: item.description
                                };
                          });
                            return resultList;
                        },
                     }
          });
  

});
function CalculateTotal() {
         var total = 0;
         $('input[name^="new_amount"],input[name^="amount"]').each(function(index,elem){
            var val = parseFloat($(elem).val());
            if( !isNaN( val ) ) {
                total += val;
            }
         });
         $('span.invoice_total').html(parseFloat(total).toFixed(2));
         if (total <= 0) {
             $(".negative-total").removeClass('hidden');
         } else {
             $(".negative-total").addClass('hidden');
         }
     }
</script>
    
@endsection