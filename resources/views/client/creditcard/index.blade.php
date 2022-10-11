@extends('client.layouts.app')

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
<style>
    .table > tbody > tr > .no-line {
      border-top: none;
  }

  .table > thead > tr > .no-line {
      border-bottom: none;
  }

  .table > tbody > tr > .thick-line {
      border-top: 2px solid;
  }
</style>

@endsection

@section('navigation')
    @include('client.navigation')
@endsection

@section('content')
    
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">
                    <h1 class="page-header">Change Credit Card
                        
                    <div class="pull-right">

                    </div>
                       
                </h1> 
                    
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
                {!! Form::open(['route' => 'client.creditcard.remove', 'class'=>'form-inline'])!!}
                @if(strlen(Auth::user()->client->payeezy_value) == 0)
                 <div class ="col-xs-4">
                     <h4><i class="fa fa-lock"></i> You are safe, We take privacy very seriously<br> <small>We accept:</small></h4>
                        <img  class="img-responsive col-xs-12 " src="{{ asset('/images/cclogos.jpg') }}" alt=""/>
                        <p>&nbsp;</p>
                        <p>Your credit card information   will never be stored on our servers, this form will be processed through a secure channel to Payeezy 
                            for payment processing and it will return a token for further payment processing.</p>
                        <img  class="img-responsive col-xs-12 " src="{{ asset('/images/payeezylogo.png') }}" alt=""/>
                             

                    </div>

               
                @else
                <div class ="col-xs-4">
                    <h4><i class="fa fa-credit-card"></i> We have a token on file corresponding to the following Information:</h4>
                    <p> 
                        <label>Card Type: </label> {{ Auth::user()->client->payeezy_type }} <br>
                        <label>Card Holder Name: </label> {{ Auth::user()->client->payeezy_cardholder_name }} <br>
                        <label>Card Expiration Date: </label> {{ strftime("%b",mktime(0,0,0,substr(Auth::user()->client->payeezy_exp_date,0,2))) }} - {{substr(Auth::user()->client->payeezy_exp_date,2,2) }} <br>
                     <h4><i class="fa fa-lock"></i> New Credit Card Information</h4>

                        <p>We will send your Credit Card Information through a secure channel to 
                            retrieve a token from our payment gateway, that we will use for future payments.</p>
                        <img  class="img-responsive col-xs-12 " src="{{ asset('/images/payeezylogo.png') }}" alt=""/>
                            @if(count($cardslist)>0)
                            <div class="hidden">
                                <button type="submit" class=" btn btn-danger btn-block  form-control">Remove Card</button>
                            </div>
                            @endif
                     
                </div> 
                
                @endif
                {!! Form::close() !!}
                <div class="col-xs-8">
                    <form id="form">
                        <div id="payment-errors" class="alert alert-danger hidden" >
                            <span></span>
                        </div>
                        <div id="payment-success" class="alert alert-success hidden">
                            <span></span>
                        </div>
                    
                        <div class="row">
                            <div class="col-xs-12 form-group">
                                <label for="cc-name" class="control-label">Card Holder Name:</label>
                                <div class="form-control payment-fields disabled" id="cc-name" data-cc-name></div>
                            </div>
                        </div>
                    
                        <div class="row">
                            <div class="col-xs-12 form-group">
                                <label for="cc-card" class="control-label">Card Number:</label>
                                <div class="form-control payment-fields disabled empty" id="cc-card" data-cc-card></div>
                            </div>
                        </div>
                    
                        <div class="row">
                            <div class="col-xs-12 form-group">
                                <label for="cc-cvv" class="control-label">CVV Code:</label>
                                <div class="form-control payment-fields disabled empty" id="cc-cvv" data-cc-cvv></div>
                            </div>
                        </div>
                    
                        <div class="row">
                            <div class="col-xs-12 form-group">
                                <label for="cc-exp" class="control-label">Expiry Date:</label>
                                <div class="form-control payment-fields disabled empty" id="cc-exp" data-cc-exp></div>
                            </div>
                        </div>
                    
                        <div class="row">
                        <div class="col-xs-12 ">
                            <div class="col-xs-4 pull-right">
                                <button id="submit" class="btn btn-success form-control btn--primary disabled-bkg" data-submit-btn disabled>
                                    <span class="btn__loader" style="display:none;">loading...</span>Add Card <span data-card-type></span>
                                </button>
                            </div>
                        </div>
                        </div>

                    </form>
                </div>
            </div>
            <!-- /.container-fluid -->
            <div class="container-fluid">
                <div class ="col-xs-12">
                    <center><h3 class="print_invoicestitle"> Cards List</h3></center>    
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="15%">Card Type</th>
                                    <th width="50%">Card Name</th>
                                    <th width="10%">Expiration Date</th>
                                    <th width="25%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if(strlen(Auth::user()->client->payeezy_value) > 0)
                                <tr>
                                    <td>{{Auth::user()->client->payeezy_type}}</td>
                                    <td>{{Auth::user()->client->payeezy_cardholder_name}}</td>
                                    <td> {{ strftime("%b",mktime(0,0,0,substr(Auth::user()->client->payeezy_exp_date,0,2))) }} - {{substr(Auth::user()->client->payeezy_exp_date,2,2) }}</td>
                                    <td>
                                        <button type="submit" class=" btn form-control" disabled>Current Card to use</button>
                                        @if(count($cardslist)>0 && 1==2)
                                        {!! Form::open(['route' => 'client.creditcard.remove', 'class'=>'form-inline '])!!}
                                        <button type="submit" class="btn btn-danger form-group" style="width: 100% !important">Remove Card</button>
                                        {!! Form::close() !!}
                                        @endif
                                        
                                    </td>
                                </tr>
                            @endif
                            @foreach($cardslist as $card)
                                <tr>
                                    <td>{{$card->payeezy_type}}</td>
                                    <td>{{$card->payeezy_cardholder_name}}</td>
                                    <td> {{ strftime("%b",mktime(0,0,0,substr($card->payeezy_exp_date,0,2))) }} - {{substr($card->payeezy_exp_date,2,2) }}</td>
                                     
                                    <td>
                                        
                                        <a href="{{route('client.creditcard.active_card')}}?id={{$card->id}}" class=" btn btn-success ">Use this Card</a>
                                        @if(count($cardslist)>1 || Auth::user()->client->payeezy_type)
                                        <a href="{{route('client.creditcard.remove_card')}}?id={{$card->id}}" class=" btn btn-danger">Remove Card</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                </div>
            </div>                
            <!-- /.container-fluid -->
            
        </div>
   
@endsection

@section('scripts')
<script src="{{ asset('/vendor/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="https://docs.paymentjs.firstdata.com/lib/{{env('PAYMENTJS_LIBRARY_ID')}}/client-2.0.0.js"></script>
<script type="text/javascript" src="{{ asset('/vendor/paymentjs/js/client.js') }}"></script>
<script>
$.fn.select2.defaults.set("theme", "bootstrap");
</script>
    
@endsection