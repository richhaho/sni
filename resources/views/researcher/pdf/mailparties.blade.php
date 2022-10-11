@extends('researcher.layouts.app')


@section('css')
	
@endsection


@section('content')
<div class="container">
    <div>&nbsp;</div>
   
    {!! Form::open(['route' => ['pdfpage.save',$work_order->id]])!!}
<div class="panel panel-default">
    <div class="panel-heading">Mailing Parties</div>
    <div class="panel-body">
       
        <div class="row">&nbsp;</div>
        @foreach($parties as $party)
         {!! Form::hidden('party_id[]', $party->id) !!}
         {!! Form::hidden('party_type['. $party->id. ']', $party->type) !!}
        <div class="col-xs-12">
            <div class="panel panel-default">
            <div class="panel-heading">{!! Form::text('firm_name[' .  $party->id . ']', $party->firm_name,['class'=>'form-control'])!!}</div>
            <div class="panel-body form-horizontal party-fields{{$party->id}}">
                <div class="form-group">
                    <label class="col-xs-2">Party Type: </label>
                    <div class="col-xs-10">
                         {{ (array_key_exists($party->type,$parties_type)) ? $parties_type[$party->type]: $party->type }}
                         @if($party->type == "bond")
                          - {{ ucfirst($party->bond_type) }}
                         @endif 
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-2">Attention To:</label>
                    <div class="col-xs-10">
                         {!! Form::text('attention_name[' .  $party->id . ']', $party->attention_name,['class'=>'form-control'])!!}
                    </div>
                </div>
                 <div class="form-group">
                    <label class="col-xs-2">Address:</label>
                    <div class="col-xs-10">
                         {!! Form::textarea('address[' .  $party->id . ']', preg_replace('#<br\s*?/?>#', "\n", $party->address),['class'=>'form-control', 'rows' =>'3'])!!}
                    </div>
                </div>
                <div class="form-group email-group{{$party->id}} {{$errors->has('email[' .  $party->id . ']') ? '' : 'hidden'}}">
                    <label class="col-xs-2">Email:</label>
                    <div class="col-xs-10">
                         {!! Form::text('email[' .  $party->id . ']', old('email[' .  $party->id . ']',$party->email),['class'=>'form-control email-field noucase'])!!}
                    </div>
                    @if($errors->has('email.' .  $party->id ))
                    <div class="col-xs-offset-2 col-xs-10">
                        <span class="text-danger">Invalid Email Address</span>
                    </div>
                    @endif
                </div>
                <div class="form-group">
                    <label class="col-xs-2">Mailing:</label>
                    <div class="col-xs-10">
                        
                         {!! Form::select('mailing_type['. $party->id .']', $mailing_types,$party->mailing_type,['class'=>'form-control mailing_type','data-id' => $party->id ]) !!}
                        
                    </div>
                </div>

            </div>
            </div>
        </div>
         
        @endforeach
    </div>
    <div class="panel-footer">
        <div class="pull-right">
            @if(count($parties)>0)<button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save</button>
            @endif
            <a href="{{route('workorders.index')}}" class="btn  btn-danger " ><i class="fa fa-times-circle"></i> Cancel</a>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
    {!! Form::close() !!}
</div>
@endsection

@section('scripts')
<script>
$(function () {
    $('.mailing_type').on('change',function() {
        console.log('change');
        var xid = $(this).data('id');
        var xval = $(this).val();
        $("input[name='email[" +  xid + "]']").attr('disabled',true)
        $('.email-group'+xid).addClass('hidden')
        if(xval == 'certified-green' || xval == 'certified-nongreen' ) {
            if($('.is-certified' + xid).length == 0) {
                var xhtml = '<div class="is-certified' +  xid  + '"><div class="form-group"><label class="col-xs-2">Barcode:</label><div class="col-xs-10"><p class="form-control-static">Auto Generated</p></div></div>';
                xhtml += '<div class="form-group"><label class="col-xs-2">&nbsp;</label><div class="col-xs-10"><div class="checkbox checkbox-slider--b-flat"><label><input name="return_receipt[' + xid + ']" type="checkbox" ><span>Return Receipt</span></label></div></div></div></div></div>';
                $('.party-fields' + xid).append(xhtml);
            }
        } else {
            if(xval == 'other-mail') {
                $('.email-group'+xid).removeClass('hidden')
                $("input[name='email[" +  xid + "]']").attr('disabled',false)
            }
            
            $('.is-certified' + xid).remove();
        }
        
    });
     
    
    $('.mailing_type').each(function() {
        console.log('change');
        var xid = $(this).data('id');
        var xval = $(this).val();
        
        $("input[name='email[" +  xid + "]']").attr('disabled',true)
        $('.email-group'+xid).addClass('hidden')
        if(xval == 'certified-green' || xval == 'certified-nongreen' ) {
            if($('.is-certified' + xid).length == 0) {
                var xhtml = '<div class="is-certified' +  xid  + '"><div class="form-group"><label class="col-xs-2">Barcode:</label><div class="col-xs-10"><p class="form-control-static">Auto Generated</p></div></div>';
                xhtml += '<div class="form-group"><label class="col-xs-2">&nbsp;</label><div class="col-xs-10"><div class="checkbox checkbox-slider--b-flat"><label><input name="return_receipt[' + xid + ']" type="checkbox" ><span>Return Receipt</span></label></div></div></div></div></div>';
                $('.party-fields' + xid).append(xhtml);
            }
           
        } else {
            if(xval == 'other-mail') {
                $('.email-group'+xid).removeClass('hidden')
                $("input[name='email[" +  xid + "]']").attr('disabled',false)
            }
            
            $('.is-certified' + xid).remove();
        }
        
    });
});

</script>

@endsection