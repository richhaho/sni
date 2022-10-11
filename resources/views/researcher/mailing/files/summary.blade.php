@extends('researcher.mailing.pdf')
@section('content')
<div id="page">
    <div class="content">
    <h1 class="text-center">BATCH SUMMARY REPORT</h1>
    <div>&nbsp;</div> 
    <h3 class="">BATCH ID: {{$batch_id}}</h3>
    <div>&nbsp;</div> 
    
    <!-- Start Loop -->
    @foreach($table as $mailing_type => $r)
    <div class="row square-box">
         <div class=" col-12">Type: {{ $mailing_types[$mailing_type]}}</div>
    </div>
    <div class="square-box-no-top">
    <div class="row text-bold">
         <div class="col-2 text-right">Total:</div>
         <div class="col-2">&nbsp;</div>
         <div class="col-2 text-center">Rate:</div>
         <div class="col-2">&nbsp;</div>
         <div class="col-2 text-left">Amount:</div>
         <div class="col-2">&nbsp;</div>
    </div>
    <div class="row text-bold">
         <div class="col-2 text-right"> {{ $r['count'] }} </div>
         <div class="col-2 text-center">x</div>
         <div class="col-2 text-center">${{ number_format($r['rate'],2) }}</div>
         <div class="col-2 text-center">=</div>
         <div class="col-2 text-left">${{ number_format($r['amount'],2) }}</div>
         <div class="col-2">&nbsp;</div>
    </div>   
    </div>
    <div>&nbsp;</div> 
    @endforeach
    <div>&nbsp;</div> 
    
    <p><h3><span class="warning">Total Postage:</span> ${{ number_format($total_postage,2) }}</h3></p>
    <div>&nbsp;</div> 
    <p><h3><span class="warning">Total Notice:</span> {{ $total_notices }}</h3></p>
    <div>&nbsp;</div> 
    <div>&nbsp;</div> 
    <div>&nbsp;</div> 
    
    <div class="row">
    <div class="col-4 text-bold">$ ___________________</div>
    <div class="col-4 text-bold">$ ___________________</div>
    <div class="col-4 text-bold">$ ___________________</div>
    </div>
    <div class="row">
    <div class="col-4 text-bold">Beginning Postage</div>
    <div class="col-4 text-bold">Amount Purchase</div>
    <div class="col-4 text-bold">Ending Postage</div>
    </div>
    </div>
    <div class="footer">
            <div class="last-l1">
            <div class="row" style="clear: both;">
                <div class="col-12">On: {{ Carbon\Carbon::now()->format('m-d-Y H:i:s a') }}</div>
            </div>
                </div>
            <div class="last-l2">
       <div class="row" style="clear: both;">
           <div class="col-6">Printed by: {{ Auth::user()->full_name}}</div>
           <div class="col-6 text-right">Page 1 of 1</div>
       </div>
       </div>
    </div>
</div>
@endsection