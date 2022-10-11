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
@media screen and (max-width: 500px) {
    .stepwizard-step p {
        display: none !important;
    }
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
                  <div class="stepwizard-step col-xs-3">
                      <a type="button" class="btn  btn-default btn-circle">1</a>
                      <p><small>Job/Contract Information & Workorder</small></p>
                  </div>
                  <div class="stepwizard-step col-xs-3"> 
                      <a type="button" class="btn btn-default btn-circle">2</a>
                      <p><small>Job/Contract Parties & Attachments</small></p>
                  </div>
                  <div class="stepwizard-step col-xs-3">    <a type="button" class="btn btn-success btn-circle" >3</a>
                      <p style="color: black"><small><strong>Payment</strong></small></p>
                  </div>
                  <div class="stepwizard-step col-xs-3">    <a type="button" class="btn btn-default btn-circle" >4</a>
                      <p><small>Confirmation</small></p>
                  </div>
              </div>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12">
                        <h3 class="page-header">New Notice - Submit Payment</h3>
                    </div>
                </div>
            </div>
            
            <div class="container-fluid">
                @if (count($errors) > 0)
                <div class ="row">
                    <div class="col-xs-12 message-box">
                        <div class="alert alert-danger">            
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
                <div class ="row">
                    <div class="col-xs-12 col-md-12">
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
                </div>
                <div class ="row">
                    @if(strlen($client->payeezy_value) == 0)
                    <div class ="col-xs-12 col-md-4">
                         <h4><i class="fa fa-lock"></i> You are safe. We take privacy very seriously<br> <small>We accept:</small></h4>
                            <img  class="img-responsive col-xs-12 " src="{{ asset('/images/cclogos.jpg') }}" alt=""/>
                            <p>&nbsp;</p>
                            <p>Your credit card information is never stored on our servers. 
                                This form will be processed through a secure channel to Payeezy 
                                for payment processing.</p>
                            <p>&nbsp;</p>
                            
                            <img  class="img-responsive col-xs-12 " src="{{ asset('/images/payeezylogo.png') }}" alt=""/>
                    </div> 
                    <div class="col-xs-12 col-md-8">
                        <form id="form">
                        {{ Form::hidden('invoice_id', $invoice->id) }}
                        {{ Form::hidden('donottokenize',true) }}
                        {{ Form::hidden('apikey', $api_key, ['id'=>'apikey']) }}
                        {{ Form::hidden('apisecret', $api_secret, ['id'=>'apisecret']) }}
                        
                        {{ Form::hidden('currency', 'USD',['id'=>'currency','payeezy-data'=>'currency'])}}
                            <div id="payment-errors" class="alert alert-danger hidden" >
                                <span></span>
                            </div>
                            <div id="payment-success" class="alert alert-success hidden">
                                <span></span>
                            </div>
                            <div id="response_msg" class="alert alert-success hidden">
                                <span></span>
                            </div>
                            <div id="response_note" class="alert alert-info hidden">
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
                                        <span class="btn__loader" style="display:none;">loading...</span>Pay <span data-card-type></span>
                                    </button>
                                </div>
                            </div>
                            </div>

                        </form>
                    </div>
                    @else
                    <div class ="col-xs-12 col-md-4">
                         <h4><i class="fa fa-lock"></i> Payment Processing</h4>
                            <p>We will process your payment using a token retrieved 
                                from our payment gateway. Credit Card information is 
                                never stored on our servers, we use Payeezy for payment processing.</p>
                            <p>&nbsp;</p>
                           
                            <img  class="img-responsive col-xs-12 " src="{{ asset('/images/payeezylogo.png') }}" alt=""/>
                    </div> 
                    <div class="col-xs-12 col-md-8">
                        <form method="POST" name="payment-form" id="payment-form">
                            <div id="payment-errors" class="alert alert-danger hidden" >
                                <span></span>
                            </div>
                            <div id="response_msg" class="alert alert-success hidden">
                                <span></span>
                            </div>
                            <div id="response_note" class="alert alert-info hidden">
                                <span></span>
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
            </div>
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
     
    // make payment
    $('#payment-form').submit(function(e) {
        $('#pay-button').attr('disabled','true');
        $('#response_msg').addClass('hidden');
        $('#response_note').addClass('hidden');
        $('#payment-errors').addClass('hidden');
        $('#response_msg > span').html('');
        $('#response_note  > span').html('');
        $('#payment-errors > span').html('');

        var $form = $('#payment-form');
       
        var serializedData = $form.serializeArray();
       
        $.ajax({
            url: "{{ url('client/wizard2/purchase') }}",
            type: "get",
            data: $.param(serializedData),
            success: function(data) {
                        var obj = jQuery.parseJSON(data);
                       window.location.replace('{{ url('/client/wizard2/payment/') }}/' + obj.id +'/' + obj.status);
                     },
            error: function() {
                $('#payment-errors > span').html('We had problems Submitting your payment please try again, or Contact us. ');
                 $('#payment-errors').removeClass('hidden');
                 $form.find('button').prop('disabled', false);
                 $('#pay-button').attr('disabled','false');
            }
        });
        return false;
    });
 
});

</script>

<script src="https://docs.paymentjs.firstdata.com/lib/{{env('PAYMENTJS_LIBRARY_ID')}}/client-2.0.0.js"></script>
<script type="text/javascript">
const DomUtils = {
  getEl: (selector) => window.document.querySelector(selector),

  hasClass: (el, cssClass) => {
    if (el.classList) {
      return el.classList.contains(cssClass);
    }
    return !!el.className.match(new RegExp(`(\\s|^)${cssClass}(\\s|$)`));
  },

  removeClass: (el, cssClass) => {
    if (el.classList) {
      el.classList.remove(cssClass);
    } else if (DomUtils.hasClass(el, cssClass)) {
      const reg = new RegExp(`(\\s|^)${cssClass}(\\s|$)`);
      el.className = el.className.replace(reg, ' ');
    }
  },
};

const SubmitButton = {
  buttonElement: DomUtils.getEl('[data-submit-btn]'),
  loaderElement: DomUtils.getEl('.btn__loader'),

  enable: () => {
    SubmitButton.buttonElement.disabled = false;
    DomUtils.removeClass(SubmitButton.buttonElement, 'disabled-bkg');
  },

  setSubmitState: () => {
    SubmitButton.buttonElement.disabled = true;
    SubmitButton.loaderElement.style.display = 'inline-block';
  },

  removeSubmitState: () => {
    SubmitButton.buttonElement.disabled = false;
    SubmitButton.loaderElement.style.display = 'none';
  }
};

const config = {
  fields: {
    card: {
      selector: '[data-cc-card]',
    },
    cvv: {
      selector: '[data-cc-cvv]',
    },
    exp: {
      selector: '[data-cc-exp]',
    },
    name: {
      selector: '[data-cc-name]',
      placeholder: 'Full Name',
    },
  },

  // css classes to be injected into the iframes.
  // the properties allowed are restricted via whitelist.
  // further, unrestricted styling can be applied to the div's in which the iframes are injected.
  styles: {
    input: {
      'font-size': '16px',
      color: '#00a9e0',
      'font-family': 'monospace',
      background: 'black',
    },
    '.card': {
      'font-family': 'monospace',
    },
    ':focus': {
      color: '#00a9e0',
    },
    '.valid': {
      color: '#43B02A',
    },
    '.invalid': {
      color: '#C01324',
    },
    '@media screen and (max-width: 700px)': {
      input: {
        'font-size': '18px',
      },
    },
    'input:-webkit-autofill': {
      '-webkit-box-shadow': '0 0 0 50px white inset',
    },
    'input:focus:-webkit-autofill': {
      '-webkit-text-fill-color': '#00a9e0',
    },
    'input.valid:-webkit-autofill': {
      '-webkit-text-fill-color': '#43B02A',
    },
    'input.invalid:-webkit-autofill': {
      '-webkit-text-fill-color': '#C01324',
    },
    'input::placeholder': {
      color: '#aaa',
    },
  },

  // these values correspond to css class names defined above
  classes: {
    empty: 'empty',
    focus: 'focus',
    invalid: 'invalid',
    valid: 'valid',
  },
};

function authorizeSession(callback) {
  let request = new XMLHttpRequest();
  request.onload = () => {
    if (request.status >= 200 && request.status < 300) {
      callback(JSON.parse(request.responseText));
    } else {
      throw new Error("error response: " + request.responseText);
    }
    request = null;
  };
  request.open("POST", "/api/authorize-session", true);
  request.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
  request.send();
}

const hooks = {
  preFlowHook: authorizeSession,
};

const onCreate = (paymentForm) => {
  const onSuccess = (clientToken) => {
    $('#payment-success > span').html("Tokenization request sent! Please wait while review result.");
    $('#payment-success').removeClass('hidden');
    
    setTimeout(
      function()
      {
        $('#payment-success').addClass('hidden');
        $.get('/api/verify-tokenize-response?client_token='+clientToken, function(data){
          if (data.status=='success'){
            $('#payment-success > span').html(data.description);
            $('#payment-success').removeClass('hidden');
          }else{
            $('#payment-errors > span').html(data.description);
            $('#payment-errors').removeClass('hidden');
          }
          
            var $form = $('#form');
       
            var serializedData = $form.serializeArray();
            console.log(serializedData);
            $.ajax({
                url: "{{ url('client/wizard2/purchase') }}",
                type: "get",
                data: $.param(serializedData),
                success: function(data) {
                        var obj = jQuery.parseJSON(data);
                        window.location.replace('{{ url('/client/wizard2/payment/') }}/' + obj.id +'/' + obj.status);
                },
                error: function() {
                    $('#payment-errors > span').html('We had problems Submitting your payment please try again, or Contact us. ');
                     $('#payment-errors').removeClass('hidden');
                     $form.find('button').prop('disabled', false);
                     $('#pay-button').attr('disabled','false');
                }
            });

        });
      }, 3000);
  };

  const onError = (error) => {
    $('#payment-errors > span').html("Tokenization request error: \"" + error.message + "\"");
    $('#payment-errors').removeClass('hidden');
    setTimeout(
      function()
      {
        SubmitButton.removeSubmitState();
        paymentForm.reset(() => {});
        $('#payment-errors').addClass('hidden');
      }, 3000);
  };
  const form = DomUtils.getEl('#form')
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    SubmitButton.setSubmitState();
    paymentForm.onSubmit(onSuccess, onError);
  });

  const ccFields = window.document.getElementsByClassName('payment-fields');
  for (let i = 0; i < ccFields.length; i++) {
    DomUtils.removeClass(ccFields[i], 'disabled');
  }
  SubmitButton.enable();
};

window.firstdata.createPaymentForm(config, hooks, onCreate);

</script>
@endsection