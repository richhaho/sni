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
    {!! Form::open(['route' => ['invoices.store'],'autocomplete' => 'off']) !!}
  
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">Create New Invoice 
                    <div class="pull-right">
                        <button class="btn btn-success " type="submit"> <i class="fa fa-floppy-o"></i> Save</button>
                 
                            <a class="btn btn-danger " href="{{ route('invoices.index')}}"><i class="fa fa-times-circle"></i> Cancel</a> &nbsp;&nbsp;

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
                
                <div class="alert alert-danger hidden negative-total">
                        <ul>
                           <li>Postage to be charged is less than previous postage paid, No invoice will be created.</li>
                           
                        </ul>
                    </div>
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
                                   {{ Form::select('client',$clients,'',['class'=>'form-control'])}}
                                </div>
                                <div class="col-xs-6 form-group">
                                    <label>Work Order Number:</label>
                                   {{ Form::select('work_order_id',$work_orders,'',['class'=>'form-control'])}}
                                    
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Item Lines
                                <div class="pull-right"><a href="#" class="btn btn-success btn-xs add-line"><i class="fa fa-plus"></i> New Line</a></div>
                            </div>
                         
                                <table class="table lines">
                                    @if(old('new_line_type'))
                                    @foreach (old('new_line_type') as $key => $val)
                                    <tr class="new-line-{{$key}}"><td>
                                       
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
                                           <input type="number" data-newid="{{$key}}"  name="new_price[{{$key}}]" value="{{old('new_price')[$key]}}" placeholder="price" min="0" step="0.01" class="form-control new_price">
                                       </div>
                                        <div class="col-xs-2 form-group">
                                            <label>Amount</label>
                                            <input disabled data-newid="{{$key}}"  type="number" name="new_amount[{{$line->id}}]" value="{{ old("new_amount." . $line->id, $line->amount)}}" placeholder="Amount" min="0" step="0.01" class="form-control new_amount">
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
            
        </div>
    {!! Form::close() !!}
    
    
    <div class="new-item-line">
       
        <tr class="new_line@i"><td>
         
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
            <input type="number" data-newid="@i"  name="new_price[@i]" value="" placeholder="price" min="0" step="0.01" class="form-control new_price">
        </div>
        <div class="col-xs-2 form-group">
            <label>Amount</label>
            <input disabled data-newid="@i"  type="number" name="new_amount[@i]" value="" placeholder="Amount" min="0" step="0.01" class="form-control new_amount">
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
        
        var new_line = $('.new-item-line').html();
        var nhtml = new_line.replace(/@i/g, contador.toString());
        nhtml = '<tr class="new-line-' + contador.toString() + '"><td>' + nhtml + '</td></tr>'
        $('table.lines').append(nhtml);
        calculateTotal();
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
        calculateTotal();
    });
  
     $('table.lines').on('click','a.delete-existent-line',function(){ 
        var xid  = $(this).data('id');
        $.ajax({
            url: '{{url("researcher/invoices/lines")}}/' + xid,
            type: 'DELETE',
            success: function(result) {
                if (result == 'DELETED') {
                    $('table tr[class="existent-line-' + xid + '"]').remove();
                    calculateTotal()
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
         calculateTotal();
         
     });
     
     $('table.lines').on('change',".price, .qty", function() {
         var xid = $(this).data('id');
         var nprice =  $('input[name="price['+ xid + ']"]').val();
         var nqty =  $('input[name="quantity['+ xid + ']"]').val();
         $('input[name="amount['+ xid + ']"]').val(parseFloat(nprice * nqty).toFixed(2));
         calculateTotal();
     });
     calculateTotal();
     
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
  

})

function calculateTotal() {
        var total = 0;
         $('input[name^="new_amount"],input[name^="amount"]').each(function(index,elem){
            var val = parseFloat($(elem).val());
            if( !isNaN( val ) ) {
                total += val;
            }
         });
         $('span.invoice_total').html(parseFloat(total).toFixed(2));
         if (total <= 0) {
             $()".negative-total").removeClass('hidden');
         } else {
             $()".negative-total").addClass('hidden');
         }
}
</script>
    
@endsection