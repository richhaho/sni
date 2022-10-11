@extends('client.layouts.app')


@section('css')
       
@endsection


@section('content')
<div class="container">
    {!! Form::open(['route'=>['client.notices.reset.do', $work->id],'method'=> 'POST','autocomplete' => 'off'])!!}
    {{ Form::hidden('backurl', $backurl,['id' =>'backurl']) }}
    <div class="col-xs-12 text-center">
        <h3>You or someone else started generating this notice at<br> 
            {{ $work->pdf_pages()->orderBy('updated_at', 'DESC')->first()->updated_at->format('m-d-y h:i:s a')}}<br> but did not complete it.  Do you want to continue?</h3>
    </div>
    <div>&nbsp;</div>
        <div class="col-xs-3 text-Left">
            <?php 
            $generated_att=0;
            foreach ($work->attachments as $attach){
                if ($attach->type=="generated"){
                    $generated_att=1;break;
                }
            }
            ?>
            @if ($generated_att>0)
            <button type="submit" class="btn  btn-success btn-block" name="reset" value="yes" disabled><i class="fa fa-check"></i> Yes</button>
            @else
            <button type="submit" class="btn  btn-success btn-block" name="reset" value="yes"><i class="fa fa-check"></i> Yes</button>
            @endif
        </div>
        <div class="col-xs-6">
            &nbsp;
        </div>
        <div class="col-xs-3 text-right">
            <button type="submit" class="btn  btn-danger btn-block" name="reset" value="no"><i class="fa fa-times"></i> No</button>
        </div>
    {!! Form::close() !!}
</div>
@if ($generated_att>0)
<br><br>
<div class="container">
    <div class="col-xs-12 text-center">
        <h4>The attachments were already generated.  You can no longer restart this session unless you delete the generated PDFs.</h4>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script src="{{ asset('/vendor/datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script>
$(function () {
   
});

</script>

@endsection