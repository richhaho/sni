@extends('client.layouts.app')

@section('navigation')
    @include('client.navigation')
@endsection
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

        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">View Invoice {{$invoice->number}}
                    <div class="pull-right">
                        
                            <a class="btn btn-danger " href="{{ route('client.invoices.index')}}"><i class="fa fa-chevron-left"></i> Back</a> &nbsp;&nbsp;
                        
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
                               Info
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                <div class="col-xs-6 form-group">
                                    <label>Client:</label>
                                   {{$invoice->client->company_name}}
                                </div>
                                <div class="col-xs-6 form-group">
                                    <label>Work Order Number:</label>
                                   {{$invoice->work_order->number}}
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Item Lines
                                <div class="pull-right"></div>
                            </div>
                         
                                <table class="table lines">
                                     @foreach($invoice->lines as $line)
                                    <tr class="existent-line-{{$line->id}}">
                                        <td>
                                           
                                            <div class="col-xs-6 form-group">
                                                <label>Description</label>
                                                <input  disabled="on" name="description[{{$line->id}}]" value="{{ old("description." . $line->id,$line->description)}}" data-id="{{$line->id}}" placeholder="Item Descriptions" class="form-control description" data-toggle="tooltip" data-placement="top" title="">
                                            </div>
                                            <div class="col-xs-1 form-group">
                                                <label>Quantity</label>
                                                <input disabled="on" type="number" min="1" data-id="{{$line->id}}" name="quantity[{{$line->id}}]" value="{{ old("quantity." . $line->id,$line->quantity)}}" placeholder="Qty" class="form-control qty" data-toggle="tooltip" data-placement="top" title="">
                                            </div>
                                            <div class="col-xs-2 form-group">
                                                <label>Price</label>
                                                <input disabled="on"  type="number" data-id="{{$line->id}}" name="price[{{$line->id}}]" value="{{ old("price." . $line->id, $line->price)}}" placeholder="price" min="0" step="0.01" class="form-control price">
                                            </div>
                                             <div class="col-xs-2 form-group">
                                                <label>Amount</label>
                                                <input disabled="on"  disabled type="number" data-id="{{$line->id}}" name="amount[{{$line->id}}]" value="{{ old("amount." . $line->id, $line->amount)}}" placeholder="price" min="0" step="0.01" class="form-control amount">
                                            </div>
                                            
                                        </td>
                                    </tr>
                                    @endforeach
                                    
                                </table>
                            <div class="panel-footer ">&nbsp;<div class="col-xs-4 col-xs-offset-7"><strong class="">Invoice Total: <span class="invoice_total">{{$invoice->total_amount}}</span></strong></div></div>  
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
var contador = 1;
    
    
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
    });
  
     $('table.lines').on('click','a.delete-existent-line',function(){ 
        var xid  = $(this).data('id');
        $.ajax({
            url: '{{url("admin/invoices/lines")}}/' + xid,
            type: 'DELETE',
            success: function(result) {
                if (result == 'DELETED') {
                    $('table tr[class="existent-line-' + xid + '"]').remove();
                }
            }
        });
    });
  
     $('table.lines').on('change',".new_price, .new_qty", function() {
         var xid = $(this).data('newid');
         var nprice =  $('input[name="new_price['+ xid + ']"]').val();
         var nqty =  $('input[name="new_quantity['+ xid + ']"]').val();
         $('input[name="new_amount['+ xid + ']"]').val(parseFloat(nprice * nqty).toFixed(2));
         var total = 0;
         $('input[name^="new_amount"],input[name^="amount"]').each(function(index,elem){
            var val = parseFloat($(elem).val());
            if( !isNaN( val ) ) {
                total += val;
            }
         });
         $('span.invoice_total').html(parseFloat(total).toFixed(2));
         
     });
     
     $('table.lines').on('change',".price, .qty", function() {
         var xid = $(this).data('id');
         var nprice =  $('input[name="price['+ xid + ']"]').val();
         var nqty =  $('input[name="quantity['+ xid + ']"]').val();
         $('input[name="amount['+ xid + ']"]').val(parseFloat(nprice * nqty).toFixed(2));
         var total = 0;
         $('input[name^="new_amount"],input[name^="amount"]').each(function(index,elem){
            var val = parseFloat($(elem).val());
            if( !isNaN( val ) ) {
                total += val;
            }
         });
         $('span.invoice_total').html(total.toFixed(2));
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
  
            $('.description').typeahead(null, {
                name: 'description',
                source: items,
                display: 'description'
              }).on('typeahead:select', function(ev, suggestion) {
                  var xid = $(ev.target).data('id');
                  $('input[name="price[' + xid +']"]').val(d[resultList.indexOf(suggestion)].price).trigger( "change" );
              });
              

})
</script>
    
@endsection