@extends('researcher.layouts.app')

@section('navigation')
    @include('researcher.navigation')
@endsection
@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
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
    
    input[name="daterange"] {
            min-width: 180px;
    }
</style>
@endsection

@section('content')
            <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="page-header col-xs-12">
                        <div class="col-xs-12  col-md-10">
                            <h1 class="" > Invoices</h1>
                        </div>
                        <div class="col-xs-12 col-md-2">
                            <div class="col-md-12 ">
                                @if(count($clients) == 0)
                                <a class="btn btn-success pull-right" href="{{route('invoices.create')}}" id="add-template"><i class="fa fa-plus"></i> Add Invoice</a>
                                @endif
                            </div>
                        </div>
                        
                    </div>
                         <div class="col-xs-12" id="filters-form">
                            {!! Form::open(['route' => 'invoices.setfilter', 'class'=>'form-inline'])!!}
                                @if(count($clients) > 0)
                                <div class="form-group">
                                    <label for="client_filter">Client: </label>
                                    {!! Form::select('client_filter',$clients,session('invoice_filter.client'),['class'=>'form-control select2'])!!}
                                </div>
                                @endif
                                <div class="form-group">
                                    <label for="job_type_filter"> Job: </label>
                                    {!! Form::select('job',$jobs,session('invoice_filter.job'),['class'=>'form-control select2'])!!}
                                </div>
                                <div class="form-group">
                                    <label for="job_status_filter"> Status: </label>
                                    {!! Form::select('status',$statuses,session('invoice_filter.status'),['class'=>'form-control'])!!}
                                </div>
                                <div class="form-group">
                                        <label for="job_amount_filter"> Amount: </label>
                                        {!! Form::text('amount',session('invoice_filter.amount'),['class'=>'form-control'])!!}
                                    </div>
                            <button class="btn btn-success" type="submit" ><i class="fa fa-filter"></i> Filter</button>
                             <a href="{{ route('invoices.resetfilter') }}" class="btn btn-danger">Reset</a>
                            {!! Form::close() !!}
                           
                        </div>
                    
                         @if (Session::has('message'))
                            <div class="col-xs-12 message-box">
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                            </div>
                        @endif
                    
                        
                        @if(count($invoices) > 0 )
                        <div class="col-xs-12">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Invoice Number</th>
                                    <th>Client</th>
                                    <th>Work Order Number</th>
                                    <th>Job Name</th>
                                    <th>Status</th>
                                    <th>Total Amount</th>
                                    <th class="col-xs-2">Actions</th>
                                    @if(session('invoice_filter.client')>0)

                                    <th class="col-xs-1">Pay</th>
                                    
                                    @endif
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoices as $invoice)
                                <tr>
                                    <td> {{ $invoice->number  }}</td>
                                    <td> {{ $invoice->client ? $invoice->client->company_name : 'N/A' }}</td>
                                    
                                    <td> {!! $invoice->work_order ?  '<a href="' . route('workorders.edit',$invoice->work_order->id) .  '">' . $invoice->work_order->number .'</a>' : 'N/A' !!}</td>
                                    <td> {{ $invoice->work_order ?  $invoice->work_order->job->name : 'N/A' }}</td>
                                    <td> {{ ($invoice->status=="paid") ? 'Paid' : 'Unpaid' }}</td>
                                    <td> {{ $invoice->total_amount }}</td>
                                    <td>
                                        @component('researcher.invoices.components.deletemodal')
                                        @slot('id') 
                                            {{ $invoice->id }}
                                        @endslot
                                        @slot('invoice_company_name') 
                                            {{ $invoice->client ? $invoice->client->company_name : 'N/A' }}
                                        @endslot
                                        @endcomponent
                                        <a href="{{ route('invoices.edit',$invoice->id)}}" class="btn btn-success btn-xs"><i class="fa fa-pencil"></i> Edit</a>
                                    </td>
                                    @if(session('invoice_filter.client')>0)
                                    
              
                                    <td>
                                        @if($invoice->status== 'open' || $invoice->status== 'unpaid')
                                        <div class="checkbox checkbox-slider--b-flat">
                                        <label>
                                            <input class="topay" name="pay[{{$invoice->id}}]" type="checkbox" ><span></span>
                                        </label>

                                        </div>
                                        @endif
                                    </td>
                                    
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                         @if(session('invoice_filter.client')>0)
                         <div class="col-xs-12 text-right">
                          {{ Form::open(['route'=>['invoices.payment.bycheck'],'class'=>'paymentform inline-form'])}}
                             {{ Form::hidden('client_id',session('invoice_filter.client'))}}
                            
                                 <button type="submit" class="btn btn-success"><i class="fa fa-money"></i> Pay By Check</button>
                            
                             {{ Form::close() }}
                              </div>
                         <div>&nbsp;</div>
                         @if(App\Client::find(session('invoice_filter.client'))->payeezy_value)
                            <div class="col-xs-12 text-right">
                             {{ Form::open(['route'=>['invoices.payment'],'class'=>'paymentform inline-form'])}}
                             {{ Form::hidden('client_id',session('invoice_filter.client'))}}
                            
                            
                                 <button type="submit" class="btn btn-success"><i class="fa fa-money"></i> Pay By Credit Card </button>
                             
                             {{ Form::close() }}
                             </div>
                         @endif 
                       
                         @endif
                        <div class="col-xs-12 text-center">
                            {{ $invoices->links() }}
                        </div>
                        @else
                        <div class="col-xs-12">
                            <h5>No Invoices found</h5>
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
<script src="{{ asset('/vendor/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script>
$(function () {
    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
     $('.select2').select2();
     
     
   $('.topay').click(function() {
    if(this.checked) {
        $('.paymentform').append("<input type='hidden' class='paywo' name='" + $(this).attr("name") + "'>");
    } else {
        $('.paywo[name="' +  $(this).attr("name") + '"]').remove();
    }// There is no need for jQuery here
   })
});
</script>
@endsection