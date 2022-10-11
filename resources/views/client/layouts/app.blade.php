<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!--  <link href="{{ asset('css/app.css') }}" rel="stylesheet">-->
    
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/metisMenu/metisMenu.min.css') }}" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('vendor/sb-admin/css/sb-admin-2.css') }}" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="{{ asset('vendor/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('/vendor/typeahead/css/typeaheadjs.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('/vendor/checkbox-b-flat/css/checkbox-b-flat.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('/css/search.css')}}" rel="stylesheet" type="text/css"/>
    <style>
        .label-as-badge {
            border-radius: 1em;
        }
        .menu-badge {
            position: absolute;
            top: 6px;
            left: 29px;
        }
       
    </style>
    @yield('css')

</head>
<body>
    <div id="wrapper">
    @yield('navigation')
    
   
    <div id="right-container">
        
                
          
    @yield('content')
    </div>
    <!-- Scripts -->
    </div>
        <!--<script src="{{ asset('js/app.js') }}"></script>-->
        <script src="{{ asset('/vendor/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('/vendor/metisMenu/metisMenu.min.js') }}"></script>
        <script src="{{ asset('/vendor/sb-admin/js/sb-admin-2.js') }}"></script>
        <script src="{{ asset('/vendor/typeahead/js/typeahead.bundle.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('/vendor/socket-io/js/socket.io.js') }}"></script>
        <script src="{{ asset('/vendor/laravelecho/echo.js') }}"></script>
        <script src="{{ asset('/vendor/moment/js/moment.min.js') }}"></script>
        <script src="{{ asset('/vendor/moment/js/moment-timezone-with-data.min.js') }}"></script>
        <script src="{{ asset('/vendor/caret/caret.js') }}"></script>
<script type="text/javascript">
    var remove_notification_url = '{{ url ('client/notification')}}';
            
</script>
        <script src="{{ asset('/js/processnotifications.js') }}"></script>
        <script src="{{ asset('/js/sitesearch.js') }}"></script>
         <script src="{{ asset('/js/uppercase.js') }}"></script>

        <script>
            var search_url_loading = '{{route('search.loading')}}';
            var search_url_clients = '{{route('search.clients')}}';
            var search_url_associates = '{{route('search.associates')}}';
            var search_url_contacts = '{{route('search.contacts')}}';
            var search_url_jobs = '{{route('search.jobs')}}';
            var search_url_notes = '{{route('search.notes')}}';
            var search_url_attachments = '{{route('search.attachments')}}';
            var search_url_parties = '{{route('search.parties')}}';
            var remove_notification_url = '{{ url ('client/notification')}}';
            
            var module = { };
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            window.Echo = new Echo({
                broadcaster: 'socket.io',
                host: '{{env("ECHO_PROTOCOL","https")}}://{{env("ECHO_SERVER")}}:6001'
            });
           window.Echo.private('App.User.{{Auth::user()->id}}')
                   .notification((notification) => {
                       ProcessNotification(notification);
                    });

           
           var thisitis;
          


            $(function () {
                $('input').each(function(){
                            var xname = $(this).attr("name");
                           $(this).attr("autocomplete","new-" + xname);
                       });
                // var ua=navigator.userAgent,tem,M=ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || []; 
                // if (M[1]=='Chrome' || M[1]=='Firefox' ){
                //     $('.meesage-link').attr('href','#');
                //   }

            });

            jQuery.fn.preventDoubleSubmission = function() {
                $(this).on('submit',function(e){
                    var $form = $(this);

                    if ($form.data('submitted') === true) {
                    // Previously submitted - don't submit again
                    e.preventDefault();
                    } else {
                    // Mark it so that the next submit can be ignored
                    $form.data('submitted', true);
                    }
                });

                // Keep chainability
                return this;
                };
        </script>


<script>
    $('.btn-service-back').click(function() {
      $('.service-data-group').removeClass('hidden');
      $('.renews-group').addClass('hidden');
    });
    $('.btn-service-save').click(function() {
      $.ajax({
          url: "{{ url('client/service/renews') }}",
          type: "get",
          data: {
            isUpdate: 1,
            service: $('.service_type').val(),
            subscription: $('.subscription_type').val(),
            subscription_rate: $('.subscription_rate').val()
          },
          success: function(data) {
            const rate = parseFloat(data) || 0;
            if (rate > 0) {
              $('.service-data-group').addClass('hidden');
              $('.renews-group').removeClass('hidden');
            } else {
              location.reload();
            }
          }
      });
    });
    $('.service_type').change(function() {
        $('.self-service-description').addClass('hidden');
        $('.full-service-description').addClass('hidden');
        const service = $('.service_type').val();
        if (service == 'self') {
          $('.self-service-description').removeClass('hidden');
        }
        if (service == 'full') {
          $('.full-service-description').removeClass('hidden');
        }
        showSubscriptionRate();
    });
    $('.subscription_type').change(function() {
        showSubscriptionRate();
    });
    function showSubscriptionRate() {
        const service = $('.service_type').val();
        const subscription = $('.subscription_type').val();
        let rate = null;
        if (service == 'self' && subscription=='30') {
            rate = "{{Auth::user()->client->self_30day_rate}}";
        } else if (service == 'self' && subscription=='365') {
            rate = "{{Auth::user()->client->self_365day_rate}}";
        } else if (service == 'full' && subscription=='30') {
            rate = "{{Auth::user()->client->full_30day_rate}}";
        } else if (service == 'full' && subscription=='365') {
            rate = "{{Auth::user()->client->full_365day_rate}}";
        }
        $('.subscription_rate').val(rate);
    }
    $('#renews-form').submit(function(e) {
        $('#pay-button').attr('disabled','true');
        $('#response_msg').addClass('hidden');
        $('#response_note').addClass('hidden');
        $('#payment-errors').addClass('hidden');
        $('#response_msg > span').html('');
        $('#response_note  > span').html('');
        $('#payment-errors > span').html('');

        var $form = $('#renews-form');
       
        var serializedData = $form.serializeArray();
       
        $.ajax({
            url: "{{ url('client/service/renews') }}",
            type: "get",
            data: $.param(serializedData),
            success: function(data) {
                    location.reload();
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
</script>
@if(1===2)
@if(!Auth::user()->client->service || (Auth::user()->client->service && Auth::user()->client->subscriptionRate && (!Auth::user()->client->expiration || Auth::user()->client->expiration < date('Y-m-d H:i:s'))))
<script src="https://docs.paymentjs.firstdata.com/lib/{{env('PAYMENTJS_LIBRARY_ID')}}/client-2.0.0.js"></script>
<script type="text/javascript">
const DomUtilsForRenews = {
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
    } else if (DomUtilsForRenews.hasClass(el, cssClass)) {
      const reg = new RegExp(`(\\s|^)${cssClass}(\\s|$)`);
      el.className = el.className.replace(reg, ' ');
    }
  },
};

const SubmitButton = {
  buttonElement: DomUtilsForRenews.getEl('[data-submit-btn]'),
  loaderElement: DomUtilsForRenews.getEl('.btn__loader'),

  enable: () => {
    SubmitButton.buttonElement.disabled = false;
    DomUtilsForRenews.removeClass(SubmitButton.buttonElement, 'disabled-bkg');
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
          
            var $form = $('#renews-form-card');
       
            var serializedData = $form.serializeArray();
            $.ajax({
                url: "{{ url('client/service/renews') }}",
                type: "get",
                data: $.param(serializedData),
                success: function(data) {
                    location.reload();
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
  const form = DomUtilsForRenews.getEl('#renews-form-card')
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    SubmitButton.setSubmitState();
    paymentForm.onSubmit(onSuccess, onError);
  });

  const ccFields = window.document.getElementsByClassName('payment-fields');
  for (let i = 0; i < ccFields.length; i++) {
    DomUtilsForRenews.removeClass(ccFields[i], 'disabled');
  }
  SubmitButton.enable();
};
window.firstdata.createPaymentForm(config, hooks, onCreate);
</script>
@endif
@endif

    @yield('scripts')
    <script async data-cfasync="false" src="https://d29l98y0pmei9d.cloudfront.net/js/widget.min.js?k=Y2xpZW50SWQ9MjMwMCZob3N0TmFtZT1zdW5zaGluZW5vdGljZXMuc3VwcG9ydGhlcm8uaW8="></script>

</body>
</html>
