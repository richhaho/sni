@extends('admin.layouts.app')

@section('navigation')
    @include('admin.navigation')
@endsection

@section('content')
            <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12">
                        <h1 class="page-header">Admin Users 
                            <a class="btn btn-success pull-right" href="{{ route('users.create')}}"><i class="fa fa-plus"></i> Add User</a>
                        </h1>
                       
                    </div>
                         @if (Session::has('message'))
                            <div class="col-xs-12 message-box">
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                            </div>
                        @endif
                    
                        
                        @if(count($users) > 0 )
                        <div class="col-xs-12">
                            <h3>Administrators</h3>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="25%">Email</th>
                                    <th width="30%">Full Name</th>
                                    <th width="20%">Status</th>
                                    <th width="25%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                @if($user->deleted_at==null)
                                <tr>
                                    <td> {{ $user->email }}</td>
                                    <td> {{ $user->full_name }}</td>
                                    
                                    <td> {{ ($user->status == 1) ? 'Enabled': 'Disabled' }}</td>
                                    <td>
                                    @if(count($users) > 1)
                                        @component('admin.manageusers.components.deletemodal')
                                            @slot('id') 
                                                {{ $user->id }}
                                            @endslot
                                            @slot('name') 
                                                {{ $user->full_name }}
                                            @endslot
                                        @endcomponent
                                    @else
                                        <button type="button" class="btn btn-danger btn-xs disabled" ><i class="fa fa-times"></i> Delete</button>
                                    @endif 
                                    <a class="btn btn-success btn-xs " href="{{ route('users.edit',$user->id)}}"><i class="fa fa-pencil"></i> Edit</a>
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        <div class="col-xs-12 text-center">
                            {{ $users->links() }}
                        </div>
                        @else
                        <div class="col-xs-12">
                            <h5>No Administrators found</h5>
                        </div>
                        @endif

                        @if(count($researchers) > 0 )
                        <div class="col-xs-12">
                            <h3>Researchers</h3>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="25%">Email</th>
                                    <th width="30%">Full Name</th>
                                    <th width="20%">Status</th>
                                    <th width="25%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($researchers as $user)
                                @if($user->deleted_at==null)
                                <tr>
                                    <td> {{ $user->email }}</td>
                                    <td> {{ $user->full_name }}</td>
                                    
                                    <td> {{ ($user->status == 1) ? 'Enabled': 'Disabled' }}</td>
                                    <td>
                                    @if(count($researchers) > 1)
                                        @component('admin.manageusers.components.deletemodal')
                                            @slot('id') 
                                                {{ $user->id }}
                                            @endslot
                                            @slot('name') 
                                                {{ $user->full_name }}
                                            @endslot
                                        @endcomponent
                                    @else
                                        <button type="button" class="btn btn-danger btn-xs disabled" ><i class="fa fa-times"></i> Delete</button>
                                    @endif 
                                    <a class="btn btn-success btn-xs {{($user->id == Auth::user()->id) ? 'disabled': '' }}" href="{{ route('users.edit',$user->id)}}"><i class="fa fa-pencil"></i> Edit</a>
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        <div class="col-xs-12 text-center">
                            {{ $researchers->links() }}
                        </div>
                        @else
                        <div class="col-xs-12">
                            <h5>No Researchers found</h5>
                        </div>
                        @endif
                    
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
@endsection

@section('scripts')
<script>
$(function () {
    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
});
</script>
@endsection