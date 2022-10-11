@extends('client.layouts.app')

@section('navigation')
    @include('client.navigation')
@endsection

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/tooltipster/css/tooltipster.bundle.min.css') }}" rel="stylesheet" type="text/css">
<style>
.thumbnail,.message-box{
  word-break: break-word;
}
.tooltip_templates { display: none; } 
.select2-new-button { padding: 5px; }
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


@section('content')
            <div id="page-wrapper">
                <br>
                <div class="stepwizard">
                  <div class="stepwizard-row setup-panel">
                    <div class="stepwizard-step col-xs-3">
                        <a type="button" class="btn btn-default btn-circle">1</a>
                        <p style="color: black"><small>Job/Contract Information & Workorder</small></p>
                    </div>
                    <div class="stepwizard-step col-xs-3"> 
                        <a type="button" class="btn btn-success btn-circle">2</a>
                        <p><small><strong>Job/Contract Parties & Attachments</strong></small></p>
                    </div>
                    @if (Auth::user()->client->billing_type!='invoiced')
                    <div class="stepwizard-step col-xs-3">    <a type="button" class="btn btn-default btn-circle" >3</a>
                        <p><small>Payment</small></p>
                    </div>
                    <div class="stepwizard-step col-xs-3">    <a type="button" class="btn btn-default btn-circle" >4</a>
                        <p><small>Confirmation</small></p>
                    </div>
                    @else
                    <div class="stepwizard-step col-xs-3">    <a type="button" class="btn btn-default btn-circle" >3</a>
                        <p><small>Confirmation</small></p>
                    </div>
                    @endif
                  </div>
                </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="row">    
                            
                                <h4 class="page-header">
                                    Your job has been created! Now let's add some parties and documents so we can process this for you as quickly as possible.<br>
                                    <span class="h5">Job: {{$job->name}}</span>
                                </h4>
                            
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="row">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <h5><strong>Are there any other parties on the job? If so, now is the time to tell us.</strong></h5>
                                    <h5 class="page-header">
                                    Please verify and enter any parties you know of that would need to receive a copy of your Work Order if they are not already listed below.  This includes the landowner, general contractor, surety if a Public job, and any other parties associated with the Job/Contract. If the party is someone you have entered into our system before, you can simply begin typing their name into the "Contact" box to search and pull up their contact record.
                                    </h5>
                                </div>
                            </div>
                            @if (count($errors) > 0)
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 message-box">
                                    <div class="alert alert-danger">
                                            @foreach ($errors->all() as $error)
                                                <p>{{ $error }}</p>
                                            @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="row">
                                {!! Form::open (['route'=>['wizard2.parties.store',$job->id],'files' => true,'id'=> 'party_form']) !!}
                                {!! Form::hidden('job_id',$job->id) !!}
                                {!! Form::hidden('client_id',$job->client->id) !!}
                                {{ Form::hidden('workorder', $work_order) }}
                                <div class="col-md-12 col-lg-12 search-contact">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">Add New Job Party</h4>
                                        </div>
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-xs-12 form-group">
                                                    <label>Job Party Type:</label>
                                                    {!!  Form::select('type',$parties_type,old("type"), ['class' => 'form-control','id' =>'type','required']) !!}
                                                </div>
                                                <div id="box" class="{{ Session::exists('_old_input') ? array_key_exists("contact_id",Session::get('_old_input')) ? '' : 'hidden' : '' }}">
                                                <div class="col-xs-12 form-group">
                                                    <label>Contact: (If entered previously start typing name here. If not, click on contact and New Contact.)</label>
                                                    <select id="contact_id" name="contact_id" class="form-control" style="width: 100%">
                                                        @if(strlen(old("contact_id")) > 0 )
                                                        <option value="{{old("contact_id")}}">{{ \App\ContactInfo::find(old("contact_id"))->name_entity_name}}</option>
                                                        @else
                                                        <option value=""></option>
                                                        @endif
                                                    </select>
                                                    
                                                </div>
                                                </div>
                                            </div>
                                            <div id="type-fields">
                                                
                                            </div>
                                
                                            <div id="new-contact" class="{{ Session::exists('_old_input') ? array_key_exists("first_name",Session::get('_old_input')) ? '' : 'hidden' : 'hidden' }}">
                                                
                                                @include('client.wizard2.dynamicforms.newcontact')
                                            </div>
                                        
                                            
                                            <div class='row'>
                                                
                                                <div class="col-xs-12 form-group ">
                                                    <a id="cancel-new-contact-button" class="btn btn-danger hidden">Cancel</a>
                                                    <button type="button" class="btn btn-success btn-save pull-right btn-submit-party-form" form="party_form" > Save</button>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {!! Form::close(); !!}
                                
                                <div class="col-md-12 col-lg-12">
                                        @if (Session::has('message'))
                                            <div class="row">
                                            <div class="message-box col-lg-12">
                                                <div class="alert alert-info">{{ Session::get('message') }}</div>
                                            </div>
                                            </div>
                                        @endif
                                        @foreach ($parties_type1 as $type_key => $type_name)
                                            @if($loop->first )
                                                <div class="row">
                                            @endif
                                            <div class="col-lg-6">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading">
                                                        <h4 class="panel-title">{{ $type_name }} @if ($type_name=="Client") (Your Company) @endif</h4>
                                                    </div>
                                                    <div class="panel-body">
                                                    @foreach($job->parties()->ofType($type_key)->get() as $jobparty) 
                                            
                                                            @include('client.wizard2.components.contacticon')

                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            @if($loop->iteration % 2 == 0 && $loop->last)
                                            </div>
                                            @else
                                            @if($loop->iteration % 2 == 0)
                                                </div>
                                                <div class="row">
                                            @else
                                                @if($loop->last)
                                                    </div>
                                                @endif
                                            @endif
                                        @endif
                                        @endforeach
                                    
                                            
                                        
                                </div>
                                
                            </div>
                            <!-- /.col-lg-12 -->
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="row">
                            <div  class="col-xs-12">
                                <h5><strong>Are there any other documents we may need, like an NOC, copy of your contract, etc?</strong></h5>
                                <h5 class="page-header">
                                    Please upload any documents you may have such as the NOC for the job, a copy of your contract with your customer, or any other supporting documents that could help us fully prepare your notice.
                                </h5>  
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Job Attachments </div>
                                    <div class="panel-body">
                                    {!! Form::open(['route' => ['wizard2.addattachments',$job->id],'files' => true,'class'=>'uploadfile']) !!} 
                                    {!! Form::hidden('attach_to','job') !!}
                                    {!! Form::hidden('to_id',$job->id) !!}
                                    <div class="col-xs-12 form-group filegroup">
                                        <label>New File:</label>
                                        {!!  Form::file('file','', ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="col-xs-12 form-group">
                                        <label>Type:</label>
                                        {!!  Form::select('type',$attachment_types,'notice-of-commencement', ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="col-xs-12 form-group">
                                        <label>Description:</label>
                                        {!!  Form::textarea('description','', ['class' => 'form-control','rows'=>4]) !!}
                                    </div>
                                    <div class="col-xs-12 form-group ">
                                        <button class="btn btn-success btn-upload pull-right " type="submit"> <i class="fa fa-floppy-o"></i> Upload</button>
                                    </div>
                                    {!! Form::close() !!}
                                        <div class="col-xs-12">
                                            @foreach ($job->attachments as $attach)
                                                @if($loop->first)
                                                    <div class="row">
                                                @endif
                                                <div class="col-md-4 text-center">
                                                    <div class="thumbnail">
                                                            <a href="{{ route('wizard2.jobs.showattachment',[$job->id,$attach->id])}}">
                                                        <img class="img-responsive" src="{{ route('wizard2.showthumbnail',[$attach->id])}}" alt="{{ $attach->type }}">
                                                            </a>
                                                        <div class="caption">
                                                            <h5 style="word-wrap: break-word;">{{ $attach->original_name}}</h5>
                                                            <p>{{ $attach->description }}</p>
                                                            @if($attach->type <> 'generated' && $attach->type <> 'mailing-generated')
                                                            <p>{{ strtoupper(str_replace('-',' ',$attach->type)) }}</p>
                                                            @endif
                                                            <p>
                                                            @component('client.wizard2.components.deleteattachmentmodal')
                                                                @slot('jid') 
                                                                    {{ $job->id }}
                                                                @endslot
                                                                
                                                                @slot('id') 
                                                                    {{ $attach->id }}
                                                                @endslot
                                                                @slot('file_name') 
                                                                    {{ $attach->original_name }}
                                                                @endslot
                                                            @endcomponent
                                                            </p>
                                                        </div>
                                                        </div>
                                                </div>
                                                @if($loop->iteration % 3 == 0 && $loop->last)
                                                    </div>
                                                @else
                                                    @if($loop->iteration % 4 == 0)
                                                        </div>
                                                        <div class="row">
                                                    @else
                                                        @if($loop->last)
                                                            </div>
                                                        @endif
                                                    @endif
                                                @endif
                                            @endforeach
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="pull-right">
                            <a class="btn btn-danger " href="{{ route('wizard2.getjobworkorder') . '?job_id=' . $job->id.'&workorder_id='.$work_order }}"><i class="fa fa-chevron-left"></i> Back</a> 
                            <!-- <a class="btn btn-success btn-next" href="{{ route($job->client->service == 'self' ? 'wizard2.choicenext' : 'wizard2.payments', [$job->id, $work_order])}}"> Done <i class="fa fa-chevron-right"></i></a> -->
                            <a class="btn btn-success btn-next" href="{{ route('wizard2.payments', [$job->id, $work_order])}}"> Done <i class="fa fa-chevron-right"></i></a>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
@endsection

@section('scripts')
<script src="{{ asset('/vendor/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/bootstrap-filestyle/js/bootstrap-filestyle.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/tooltipster/js/tooltipster.bundle.min.js') }}" type="text/javascript"></script>
<script>
$('.btn-next').click(function(){
    $('.btn-next').addClass("disabled");
    $('.btn-next').css('pointer-events','none');
}); 

$('#type').click(function(){
  $('.btn-save').removeClass("disabled");
    $('.btn-save').css('pointer-events','auto');
});
 $('.delete_job_party').click(function(){
    $('.delete_job_party').addClass("disabled");
    $('.delete_job_party').css('pointer-events','none');
}); 
    var job_id = {{ $job->id }};
    var button_added = false;
$(function () {
    
    
    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
    
     $('.tooltipster').tooltipster();
     
   $("#contact_id").select2({
        theme:'bootstrap',
        minimumInputLength: 2,
        ajax: {
            url: '{{url("/client/wizard/job")}}/'+ job_id + '/contacts',
            dataType: 'json',
            type: "GET",
            delay: 50,
            data: function (params) {
                $(".select2-dropdown").find('.searching').remove();
                $(".select2-dropdown").prepend('<span class="searching">&nbsp;Searching...</span>');
                return params;
            },
            processResults: function (data) {
                $(".select2-dropdown").find('.searching').remove(); 
                return {
                    results: $.map(data, function (item) {
                        return {
                            name_entity_name: item.name_entity_name,
                            is_hot: item.is_hot,
                            id: item.id,
                            full_address: item.full_address,
                            text: item.name_entity_name,
                        }
                    })
                };
            }
        },
        templateResult: formatContact
   }); 

   var contactList = [];
   $.get('{{url("/client/wizard2/job")}}/'+ job_id + '/contactsall' , function( data ) {
      contactList = JSON.parse(data).map(function(item) {
          const firm_name = item.entity.firm_name ? item.entity.firm_name.trim() : '';
          const first_name = item.first_name ? item.first_name.trim() : '';
          const last_name = item.last_name ? item.last_name.trim() : '';
          const text = firm_name + '('+first_name + ' ' + last_name+')';
          return {contactId: item.id, text: text, contact: item};
      });
      contactList.forEach(item => {
        $('.data-list-contact').append('<option>'+item.text+'</option>');
      });
   });

   var selectedContact = null;
   $('.btn-submit-party-form').click(function() {
        $('.btn-save').addClass("disabled");
        $('.btn-save').css('pointer-events','none');
       const new_contact = $('.new_contact_firm_name').val();
       if (!new_contact) {
            $('#party_form').submit();
       }
       if (!selectedContact) {
            const exist_contact= contactList.find(item=>item.text == new_contact);
            selectedContact = exist_contact ? exist_contact : null;
       }

       if (selectedContact) {
           const contact = selectedContact.contact;
            const first_name = $('input[name="first_name"]').val() || '';
            const last_name = $('input[name="last_name"]').val() || '';
            const email = $('input[name="email"]').val() || '';
            const phone = $('input[name="phone"]').val() || '';
            const mobile = $('input[name="mobile"]').val() || '';
            const fax = $('input[name="fax"]').val() || '';
            const address_1 = $('input[name="address_1"]').val() || '';
            const address_2 = $('input[name="address_2"]').val() || '';
            const country = $('input[name="country"]').val() || '';
            const state = $('input[name="state"]').val() || '';
            const city = $('input[name="city"]').val() || '';
            const zip = $('input[name="zip"]').val() || '';

            const contact_first_name = contact.first_name || ''
            const contact_last_name = contact.last_name || ''
            const contact_email = contact.email || ''
            const contact_phone = contact.phone || ''
            const contact_mobile = contact.mobile || ''
            const contact_fax = contact.fax || ''
            const contact_address_1 = contact.address_1 || ''
            const contact_address_2 = contact.address_2 || ''
            const contact_country = contact.country || ''
            const contact_state = contact.state || ''
            const contact_city = contact.city || ''
            const contact_zip = contact.zip || ''

            if (
                first_name == contact_first_name && 
                last_name == contact_last_name &&
                email == contact_email &&
                phone == contact_phone &&
                mobile == contact_mobile &&
                fax == contact_fax &&
                address_1 == contact_address_1 &&
                address_2 == contact_address_2 &&
                country == contact_country &&
                state == contact_state &&
                city == contact_city &&
                zip == contact_zip
            ) {
                $('#box').removeClass('hidden');
                $('#contact_id').prop('disabled',false)
                $('#new-contact').find('select,input').prop('disabled',true);
                $("#contact_id").html('<option value="'+selectedContact.contactId+'">'+selectedContact.text+'</option>')
                $('#contact_id').val(selectedContact.contactId);
                // switch_to_existing_contact(selectedContact)
                // $('.new_contact_firm_name').val('');
                // $('.btn-save').removeClass("disabled");
                // $('.btn-save').css('pointer-events','auto');
            } else {
                console.log(selectedContact.contact.entity.id);
                $('.entity_id').prop('disabled',false);
                $('.entity_id').val(selectedContact.contact.entity.id);
            }
       }
       $('#party_form').submit();
   });

   $('body').on('input','.new_contact_firm_name',function() {
       const new_contact = $('.new_contact_firm_name').val();
       selectedContact = null;
       const exist_contact = contactList.find(item=>item.text == new_contact);
       if (exist_contact) {
           //switch_to_existing_contact(exist_contact);
           selectedContact = exist_contact;
           pullContactFields(exist_contact.contact);
       }
   });

   function pullContactFields(contact) {
       $('input[name="first_name"]').val(contact.first_name);
       $('input[name="last_name"]').val(contact.last_name);
       $('input[name="email"]').val(contact.email);
       $('input[name="phone"]').val(contact.phone);
       $('input[name="mobile"]').val(contact.mobile);
       $('input[name="fax"]').val(contact.fax);

       $('input[name="address_1"]').val(contact.address_1);
       $('input[name="address_2"]').val(contact.address_2);
       $('input[name="country"]').val(contact.country);
       $('input[name="state"]').val(contact.state);
       $('input[name="city"]').val(contact.city);
       $('input[name="zip"]').val(contact.zip);
   }

   var isSetfronNewContact = false;
   function switch_to_existing_contact (contact) {
    $('#new-contact').addClass('hidden');
       $('#new-contact').find('select,input').prop('disabled',true);
       $('#box').removeClass('hidden');
       $('#cancel-new-contact-button').toggleClass('hidden')
       $('#contact_id').prop('disabled',false)
       
        // $('#new-contact').addClass('hidden');
        // $('#new-contact').find('select,input').prop('disabled',true);
        // $('#box').removeClass('hidden');
        // $('#cancelsubmit-new-contact').toggleClass('hidden')
        // $('#contact_id').prop('disabled',false)
        $("#contact_id").html('<option value="'+contact.contactId+'">'+contact.text+'</option>')
        $("#contact_id").select2(); 
        button_added =false;
        $('#contact_id').val(contact.contactId);
        isSetfronNewContact = true;
   }
   
   
   function formatContact (contact) {
    var hot_str = "";
    if (contact.is_hot ==1) {
        hot_str = '<i class="fa fa-fire"></i>';
    }
    var str = '<span>' + hot_str + ' <b> ' + contact.name_entity_name + '</b><br>' + contact.full_address + '</span>'
    var $state = $(str);
    return $state;
  };
  
   $("#contact_id").on('select2:open',function() {
        if(isSetfronNewContact) {
            setTimeout(() => {
                init_contact_id_select();
            }, 10);
        }
        isSetfronNewContact = false;
       if (!button_added ) {
       $(".select2-dropdown").append('<div class="row select2-new-button"><div class="col-xs-4 col-xs-offset-8"><a id="new-contact-button" href="#" class="btn btn-success btn-xs btn-block"><i class="fa fa-plus"></i> New Contact</a></div></div>');
       button_added =true;
       }
   });  
   function init_contact_id_select() {
        $("#contact_id").select2({
            theme:'bootstrap',
            minimumInputLength: 2,
            ajax: {
                url: '{{url("/client/wizard2/job")}}/'+ job_id + '/contacts',
                dataType: 'json',
                type: "GET",
                delay: 50,
                data: function (params) {
                    $(".select2-dropdown").find('.searching').remove();
                    $(".select2-dropdown").prepend('<span class="searching">&nbsp;Searching...</span>');
                    return params;
                },
                processResults: function (data) {
                    $(".select2-dropdown").find('.searching').remove();  
                    return {
                        results: $.map(data, function (item) {
                            return {
                                name_entity_name: item.name_entity_name,
                                id: item.id,
                                full_address: item.full_address,
                                text: item.name_entity_name
                            }
                        })
                    };
                }
            },
            templateResult: formatContact
        }); 
        button_added = false;
        $('#contact_id').select2("open");
   }
  
  
  function updatetype() {
        var xtype= $('#type').val();
       $('#type-fields').load('{{ route("wizard2.additionalform","")}}' +'/'+ xtype,function(){
           $(":file").filestyle();
           $('.date-picker').datepicker();
           leasetype();
       });   
   }
   $('#type').on("change",function() {
       updatetype();
       
   });
   
   
   
   updatetype();
   
   $('body').on('change','.copy_recipient_type',function () {
       var xval = $(this).val();
       
       if (xval == 'other') {
           $('.other_type').removeClass('hidden');
       } else {
           $('.other_type').addClass('hidden');
           
       }
   });
   
   
   $('body').on('change','.lease-type',function () {
       var xval = $(this).val();
       
       if (xval == 'Lessee') {
           $('.bookpage_number').addClass('hidden');
       } else {
           $('.bookpage_number').removeClass('hidden');
           
       }
   });
   
   function leasetype() {
       var xval = $('.lease-type').val();
       
       if (xval == 'Lessee') {
           $('.bookpage_number').addClass('hidden');
       } else {
           $('.bookpage_number').removeClass('hidden');
           
       }
   }
   leasetype();
   
   $('body').on('click','#new-contact-button', function(){
       $('#contact_id').select2("close");
       $('#new-contact').removeClass('hidden');
       $('#new-contact').find('select,input').prop('disabled',false);
      $('#contact_id').prop('disabled',true)
        $(".choose-company").addClass('hidden').find('select').prop('disabled',true);
        $(".new-company").removeClass('hidden').find('input').prop('disabled',false);
       $('#cancel-new-contact-button').toggleClass('hidden');
       $('#box').addClass('hidden');
   });
   
    $('body').on('click','#cancel-new-contact-button', function(){
       $('#new-contact').addClass('hidden');
       $('#new-contact').find('select,input').prop('disabled',true);
       $('#box').removeClass('hidden');
       $('#cancel-new-contact-button').toggleClass('hidden')
       $('#contact_id').prop('disabled',false)
       $("#contact_id").select2({
        theme:'bootstrap',
        minimumInputLength: 2,
        ajax: {
            url: '{{url("/client/wizard2/job")}}/'+ job_id + '/contacts',
            dataType: 'json',
            type: "GET",
            delay: 50,
            data: function (params) {
                $(".select2-dropdown").find('.searching').remove();
                $(".select2-dropdown").prepend('<span class="searching">&nbsp;Searching...</span>');
                return params;
            },
            processResults: function (data) {
                $(".select2-dropdown").find('.searching').remove();  
                return {
                    results: $.map(data, function (item) {
                        return {
                            name_entity_name: item.name_entity_name,
                            id: item.id,
                            full_address: item.full_address,
                            text: item.name_entity_name
                        }
                    })
                };
            }
        },
        templateResult: formatContact
   }); 
        button_added =false;
      
       $('#contact_id').select2("open");
    });
    $('body').on('click','#choose-company-button', function() {
        
        $(".choose-company").removeClass('hidden').find('select').prop('disabled',false);
        $(".new-company").addClass('hidden').find('input').prop('disabled',true);
       
    });
    
    $('body').on('click','#new-company-button', function() {
        $(".choose-company").addClass('hidden').find('select').prop('disabled',true);
        $(".new-company").removeClass('hidden').find('input').prop('disabled',false);
       
    });
    
    var countries = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            // url points to a json file that contains an array of country names, see
            // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
            //local: ['Afghanistan','Albania','Algeria','American Samoa','Andorra','Angola','Anguilla','Antarctica'],
            prefetch:  { url: '{{ route('list.countries') }}' , cache: false }
          });

              // passing in `null` for the `options` arguments will result in the default
              // options being used
              $('#countries').typeahead(null, {
                name: 'countries',
                source: countries
              });


              var states = new Bloodhound({
                  datumTokenizer: Bloodhound.tokenizers.whitespace,
                  queryTokenizer: Bloodhound.tokenizers.whitespace,
                  prefetch: {
                      url: '{{ route('list.states') }}/%QUERY',
                      prepare: function(settings) {
                          if ($('#countries').val().length > 0) { 
                              return settings.url.replace('%QUERY',  $('#countries').val());
                          } else { 
                              return settings.url.replace('%QUERY',  'none');              
                          }
                      },
                      cache:false
                  }
              });

               $('#states').typeahead(null, {
                name: 'states',
                source: states
              });

              $('#states').focus(function () {
                  states.initialize(true);
              }); 
              
              if($('#new-contact').hasClass('hidden')) {
                  $('#new-contact').find('select,input').prop('disabled',true);
                  $('#box').find('select').prop('disabled',false);
                  $('#cancel-new-contact-button').addClass('hidden')
              } else {
                  $('#new-contact').find('select,input').prop('disabled',false);
                  $('#box').find('select').prop('disabled',true);
                  $('#cancel-new-contact-button').removeClass('hidden')
              }
              
              @if(Session::exists('_old_input'))
                  @if(array_key_exists("firm_name",Session::get('_old_input')))
                        $(".choose-company").addClass('hidden').find('select').prop('disabled',true);
                        $(".new-company").removeClass('hidden').find('input').prop('disabled',false);
                  @else
                        $(".choose-company").removeClass('hidden').find('select').prop('disabled',false);
                        $(".new-company").addClass('hidden').find('input').prop('disabled',true);
                  @endif
                
              @else
                 $(".choose-company").addClass('hidden').find('select').prop('disabled',true);
                 $(".new-company").removeClass('hidden').find('input').prop('disabled',false);
              @endif
              
});
</script>
<?php
$max_uploadfileSize= min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
$max_uploadfileSize= substr($max_uploadfileSize, 0, -1)*1024*1024;
?>
<script>
$.fn.select2.defaults.set("theme", "bootstrap");
$('.btn-upload').click(function(){
    $('.btn-upload').addClass("disabled");
    $('.btn-upload').css('pointer-events','none');
}); 
$(function () {
    $("input[type='file']").attr('accept', '.pdf,.jpg,.jpeg,.tiff,.tif,.doc,.xls,.docx,.xlsx');
    $( '.uploadfile').submit( function(event){
        $(".filegroup p").remove();        
        var fe=$('input:file')[0].files[0].size;
        var max_uploadfileSize={{$max_uploadfileSize}};
        var file_name=$('input:file')[0].files[0].name;
        var ext=file_name.split('.').pop().toLowerCase();
        var ext_area=['pdf','jpeg','jpg','tiff','tif','doc','xls','docx','xlsx'];
        if (ext_area.indexOf(ext)==-1){
            $(".filegroup").append('<p>This file type is not permitted for upload.</p>');
            event.preventDefault();
        }
        if (fe>max_uploadfileSize){
            $(".filegroup").append('<p>This file is too large to upload.</p>');
            event.preventDefault();
        }
    });
    $('input:file').click( function(){
      $(".filegroup p").remove();
      $('.btn-upload').removeClass("disabled");
      $('.btn-upload').css('pointer-events','auto'); 
    });
    
    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
    $('[data-toggle="tooltip"]').tooltip()
    $('.date-picker').datepicker();
    $(":file").filestyle();
 
});
</script>
@endsection