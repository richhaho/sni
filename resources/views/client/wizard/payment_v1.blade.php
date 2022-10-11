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
.stepwizard-step p {
    margin-top: 0px;
    color:#666;
}
.stepwizard-row {
    display: table-row;
}
.stepwizard {
    display: table;
    width: 100%;
    position: relative;
    pointer-events:none;
}

.stepwizard-row:before {
    top: 14px;
    bottom: 0;
    position: absolute;
    content:" ";
    width: 100%;
    height: 1px;
    background-color: #ccc;
    z-index: 0;
}
.stepwizard-step {
    display: table-cell;
    text-align: center;
    position: relative;
}
.btn-circle {
    width: 30px;
    height: 30px;
    text-align: center;
    padding: 6px 0;
    font-size: 12px;
    line-height: 1.428571429;
    border-radius: 15px;
}
</style>

@endsection

@section('navigation')
    @include('client.navigation')
@endsection

@section('content')
    
        <div id="top-wrapper" >
                <br>
                <div class="stepwizard">
                  <div class="stepwizard-row setup-panel">
                      <div class="stepwizard-step col-xs-2">
                          <a type="button" class="btn  btn-default btn-circle">1</a>
                          <p><small>Job/Contract Information</small></p>
                      </div>
                      <div class="stepwizard-step col-xs-2"> 
                          <a type="button" class="btn btn-default btn-circle">2</a>
                          <p><small>Job/Contract Parties</small></p>
                      </div>
                      <div class="stepwizard-step col-xs-2">    <a type="button" class="btn btn-default btn-circle">3</a>
                          <p><small>Attachments</small></p>
                      </div>
                      <div class="stepwizard-step col-xs-2">    <a type="button" class="btn btn-default btn-circle" >4</a>
                          <p><small>Document to Order</small></p>
                      </div>
                      <div class="stepwizard-step col-xs-2">    <a type="button" class="btn btn-success btn-circle" >5</a>
                          <p style="color: black"><small><strong>Payment</strong></small></p>
                      </div>
                      <div class="stepwizard-step col-xs-2">    <a type="button" class="btn btn-default btn-circle" >6</a>
                          <p><small>Confirmation</small></p>
                      </div>
                  </div>
                </div>
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">
                    <h1 class="page-header">New Notice - Submit Payment
                        
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
                <div class ="col-xs-12">
                    <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><strong>Invoice summary</strong></h3>
                                </div>
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        <table class="table table-condensed">
                                            <thead>
                                            <tr>
                                                <td><strong>Item</strong></td>
                                                <td class="text-center col-xs-2"><strong>Price</strong></td>
                                                <td class="text-center"><strong>Quantity</strong></td>
                                                <td class="text-right"><strong>Totals</strong></td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($invoice->lines as $line)
                                                <tr>
                                                        <td>{{$line->description}}</td>
                                                        <td class="text-center">${{ number_format($line->price,2)}}</td>
                                                        <td class="text-center">{{ $line->quantity }}</td>
                                                        <td class="text-right">${{ number_format($line->amount,2)}}</td>
                                                </tr>
                                                @endforeach
                                                <tr>
                                                        <td class="thick-line"></td>
                                                        <td class="thick-line"></td>
                                                        <td class="thick-line text-center"><strong>Total</strong></td>
                                                        <td class="thick-line text-right">${{number_format($invoice->total_amount,2)}}</td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                            </div>
                                
                        </div>
                </div>
                @if(strlen($client->payeezy_value) == 0)
                 <div class ="col-xs-4">
                     <h4><i class="fa fa-lock"></i> You are safe. We take privacy very seriously<br> <small>We accept:</small></h4>
                        <img  class="img-responsive col-xs-12 " src="{{ asset('/images/cclogos.jpg') }}" alt=""/>
                        <p>&nbsp;</p>
                        <p>Your credit card information is never stored on our servers. 
                            This form will be processed through a secure channel to Payeezy 
                            for payment processing.</p>
                        <p>&nbsp;</p>
                        
                        <img  class="img-responsive col-xs-12 " src="{{ asset('/images/payeezylogo.png') }}" alt=""/>
                    </div> 
                <div class="col-xs-8">
                   
                    <form method="POST" name="payment-form" id="payment-form">
                    {{ Form::hidden('invoice_id', $invoice->id) }}
                    {{ Form::hidden('apikey', $api_key, ['id'=>'apikey']) }}
                    {{ Form::hidden('apisecret', $api_secret, ['id'=>'apisecret']) }}
                    {{ Form::hidden('js_security_key', $js_security_key, ['id'=>'js_security_key']) }}

                    {{ Form::hidden('ta_token', $ta_token, ['id'=>'ta_token','payeezy-data'=>'ta_token'])}}
                    {{ Form::hidden('currency', 'USD',['id'=>'currency','payeezy-data'=>'currency'])}}
                    {{ Form::hidden('auth', 'true',['id'=>'auth','payeezy-data'=>'auth'])}}
                    {{ Form::hidden('cc_token', '')}}
                  
                        <div id="payment-errors" class="alert alert-danger hidden" >
                                <span ></span>
                        </div>
                        <div id="response_msg class="alert alert-success hidden">
                                <span "></span>
                        </div>
                       <div id="response_note" class="alert alert-info hidden">
                                <span ></span>
                        </div>
                        <div id="someHiddenDiv" style="display: none; color: red">Requesting
                                Payeezy token...</div>
                    
                        <div class="row">
                            <div class="col-xs-12 form-group">
                                <label>Card Type :</label>
                               <select payeezy-data="card_type" class="form-control">
                                    <option value="visa">Visa</option>
                                    <option value="mastercard">Master Card</option>
                                    <option value="American Express">American Express</option>
                                    <option value="discover">Discover</option>
                                </select>
                            </div>
                        </div>
                    
                        <div class="row">
                            <div class="col-xs-12 form-group">
                                <label>Cardholder Name :</label>
                                <input type="text" payeezy-data="cardholder_name" class="form-control" value="" />
                            </div>
                        </div>
                    
                        <div class="row">
                            <div class="col-xs-12 form-group">
                                <label>Card Number :</label>
                                <input type="text" payeezy-data="cc_number" class="form-control"  value="" />
                            </div>
                        </div>
                    
                        <div class="row">
                            <div class="col-xs-12 form-group">
                                <label>CVV Code :</label>
                                <input type="text" payeezy-data="cvv_code"  class="form-control" />
                            </div>
                        </div>
                    
                        <div class="row">
                            <div class="col-xs-4 form-group">
                                <label>Expiry Date :</label>
                                <select payeezy-data="exp_month" class=" form-control">
                                    <option value="01">01</option>
                                    <option value="02">02</option>
                                    <option value="03">03</option>
                                    <option value="04">04</option>
                                    <option value="05">05</option>
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12" selected>12</option>
                                </select> 
                            </div>
                            <div class="col-xs-4 form-group">
                                <label>&nbsp;</label>
                                <select payeezy-data="exp_year" class=" form-control">
                                    
                                    <option value="18">2018</option>
                                    <option value="19">2019</option>
                                    <option value="20">2020</option>
                                    <option value="21">2021</option>
                                    <option value="22">2022</option>
                                    <option value="23">2023</option>
                                    <option value="24">2024</option>
                                    <option value="25">2025</option>
                                    <option value="26">2026</option>
                                    <option value="27">2027</option>
                                    <option value="28">2028</option>
                                    <option value="29">2029</option>
                                    <option value="30">2030</option>
                                    <option value="31">2031</option>
                                    <option value="32">2032</option>
                                    <option value="33">2033</option>
                                    <option value="34">2034</option>
                                    <option value="35">2035</option>
                                    <option value="36">2036</option>
                                    <option value="37">2037</option>
                                    <option value="38">2038</option>
                                    <option value="39">2039</option>
                                    <option value="40">2040</option>
                                </select>
                            </div>
                        </div>
                    
                        <div class="row">
                        <div id="generate-token" class="col-xs-12 ">
                            <p class="text-right"><strong>There may be additional costs for postage at notice completion.</strong></p>
                            <div class="col-xs-4 pull-right">
                                <div class="">
                                <button type="submit" id="pay-button" class=" btn btn-success btn-block  form-control">Pay</button>
                                </div>
                            </div>
                        </div>
                        </div>

                    </form>
                </div>
                @else
                <div class ="col-xs-4">
                     <h4><i class="fa fa-lock"></i> Payment Processing</h4>

                        <p>We will process your payment using a token retrieved 
                            from our payment gateway. Credit Card information is 
                            never stored on our servers, we use Payeezy for payment processing.</p>
                        <p>&nbsp;</p>
                       
                        <img  class="img-responsive col-xs-12 " src="{{ asset('/images/payeezylogo.png') }}" alt=""/>
                    </div> 
                <div class="col-xs-8">
                     <form method="POST" name="payment-form" id="payment-form">
                         <div id="payment-errors" class="alert alert-danger hidden" >
                                <span ></span>
                        </div>
                        <div id="response_msg class="alert alert-success hidden">
                                <span "></span>
                        </div>
                       <div id="response_note" class="alert alert-info hidden">
                                <span ></span>
                        </div>
                    {{ Form::hidden('invoice_id', $invoice->id) }}
                    {{ Form::hidden('donottokenize',true) }}
                    {{ Form::hidden('apikey', $api_key, ['id'=>'apikey']) }}
                    {{ Form::hidden('apisecret', $api_secret, ['id'=>'apisecret']) }}
                    
                    {{ Form::hidden('currency', 'USD',['id'=>'currency','payeezy-data'=>'currency'])}}
                     <p class="text-right"><strong>There may be additional costs for postage at notice completion.</strong></p>
                    <div class="col-xs-4 pull-right">
                        <div class="">
                        <button type="submit"  id="pay-button" class=" btn btn-success btn-block  form-control">Pay</button>
                        </div>
                    </div>

                    </form>
                </div>
                @endif
               
            </div>
            <!-- /.container-fluid -->
            
        </div>
   
@endsection

@section('scripts')
<script src="{{ asset('/vendor/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/payeezy/js/payeezy_us_v5.1.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/bootstrap-filestyle/js/bootstrap-filestyle.min.js') }}" type="text/javascript"></script>
<script>
$.fn.select2.defaults.set("theme", "bootstrap");
var payeezy_url ='{{ $payeezy_url}}';

$(function () {
$(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
    $('[data-toggle="tooltip"]').tooltip()
    $('.date-picker').datepicker();
    $(":file").filestyle();
   

     

        $('#payment-form').submit(function(e) {
                    $('#pay-button').attr('disabled','true');
                    $('#response_msg').addClass('hidden');
                    $('#response_note').addClass('hidden');
                    $('#payment-errors').addClass('hidden');
                    $('#response_msg > span').html('');
                    $('#response_note  > span').html('');
                    $('#payment-errors > span').html('');

                    var $form = $(this);
                    //$form.find('button').prop('disabled', true);
                    if (! $('input[name="donottokenize"]').length) {
                        
                        var apiKey = document.getElementById("apikey").value;
                        var js_security_key = document.getElementById("js_security_key").value;
                        var auth = document.getElementById("auth").value;
                        var ta_token = document.getElementById("ta_token").value;
                        var currency=document.getElementById("currency").value;;//4242424242424242  123
                        Payeezy.setApiKey(apiKey);
                        Payeezy.setJs_Security_Key(js_security_key);
                        Payeezy.setTa_token(ta_token);
                        Payeezy.setAuth(auth);
                        Payeezy.setCurrency(currency);
                        Payeezy.createToken(responseHandler);

                        $('#someHiddenDiv').show();
                    } else {
                        
                        var $form = $('#payment-form');
                       
                        var serializedData = $form.serializeArray();
                       
                        $.ajax({
                            url: "{{ url('client/wizard/purchase') }}",
                            type: "get",
                            data: $.param(serializedData),
                            success: function(data) {
                                        var obj = jQuery.parseJSON(data);
                                       window.location.replace('{{ url('/client/wizard/payment/') }}/' + obj.id +'/' + obj.status);
                                     },
                            error: function() {
                                $('#payment-errors > span').html('We had problems Submitting your payment please try again, or Contact us. ');
                                 $('#payment-errors').removeClass('hidden');
                                 $form.find('button').prop('disabled', false);
                                 $('#pay-button').attr('disabled','false');
                            }
                        });
                         
                    }

                    return false;
                });
   
 
});


 var responseHandler = function(status, response) {

        var $form = $('#payment-form');
        $('#someHiddenDiv').hide();
        if (status != 201) {
             if (response.error && status != 400) {
               var error = response["error"];
               var errormsg = error["messages"];
               var errorcode = JSON.stringify(errormsg[0].code, null, 4);
               var errorMessages = JSON.stringify(errormsg[0].description, null, 4); 
               $('#payment-errors > span').html( 'Error Code:' + errorcode + ', Error Messages:'
                                + errorMessages);
             
                    $('#payment-errors').removeClass('hidden');
            }
            if (status == 400 || status == 500 || status == 401) {
               $('#payment-errors  > span').html('');
               var errormsg = response.Error.messages;
               var errorMessages = "";
               for(var i in errormsg){
                var ecode = errormsg[i].code;
                var eMessage = errormsg[i].description;
                errorMessages = errorMessages + 'Error Code:' + ecode + ', Error Messages:'
                                + eMessage;
                }
           
               $('#payment-errors  > span').html( errorMessages);
               $('#payment-errors').removeClass('hidden');

            }

            $form.find('button').prop('disabled', false);
        } else {
            $('#payment-errors  > span').html('');
            console.log(response.token);
            var result = response.token.value;
            console.log(result);
            $('#cc_token').val(result);
            var serializedData = $form.serializeArray();
            serializedData.push({
                name: 'token',
                value: JSON.stringify(response.token)
            });
            $.ajax({
                url: "{{ url('client/wizard/purchase') }}",
                type: "get",
                data: $.param(serializedData),
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    window.location.replace('{{ url('/client/wizard/payment/') }}/' + obj.id +'/' + obj.status);
                  },
                error: function() {
                    $('#payment-errors > span').html('We had problems Submitting your payment please try again, or Contact us. ');
                     $('#payment-errors').removeClass('hidden');
                     $form.find('button').prop('disabled', false);
                }
            });
            
        }

    };
</script>
    
@endsection