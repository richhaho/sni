@extends('admin.layouts.app')

@section('navigation')
    @include('admin.navigation')
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('/vendor/bootstrap-multiselect/bootstrap-multiselect.css') }}">
<style>
    td .btn{
        margin-top: 5px;
    }
</style>
@endsection


@section('content')
    <div id="page-wrapper" style="min-height: 700px">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12">
                    <h1 class="page-header"> Email Setting To Admin/Researcher(s)</h1>
                </div>
                @if (Session::has('message'))
                    <div class="col-xs-12 message-box">
                        <div class="alert {{ Session::get('message-class','alert-info') }}">{{ Session::get('message') }}</div>
                    </div>
                @endif
                    
                <div class="col-xs-12">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="30%">Email Name</th>
                                <th width="60%">To Admin/Researcher(s)</th>
                                <th width="10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($adminemails as $email)
                            {!! Form::open(['route' => ['adminemails.update',$email->id], 'method'=> 'POST','autocomplete' => 'off']) !!}
                            <tr>
                                <td> {{$email->name}}</td>
                                <td class="email-users-{{$email->id}}" style="pointer-events: none; opacity: 0.7;">
                                    {!! Form::select('users_select', $admin_users, array_map('intval', explode(',', $email->users)) ,['class' => 'multi-select-users form-control'.$email->id, 'multiple'=>'multiple'])!!}
                                    <input type="hidden" name="users" class="users-content" value="{{$email->users}}">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-xs btn-edit btn-edit-{{$email->id}}" data="{{$email->id}}"><i class="fa fa-edit"></i> Edit</button>
                                    <button type="submit" class="btn btn-success btn-xs btn-save hidden btn-save-{{$email->id}}"><i class="fa fa-save"></i> Save</button>
                                </td>
                            </tr>
                            {!! Form::close() !!}
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
@endsection

@section('scripts')
 
<script src="{{ asset('/vendor/bootstrap-multiselect/bootstrap-multiselect.js') }}"></script>
<script>

$(function () {
    $(".message-box").fadeTo(3000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });

    $('.multi-select-users').multiselect({
        includeSelectAllOption: true,
    });
    $('.multi-select-users').change(function() {
        $(this).parent().find('.users-content').val($(this).val().join(','))
    });

    $('.btn-edit').click(function(){
        const id = $(this).attr('data');
        $('.btn-save-'+id).removeClass('hidden');
        $(this).addClass('hidden');
        $('.email-users-'+id).css('pointer-events','auto');
        $('.email-users-'+id).css('opacity', 1);
    });
});
</script>
@endsection