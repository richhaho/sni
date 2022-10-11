@extends('admin.layouts.app')

@section('navigation')
    @include('admin.navigation')
@endsection
@section('css')
<style>
    h1.with-buttons {
        display: block;
        width: 100%;
        float: left;
    }
    .page-header h1 { margin-top: 0; }
    
     #filters-form {
        margin-bottom: 15px;
        margin-top: 15px;
    }
    .pull-right{
        padding-right: 5px;
    }
    
     
</style>
@endsection

@section('content')
            <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="page-header col-xs-12">
                        <div class="col-xs-12  col-md-12">
                            <h1 class="" > Invoices Batches</h1>
                        </div>
                    </div>
                        <div class="col-xs-12" id="filters-form">
                            {!! Form::open(['route' => 'invoicesbatches.setfilter', 'class'=>'form-inline'])!!}
                                <div class="form-group">
                                    <label for="invoicebatchs_status_filter"> Status: </label>
                                    {!! Form::select('status',$statuses,session('invoicesbatches_filter.status'),['class'=>'form-control'])!!}
                                </div>
                                
                            <button class="btn btn-success" type="submit" ><i class="fa fa-filter"></i> Enter</button>
                             <a href="{{ route('invoicesbatches.resetfilter') }}" class="btn btn-danger">Clear</a>
                            {!! Form::close() !!}
                           
                        </div>
                        @if (Session::has('message'))
                            <div class="col-xs-12 message-box">
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                            </div>
                        @endif
                    
                         
                        @if(count($invoicesbatches) > 0 )
                        <div class="col-xs-12">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Invoice Batch Number</th>
                                    <th>Client Name</th>
                                    <th>Invoices Count</th>
                                     
                                    <th>Status</th>
                                    <th>Batched Date</th>
                                    <th>Batched Total Amount</th>
                                   
                                    <th>Action</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoicesbatches as $batch)
                                <tr>
                                    <td> {{ $batch->id}}</td>
                                    <td>
                                        {{\App\Client::where('id',$batch->client_id)->first()->company_name
                                        }}
                                        
                                    </td>

                                   
                                    <td> {{ count(unserialize($batch->invoice_id))}}</td>
                                     
                                    <td> 
                                        @if ($batch->payed_at)
                                            Paid
                                        @else
                                            Unpaid
                                        @endif
                                    </td>
                                    <td> {{ $batch->created_at}}</td>
                                    <td> {{ $batch->total_amount}}</td>
                                    <td>
                                        @if ($batch->payed_at)
                                        <a class="btn btn-danger btn-xs btn-view"   disabled><i class="fa fa-close"></i> &nbsp;Delete&nbsp;</a>
                                        <a class="btn btn-success btn-xs btn-view"   disabled><i class="fa fa-money" ></i> &nbsp;Pay&nbsp;</a>
                                        @else
                                        <a class="btn btn-danger btn-xs btn-view btn-delete" href="{{ route('invoicesbatches.delete',$batch->id)}}"><i class="fa fa-close"></i> &nbsp;Delete&nbsp;</a>
                                        @if ($batch->total_amount>0)
                                        <a class="btn btn-success btn-xs btn-view btn-pay" href="{{ route('invoicesbatches.payment',$batch->id)}}"><i class="fa fa-money"></i> &nbsp;Pay&nbsp;</a>
                                        @else
                                        <a class="btn btn-success btn-xs btn-view"   disabled><i class="fa fa-money" ></i> &nbsp;Pay&nbsp;</a>
                                        @endif
                                        @endif
                                        <a class="btn btn-warning btn-xs btn-view" href="{{ route('invoicesbatches.printview',$batch->id)}}"><i class="fa fa-print"></i> &nbsp;Print&nbsp;</a>
                                    </td>
                                    
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        <div class="col-xs-12 text-center">
                            {{ $invoicesbatches->links() }}
                        </div>
                         
                        @else
                        <div class="col-xs-12">
                            <h5>No Invoices Batches found</h5>
                        </div>
                        @endif
                    
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
@endsection

@section('scripts')
<script>
$('.btn-pay').click(function(){
    $('.btn-pay').addClass("disabled");
    $('.btn-pay').css('pointer-events','none');
}); 
$('.btn-delete').click(function(){
    $('.btn-delete').addClass("disabled");
    $('.btn-delete').css('pointer-events','none');
}); 
$(function () {
    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
   
});
</script>
@endsection