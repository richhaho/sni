<div  class="col-xs-12">
    <h1 class="page-header">Invoices List
        <div class="pull-right">
            <a class="btn btn-success " href="{{ route('workorders.newinvoice',$work->id)}}?fromindex=2"> <i class="fa fa-plus"></i> Create Invoice</a>
            
        </div>
    </h1>       
</div>
<div >&nbsp</div>
<div class="row">
    <div class="col-xs-12">

    <div class="col-xs-12">
        @if(count($work->invoices) >0 )
        <table class="table" >
            <thead>
                <tr>
                    <th class="text-left"> Description</th>
                    <th class="text-right">Amount</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($work->invoices as $invoice)
                <tr>
                    <td><a role="button" href="#lines{{$invoice->id}}" data-toggle="collapse" aria-expanded="false" aria-controls="lines{{$invoice->id}}"><i class="fa fa-plus-circle"></i></a> Invoice {{ $invoice->number}}</td>
                    <td class="text-right"> {{ number_format($invoice->total_amount,2) }}</td>
                    <td class="col-xs-1 text-right">
                    
                        <a href="{{ route('invoices.edit',$invoice->id) ."?from=workorder"}}" class="btn btn-success btn-xs"><i class="fa fa-pencil"></i> Edit</a>

                    

                    </td>
                </tr>
                <tr  class="collapse" id="lines{{$invoice->id}}">
                    <td colspan="2">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="text-Left">Description</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-right">Price</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->lines as $line)
                            <tr>
                                <td>{{ $line->description }}</td>
                                <td class="text-center">{{ $line->quantity }}</td>
                                <td class="text-right">{{ number_format($line->price,2) }}</td>
                                <td class="text-right">{{ number_format($line->amount,2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </td>
                </tr>
               @endforeach
            </tbody>
        </table>
        @else
        No Invoices Yet
        @endif
    </div> <!-- finish panel body -->
        
</div><!-- finish panel  -->
</div><!-- col-xs  -->
