@extends('layouts.app')

@section('content')
<style type="text/css">
  .form-group{
    margin-left: 0px !important ;
    margin-right: 0px !important ;

  }
  .help-block{
    color: red !important;
    font-size: 12px;
    display: none;
  }
  .upper{
    text-transform: uppercase !important;
  }

</style>
<div class="container">
     
     <form class="form-horizontal" role="form" method="POST" action="{{ route('register') }}">
    <div class="row firstpage">
        <div class="row">
                <div class="col-xs-2 col-xs-offset-5">
                <img class="img-responsive" src="{{ asset('images/logo.png')}}" alt="">
                </div>
        
         </div>
        <div>&nbsp;</div>
        <div class="col-md-6 ">
            <div class="panel panel-warning">
                <div class="panel-heading">Register</div>
                <div class="panel-body">
                   
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                            <label for="first_name" class="col-md-4 control-label"> First Name</label>

                            <div class="col-md-6">
                                <input id="first_name" type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" required autofocus>

                                 
                                    <span class="help-block spfirst_name">
                                        <strong>First name is required.</strong>
                                    </span>
                                 
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                            <label for="last_name" class="col-md-4 control-label"> Last Name</label>

                            <div class="col-md-6">
                                <input id="last_name" type="text" class="form-control" name="last_name" value="{{ old('last_name') }}" required autofocus>

                                
                                    <span class="help-block splast_name">
                                        <strong>Last Name is required</strong>
                                    </span>
                                 
                            </div>
                        </div>
                        
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                 
                                    <span class="help-block spemail">
                                        <strong>Email is required.</strong>
                                    </span>
                                    <span class="help-block spemail_type">
                                        <strong>Email type is incorrect.</strong>
                                    </span>
                                    <span class="help-block spemail_exist">
                                        <strong>This email already exists.</strong>
                                    </span>
                                 
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>
                                    <span class="help-block sppassword_match">
                                        <strong>Password does not match.</strong>
                                    </span>
                                    <span class="help-block sppassword">
                                        <strong>Password is required.</strong>
                                    </span>
                                    <span class="help-block sppassword_strong">
                                        <strong>Password is not strong.</strong>
                                    </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>
                        
                        <div class="col-xs-12">
                            <small>
                                Under the Electronic Signatures in Global and National 
                                Commerce Act (E-Sign), this Agreement and all electronically 
                                executed documents related hereto are legally binding 
                                in the same manner as are hard copy documents executed 
                                by hand signature when: (1) ISO or User’s electronic 
                                signature is associated with the Agreement and related 
                                documents, (2) ISO or User consents and intend to be 
                                bound by the Agreement and related documents, and (3) 
                                the Agreement is delivered in an electronic record capable 
                                of retention by the recipient at the time of receipt (i.e., print or otherwise 
                                store the electronic record). When accepted in electronic form, this Agreement and all related electronic 
                                documents shall be governed by the provisions of E-Sign. By 
                                pressing “Submit”, “Accept” or “I Agree”, ISO or User, as the case may be, 
                                agrees: (i) that the Agreement and related documents shall be effective 
                                by electronic means, (ii) to be bound by the terms and conditions of this Agreement 
                                and related documents, and (iii) that it has had the ability to print or 
                                otherwise store the Agreement and related documents. 
                            </small>
                        </div>
                        
                </div>
            </div>
        </div>

        <div class="col-md-6 ">
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <h3 class="panel-title">Disclaimer</h3>
                </div>
                <div class="panel-body">
                   <p>I understand that Sunshine Notices Inc. may not be able to 
                       ascertain to whom the NOTICE TO OWNER should be sent. 
                       Further, it is expressly understood that I hereby waive 
                       any claim against Sunshine Notices Inc. that I or my firm may have in 
                       the future due to the inability of Sunshine Notices Inc. to 
                       ascertain who should receive NOTICE TO OWNER copies, 
                       or due to the inability to effectuate timely or proper 
                       service of the Notice to Owner. </p>
                   <p>Sunshine Notices, Inc. does not provide legal counsel, 
                       anyone seeking legal advice should contact an attorney. 
                       I hereby agree on behalf of myself and my firm to pay 
                       for any research, preparation and serving expenses 
                       generated by my request or representative, including oral
                       requests, upon receipt of the invoice.</p>
                   <p>In the event that payment is not made within 15 days of 
                       the date of the invoice I agree to pay interest of 18% 
                       per annum as well as all costs of collection including 
                       reasonable attorney fees. </p>
                   <p>I also understand that should I or my firm suffer any 
                       damages as a result of the utilization of Sunshine Notices Inc. 
                       for services in connection with NOTICE TO OWNER, preliminary 
                       notices or any other services that Sunshine Notices Inc. provides 
                       or agrees to provide, It is agreed that the limit of 
                       liability of Sunshine Notices Inc., and/or affiliates, officers, employees 
                       and assigns inclusive of any interest, cost, and attorney 
                       fees shall not exceed the cost of the notice. </p>
                   <p>I authorize Sunshine Notices Inc. personnel to sign on 
                       behalf of myself and/or my firm any notices 
                       that we request you to prepare. </p>
                   <p>Returned checks will be charged a $20 fee </p>
                    <div class="col-md-12 form-group {{ $errors->has('agree') ? ' has-error' : '' }}">
                        <label>&nbsp;</label>
                        <div class="checkbox checkbox-slider--b-flat">
                            <label>
                            <input name="agree" type="checkbox" value="agree" id='agree'><span>I Agree</span>
                            </label>
                        </div>
                        
                                    <span class="help-block spagree">
                                        <strong>You must Agree to this terms.</strong>
                                    </span>
                         
                        </div>    
                    </div>
                </div>
        </div>
    
        <div class="form-group ">
            <div class="col-md-12 col-md-offset-10">
                <button type="button" class="btn btn-warning nextButton" >
                    Agree & Next
                </button>
            </div>
        </div>    
    </div>
        
    




    <div class="row secondpage" >
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">Company Information
                     
                </h1>       
            </div>

            </div>
            <div class="form-group ">
                <div class="col-md-12 ">
                    
                    <button type="button" class="btn btn-warning pull-right submitButton">
                         Register User & Company 
                    </button> 
                    <button type="button" class="btn btn-danger pull-right backButton" style="margin-right: 10px">
                          Back
                    </button>
                </div>
                
            </div>  
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h5 class="page-header">
                    Everything below will pre-fill into one or all of the forms at one time or another so try to complete what is being asked. 
                </h5>       
            </div>
            </div>
        </div>
        <div id="page-wrapper">
            
            <div class="container-fluid">
                 
                <div class="row">
                  
                    
                    <div class="col-xs-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Contact Info
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <label>Company Name:</label>
                                    <input name="company_name" id="company_name" class="form-control upper" data-toggle="tooltip" data-placement="top" title="">
                                     
                                    <span class="help-block SP_company_name">
                                        <strong>Company name is required.</strong>
                                    </span>
                                     
                                </div>
                                </div>
                                <?php

                                $gender = [
                                     'none' => 'Select one..',
                                     'female' => 'Female',
                                     'male' => 'Male',
                                 ];

                                 $print_method = [
                                     'none' => 'None',
                                     'sni' => 'SNI Prints',
                                     'client' => 'I Print',
                                 ];
                                 $billing_type = [
                                     'none' => 'Select one...',
                                     'attime' => 'When Work order is created',
                                     'invoiced' => 'Invoiced once a week',
                                 ];
                                 $send_certified = [
                                     'none' => 'None',
                                     'green' => 'Green Certified',
                                     'nongreen' => 'Non-green Certified',
                                 ];
                                 $notification_setting=[
                                     'immediate' => 'Immediate',
                                     'off' => 'Off',
                                 ];
                                 $default_materials="";

                                ?>
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label>Title:</label>
                                        <input name="title" id="title"  class="form-control upper" data-toggle="tooltip" data-placement="top" title="">

                                    </div>

                                    <div class="col-md-6 form-group">
                                    <label>Gender:</label>
                                    {!!  Form::select('gender',$gender,old("gender",'none'), ['class' => 'form-control','data-toggle'=>'tooltip', 'data-placement'=>'top', 'title'=>'This question prefills some forms ie Claim of Lien']) !!}
                                    </div>

                               
                                </div>  
                               
                         
                                
 
                                
                                
                                <div class="row">
                                <div class="col-md-12 col-lg-6 form-group">
                                    <label>Phone:</label>
                                    <input name="phone" id="phone" class="form-control upper" data-toggle="tooltip" data-placement="top" title="">
                                     
                                    <span class="help-block SP_phone">
                                        <strong>Phone number is required. </strong>
                                    </span>
                                     
                                </div>
                              
                                <div class="col-md-12 col-lg-6 form-group">
                                    <label>Mobile:</label>
                                    <input name="mobile" id="mobile" class="form-control upper" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                <div class="col-md-12 col-lg-6 form-group">
                                    <label>Fax:</label>
                                    <input name="fax"  class="form-control upper" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                 
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Confirm you are not a robot.
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <center><div class="g-recaptcha" data-sitekey="{{env('CAPTCHA_SITE_KEY')}}" data-callback="recaptchaCallback"></div></center>
                                    </div>
                                </div><!-- 6Lcjo4IUAAAAAO720ipq-Eghi8vln9ZTZDyF-LLb -->
                            </div>
                        </div>

                        
                    </div>
                    
                    <div class="col-xs-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Address
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <label>Street Address:</label>
                                    <input name="address_1" id="address_1" placeholder="Street and number" class="form-control upper" data-toggle="tooltip" data-placement="top" title="">
                                     
                                    <span class="help-block SP_address_1">
                                        <strong>Address is required.</strong>
                                    </span>
                                     
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <input name="address_2" id="address_2"  placeholder="Apartment, suite, unit, building, floor, etc." class="form-control upper" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-md-12 col-lg-4 form-group">
                                    <label>Country:</label>
                                    <input id="country" name="country" class="form-control typeahead upper" data-toggle="tooltip" data-placement="top" title="" autocomplete="off">
                                     
                                    <span class="help-block SP_country">
                                        <strong>Country  is required.</strong>
                                    </span>
                                    
                                </div>
                               <div class="col-md-6 col-lg-4 form-group">
                                    <label>City:</label>
                                    <input name="city" id="city"  class="form-control upper" data-toggle="tooltip" data-placement="top" title="">
                                     
                                    <span class="help-block SP_city">
                                        <strong>City  is required.</strong>
                                    </span>
                                     
                                </div>
                                <div class="col-md-6 col-lg-4 form-group">
                                    <label>State / Province / Region:</label>
                                    <input id="states"  name="state" class="form-control typeahead upper" data-toggle="tooltip" data-placement="top" title=""  autocomplete="off">
                                </div>
                                   
                                </div>
                                
                                <div class="row">
                              
                             
                                <div class="col-md-12 col-lg-6 form-group">
                                    <label>Zip code:</label>
                                    <input name="zip" id="zip"  class="form-control upper" data-toggle="tooltip" data-placement="top" title="">
                                      
                                    <span class="help-block SP_zip">
                                        <strong>Zip is required.</strong>
                                    </span>
                                     
                                </div>
                                      <div class="col-md-12 col-lg-6 form-group">
                                    <label>County:</label>
                                    <input id="counties"  name="county" class="form-control typeahead upper" data-toggle="tooltip" data-placement="top" title="What county is your company based in? If out of state put your county and state here" autocomplete="off">
                                </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    
                     <div class="col-xs-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Default Materials
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    {{ Form::textarea('default_materials',$default_materials ,['class'=>'form-control upper','data-toggle'=>'tooltip','id'=>'material','data-placement'=>'top', 'title'=>'Default Materials and/or Services. This is a description of what you normally would provide on your jobs. (ie Electrical Contractor would supply Electrical Materials and Services)'])}}
                                     
                                    <span class="help-block SP_material">
                                        <strong>Default Materials is required.</strong>
                                    </span>
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <input name="honeypot" id="honeypot" type="hidden" value="">
                                </div>
                                </div>
                              
                            </div>
                             
                        </div>
                        
                    </div>
                    
                    <div class="col-xs-12">
                        
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
      
               
            </div>
            <!-- /.container-fluid -->
            
        </div>
        
    </div>

    <div class="row thirdpage" >
        
    </div>


</form>
</div>

<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/jqsignature/js/jq-signature.min.js') }}" type="text/javascript"></script>

<script src='https://www.google.com/recaptcha/api.js'></script>


<script>
function recaptchaCallback() {
    var response = grecaptcha.getResponse();
    if(response.length != 0){
        $('.submitButton').prop('disabled', false);
        $('#honeypot').val(response);
    }
    
}
    
$(function () {
$('.secondpage').css('display','none');
$('.thirdpage').css('display','none');
$('.submitButton').prop('disabled', true);

$('[data-toggle="tooltip"]').tooltip();
$('#first_name').keypress(function(){
    $('.spfirst_name').css('display','none');
});
$('#last_name').keypress(function(){
    $('.splast_name').css('display','none');
});
$('#email').keypress(function(){
    $('.spemail').css('display','none');
    $('.spemail_type').css('display','none');
    $('.spemail_exist').css('display','none');
});
$('#password').keypress(function(){
    $('.sppassword').css('display','none');
    $('.sppassword_match').css('display','none');
    $('.sppassword_strong').css('display','none');
});
$('#agree').click(function(){
     
    $('.spagree').css('display','none');
});


$('.nexttocaptcha').click(function(){
    $('.thirdpage').css('display','block');
    $('.secondpage').css('display','none');
});

$('.backtosecond').click(function(){
    $('.thirdpage').css('display','none');
    $('.secondpage').css('display','block');
    grecaptcha.reset();
    $('.submitButton').prop('disabled', true);
});

$('.backButton').click(function(){
    $('.firstpage').css('display','block');
    $('.secondpage').css('display','none');
});
  $('.nextButton').click(function(){
     
    var nextable=true;;
    $('.help-block').css('display','none');
    if ($('#first_name').val().trim()==""){
        $('.spfirst_name').css('display','block');  nextable=false;
    }
    if ($('#last_name').val().trim()==""){
        $('.splast_name').css('display','block');  nextable=false;
    }
    if ($('#email').val().trim()==""){
        $('.spemail').css('display','block');  nextable=false;
    } else if ($('#email').val().indexOf('@')<0 || $('#email').val().indexOf('.')<0){
        $('.spemail_type').css('display','block');  nextable=false;
    } 

    if ($('#password').val()=="" || $('#password-confirm').val()==""){
        $('.sppassword').css('display','block');  nextable=false;
    }  else if ($('#password').val()!=$('#password-confirm').val()){
        $('.sppassword_match').css('display','block');  nextable=false;
    } else if ($('#password').val().length<6){
        $('.sppassword_strong').css('display','block');  nextable=false;

    }
    if (!$('#agree').prop('checked')){
        $('.spagree').css('display','block');  nextable=false;
    }


    
    if (nextable){
        var email=$('#email').val().trim().toLowerCase();
        $.get("{{route('validation.unique.email')}}",{email:email}).done(function(data){
            if (data=='unique'){
                $('.firstpage').css('display','none');
                $('.secondpage').css('display','block');
            } else {
                $('.spemail_exist').css('display','block');
            }
        }).fail(function(){
            $('.spemail_exist').css('display','block');
        });
    }

  });


   $( '.submitButton').click( function(){
    var nextable=true;;
    if ($('#company_name').val().trim()==""){
        $('.SP_company_name').css('display','block');  nextable=false;
    }
    if ($('#phone').val().trim()==""){
        $('.SP_phone').css('display','block');  nextable=false;
    }
    if ($('#address_1').val().trim()=="" && $('#address_2').val().trim()==""){
        $('.SP_address_1').css('display','block');  nextable=false;
    }
    if ($('#country').val().trim()==""){
        $('.SP_country').css('display','block');  nextable=false;
    }
    if ($('#city').val().trim()==""){
        $('.SP_city').css('display','block');  nextable=false;
    }
    if ($('#zip').val().trim()==""){
        $('.SP_zip').css('display','block');  nextable=false;
    }
    if ($('#material').val().trim()==""){
        $('.SP_material').css('display','block');  nextable=false;
    }
        
    if (nextable){
        $('form').submit();
    }
   });



$('#company_name').keypress(function(){
    $('.SP_company_name').css('display','none');
    $('#company_name').val()=$('#company_name').val()
});
$('#phone').keypress(function(){
    $('.SP_phone').css('display','none');
});
$('#address_1').keypress(function(){
    $('.SP_address_1').css('display','none');
});
$('#address_2').keypress(function(){
    $('.SP_address_1').css('display','none');
});
$('#country').keypress(function(){
    $('.SP_country').css('display','none');
});
$('#city').keypress(function(){
    $('.SP_city').css('display','none');
});
$('#zip').keypress(function(){
    $('.SP_zip').css('display','none');
});
$('#material').keypress(function(){
    $('.SP_material').css('display','none');
});

})
</script>

@endsection

 
 
    

    
