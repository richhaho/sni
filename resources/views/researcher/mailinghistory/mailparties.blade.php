@extends('researcher.layouts.app')


@section('css')
	
@endsection


@section('content')
<div class="container">
    <div>&nbsp;</div>
   
    {!! Form::open(['route' => ['mailinghistory.savepdf',$attachment->id]])!!}
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
                <div class="form-group">
                    <label class="col-xs-2">Mailing:</label>
                    <div class="col-xs-10">

                         {!! Form::select('mailing_type', $mailing_types, $attachment->recipient->mailing_type,['class'=>'form-control mailing_type']) !!}
                        
                    </div>
                </div>
                @if( $attachment->recipient->mailing_type == 'certified-green' || $attachment->recipient->mailing_type == 'certified-nongreen')
                    <div class="is-certified">
                        <div class="form-group">
                            <label class="col-xs-2">Barcode:</label>
                            Auto generated
                            </div>
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
});
$(function () {
    $('.mailing_type').on('change',function() {
        console.log('change');
       
        var xval = $(this).val();
        if(xval == 'certified-green' || xval == 'certified-nongreen' ) {
            if($('.is-certified').length == 0) {
                var xhtml = '<div class="is-certified"><div class="form-group"><label class="col-xs-2">Barcode:</label><div class="col-xs-10">Auto generated</div></div>';
                xhtml += '<div class="form-group"><label class="col-xs-2">&nbsp;</label><div class="col-xs-10"><div class="checkbox checkbox-slider--b-flat"><label><input name="return_receipt" type="checkbox" ><span>Return Receipt</span></label></div></div></div></div></div>';
                $('.party-fields').append(xhtml);
            }
        } else {
            $('.is-certified').remove();
        }
        
    });
    
    
   
});

</script>

@endsection