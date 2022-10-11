@extends('researcher.layouts.app')


@section('css')
       
@endsection


@section('content')
<div class="container">
    {!! Form::open(['route'=>['pdfpage.complete'],'autocomplete' => 'off'])!!}
    {{ Form::hidden('work_order_id',$work_order_id)}}
    <div class="col-xs-12 text-center">
        <h3>Would You like to set this notice as completed?</h3>
    </div>
    <div>&nbsp;</div>
        <div class="col-xs-3 text-Left">
            <button type="submit" class="btn  btn-success btn-block" name="complete" value="yes"><i class="fa fa-check"></i> Yes</button>
            
        </div>
        <div class="col-xs-6">
            &nbsp;
        </div>
        <div class="col-xs-3 text-right">
            <button type="submit" class="btn  btn-danger btn-block" name="complete" value="ni"><i class="fa fa-times"></i> No</button>
        </div>
    {!! Form::close() !!}
</div>
@endsection

@section('scripts')
<script src="{{ asset('/vendor/datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script>
$(function () {
   
});

</script>

@endsection