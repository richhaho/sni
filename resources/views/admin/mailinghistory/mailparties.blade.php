@extends('admin.layouts.app')


@section('css')
	<style type="text/css">
     .email_validate{
        color: red;
     }   
    </style>
@endsection


@section('content')
<div class="container">
    <div>&nbsp;</div>
   
    {!! Form::open(['route' => ['mailinghistory.savepdf',$attachment->id],'class'=>'save-page'])!!}
<div class="panel panel-default">
    <div class="panel-heading">Edit Mailing Party</div>
    <div class="panel-body">
       
        <div class="row">&nbsp;</div>
        
        {!! Form::hidden('recipient_id', $attachment->recipient->id) !!}
        {!! Form::hidden('party_id', $attachment->recipient->party_id) !!}
         {!! Form::hidden('party_type', $attachment->recipient->party_type) !!}
        <div class="col-xs-12">
            <div class="panel panel-default">
                
            <div class="panel-heading">{!! Form::text('firm_name',  $attachment->recipient->firm_name, ['class'=>'form-control']) !!}</div>
            <div class="panel-body form-horizontal party-fields">
                <div class="form-group">
                    <label class="col-xs-2">Party Type: </label>
                    <div class="col-xs-10">
                         {{ $parties_type[$attachment->recipient->party_type] }}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-2">Attention To:</label>
                    <div class="col-xs-10">
                         {!! Form::text('attention_name', $attachment->recipient->attention_name,['class'=>'form-control'])!!}
                    </div>
                </div>
                 <div class="form-group">
                    <label class="col-xs-2">Address:</label>
                    <div class="col-xs-10">
                         {!! Form::textarea('address', preg_replace('#<br\s*?/?>#', "\n", $attachment->recipient->address),['class'=>'form-control', 'rows' =>'3'])!!}
                    </div>
                </div>
                <div class="form-group email-group {{$attachment->recipient->mailing_type=='other-mail' ? '' : 'hidden'}}">
                    <label class="col-xs-2">Email:</label>
                    @if ($attachment->recipient->mailing_type=='other-mail')
                    <div class="col-xs-10">
                         {!! Form::text('email', old('email',$attachment->recipient->email),['class'=>'form-control email-field noucase','id'=>'email-field'])!!}
                    </div>
                    @else
                    <div class="col-xs-10">
                         {!! Form::text('email', old('email',$attachment->recipient->email),['class'=>'form-control email-field noucase','id'=>'email-field','disabled'])!!}
                    </div>

                    @endif
                     
                </div>
                <div class="form-group">
                    <label class="col-xs-2">Mailing:</label>
                    <div class="col-xs-10">

                         {!! Form::select('mailing_type', $mailing_types, $attachment->recipient->mailing_type,['class'=>'form-control mailing_type']) !!}
                        
                    </div>
                </div>
                <div class="form-group also-email-group {{$attachment->recipient->mailing_type=='other-mail' || $attachment->recipient->mailing_type=='none' ? 'hidden' : ''}}">
                    <label class="col-xs-2">&nbsp;</label>
                    <div class="col-xs-10 {{!$attachment->recipient->email ? 'hidden' : ''}}">
                        <div class="checkbox checkbox-slider--b-flat">
                            <label><input name="also_email" type="checkbox" ><span>Also eMail to {{$attachment->recipient->email}}</span></label>
                        </div>
                        <input name="also_email_value" type="hidden" value="{{$attachment->recipient->email}}">
                    </div>
                </div>
                <div class="form-group number-group {{$attachment->recipient->mailing_type=='registered-mail' || $attachment->recipient->mailing_type=='express-mail' ? '' : 'hidden'}}">
                    <label class="col-xs-2">Number:</label>
                    <div class="col-xs-10">
                         {!! Form::text('mailing_number', '',['class'=>'form-control mailing_number noucase','id'=>'mailing_number'])!!}
                    </div>
                </div>
                @if( $attachment->recipient->mailing_type == 'certified-green' || $attachment->recipient->mailing_type == 'certified-nongreen')
                    <div class="is-certified">
                        <div class="form-group">
                            <label class="col-xs-2">Barcode:</label>
                            Auto generated
                        </div>
                         
                        <div class="form-group">
                            <label class="col-xs-2">&nbsp;</label>
                            <div class="col-xs-10">
                                <div class="checkbox checkbox-slider--b-flat">
                                    <label><input name="return_receipt" type="checkbox" {{ $attachment->recipient->return_receipt ? 'checked' : ''}}><span>Return Receipt</span></label>
                                </div>
                            </div>
                        </div>
                    </div> 
                @endif
            </div>
            </div>
            <div class="form-group">
                    <label class="col-xs-2">Envelope:</label>
                    <div class="col-xs-10">
                         {!! Form::text('envelope_wording', 'THIS SHOULD SERVE AS A COURTESY COPY AS IT WAS PREVIOUSLY SENT AND RETURNED FOR VARIOUS REASONS.',['class'=>'form-control'])!!}
                    </div>
                </div>
        </div>

    </div>
    <div class="panel-footer">
        <div class="pull-right">
            <button type="submit" class="btn btn-success btn-save"><i class="fa fa-floppy-o"></i> Save</button>
            <a href="{{route('mailinghistory.index')}}" class="btn  btn-danger " ><i class="fa fa-times-circle"></i> Cancel</a>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
    {!! Form::close() !!}
</div>
@endsection

@section('scripts')
<script>
$('.btn-save').click(function(){
    $('.btn-save').addClass("disabled");
    $('.btn-save').css('pointer-events','none');
});
$(function () {
    $('input').keydown(function(){
        $('.btn-save').removeClass("disabled");
        $('.btn-save').css('pointer-events','auto');
        if ($(this).attr('id')=='email-field'){
            $(this).parent().children('.email_validate').remove();
        }
    });
    $('select').change(function(){
        $('.btn-save').removeClass("disabled");
        $('.btn-save').css('pointer-events','auto');
    });
    $( 'form.save-page').submit( function(event){
        $('.email-field').each(function() {
            if (!$(this).attr('disabled')) {;
                var email = $(this).val();
                if (email==null || email==""){
                    event.preventDefault();
                    $(this).parent().children('.email_validate').remove();
                    $(this).after( "<p class='email_validate'>Email is required.</p>" );
                    return;
                }
                if (email.indexOf('@')<0 || email.indexOf('.')<0){
                    event.preventDefault();
                    $(this).parent().children('.email_validate').remove();
                    $(this).after( "<p class='email_validate'>Email type example@admin.com is required.</p>" );
                    return;
                }
            }
        });
    });


    $('.mailing_type').on('change',function() {
        var xval = $(this).val();
        $('.email-group').addClass('hidden');
        $('.number-group').addClass('hidden')
        $("input[name='email']").attr('disabled',true);
        if(xval == 'certified-green' || xval == 'certified-nongreen' ) {
             
            if($('.is-certified').length == 0) {
                $('.is-certified').remove();
                var xhtml = '<div class="is-certified"><div class="form-group"><label class="col-xs-2">Barcode:</label><div class="col-xs-10">Auto generated</div></div>';
                xhtml += '<div class="form-group"><label class="col-xs-2">&nbsp;</label><div class="col-xs-10"><div class="checkbox checkbox-slider--b-flat"><label><input name="return_receipt" type="checkbox" ><span>Return Receipt</span></label></div></div></div></div></div>';
                $('.party-fields').append(xhtml);
            }
        } else if (xval == 'registered-mail' || xval == 'express-mail') {
            $('.number-group').removeClass('hidden');
            $('.is-certified').remove();
        } else {
            $('.is-certified').remove();
            if(xval == 'other-mail') {
                $('.email-group').removeClass('hidden');
                $("input[name='email']").attr('disabled',false);
            }
            
        }
        if (xval == 'none' || xval == 'other-mail') {
            $('.also-email-group').addClass('hidden');
        } else {
            $('.also-email-group').removeClass('hidden');
        }
        
    });
    
    
   
});

</script>

@endsection