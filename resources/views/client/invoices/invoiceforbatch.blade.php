@extends('client.layouts.app')

@section('navigation')
    @include('client.navigation')
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
     
    
    input[name="daterange"] {
            min-width: 180px;
    }
    input[type="checkbox"]{
        margin-top: -14px !important;
    }
</style>
@endsection

@section('content')
            <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="page-header col-xs-12">
                        <div class="col-xs-12  col-md-12">
                            <h1 class="" >Generate Batch Invoices</h1>
                             
                            <div class="pull-right">
                            
                             @if(count($invoices) > 0 )
                             <button class="btn btn-success btn-pay" type="button" form="pay-form"> <i class="fa fa-arrow-right"></i>&nbsp;&nbsp; Generate Batch</button> 
                             @endif
                            </div>
                             
                        </div>
                        
                        
                    </div>
                         
                    
                         @if (Session::has('message'))
                            <div class="col-xs-12 message-box">
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                            </div>
                        @endif
                    
                         {!! Form::open(['route' => 'client.invoices.tobatch', 'class'=>'form-inline', 'id' => 'pay-form'])!!}
                        @if(count($invoices) > 0 )
                        <div class="col-xs-12">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Invoice Number</th>
                                    
                                    <th>Work Order Number</th>
                                    <th>Job Number</th>
                                    <th>Job Name</th>
                                    <th>Job Address</th>
                                    <th>Customer Name</th>
                                    <th>Entered Date</th>
                                    <th>Completed Date</th>

                                    <th>Total Amount</th>
                                    <th>Status</th>
                                     
                                    <th class="col-xs-1">
                                        Select All
                                        <div class="checkbox checkbox-slider--b-flat">
                                        <label>
                                            <input id="select_all"  type="checkbox" class="selection"><span></span>
                                        </label>
                                            
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoices as $invoice)
                                <tr>
                                    <td> {{ $invoice->number  }}</td>
                                   
                                    <td> {!! $invoice->work_order ?  '<a href="' . route('client.notices.edit',$invoice->work_order->id) .  '">' . $invoice->work_order->number .'</a>' : 'N/A' !!}</td>
                                    <td> {{ $invoice->work_order ?  $invoice->work_order->job->number : 'N/A' }}</td>
                                    <td> {{ $invoice->work_order ?  $invoice->work_order->job->name : 'N/A' }}</td>
                                    <td> {{ $invoice->work_order ?  str_replace('<br>', ' ',str_replace('<br />', ' ',$invoice->work_order->job->full_address_no_country)) : 'N/A' }}</td>
                                    <td> 
                                        <?php 
                                                    $firm_name='';
                                                    $name='';
                                                    $address='';
                                                    $email='';
                                                    $customer=$invoice->work_order->job->parties->where('type','customer')->first();
                                                    if (count($customer)>0){
                                                        $customer_contact= $customer->contact;
                                                        if(count($customer_contact)>0){
                                                            $firm_name=$customer->firm->firm_name;
                                                            $name=$customer->firm->fullname;
                                                            $address=$customer_contact->address1.' '.$customer_contact->address2.' '.$customer_contact->city.' , '.$customer_contact->state.' '.$customer_contact->zip;

                                                            $phone=$customer_contact->phone;
                                                            $email=$customer_contact->email;

                                                        };
                                                    } echo $firm_name;


                                                    ?>

                                    </td>
                                    <td>{{date('m/d/Y', strtotime($invoice->work_order->created_at))}}</td>
                                    <td>
                                        @if ($invoice->work_order->status=='completed')
                                        {{date('m/d/Y', strtotime($invoice->work_order->updated_at))}}
                                        @endif
                                    </td>
                                    <td> {{ $invoice->total_amount }}</td>
                                    
                                    @if($invoice->status == "unpaid")
                                        @if($invoice->client->billing_type == "invoiced") 
                                        <td> {{ $invoice->status }}</td>
                                        @else 
                                        <td>payment pending</td>
                                        @endif
                                    @else

                                        <td> {{ $invoice->status }}
                                            <p>
                                            <?php
                                            $pm=\App\Payment::where('id',$invoice->payment_id)->first();
                                            $p_type=[
                                                'credit_card'=>'Credit Card',
                                                'pay_by_check'=>'Check'

                                            ];

                                            if (count($pm)>0){
                                                echo "(".$p_type[$pm->type].")";
                                            }
                                            ?>
                                        </p>

                                        </td>
                                    @endif
                                    
                                    <td>
                                        @if($invoice->status== 'open' || $invoice->status== 'unpaid')
                                        <div class="checkbox checkbox-slider--b-flat">
                                        <label>
                                            <input name="pay[{{$invoice->id}}]"  type="checkbox" class="selection"><span></span>
                                        </label>
                                            
                                    </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        <div class="col-xs-12 text-center">
                            {{ $invoices->links() }}
                        </div>
                        {{Form::close()}}
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
<script>
$('.btn-pay').click(function(){
    $('#pay-form').submit();
    $('.btn-pay').addClass("disabled");
    $('.btn-pay').css('pointer-events','none');
}); 
$(function () {
    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
    $('#select_all').change(function() {
        var checkboxes = $('.selection');
        if($(this).is(':checked')) {
            checkboxes.prop('checked', true);
        } else {
            checkboxes.prop('checked', false);
        }
    });
   
});
</script>
@endsection