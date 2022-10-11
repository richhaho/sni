@extends('client.layouts.app')

@section('navigation')
    @include('client.navigation')
@endsection

@section('content')
            <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12">
                        <h1 class="page-header">My Users 
                            <a class="btn btn-success pull-right add-user" href="{{ route('client.clientusers.create')}}"><i class="fa fa-plus"></i> Add User</a>
                        </h1>
                       
                    </div>
                         @if (Session::has('message'))
                            <div class="col-xs-12 message-box">
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                            </div>
                        @endif
                    
                        
                        @if(count($users) > 0 )
                        <div class="col-xs-12">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Email</th>
                                    <th>Full Name</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                @if($user->deleted_at == null)
                                <tr>
                                    <td> {{ $user->email }}</td>
                                    <td> {{ $user->full_name }}</td>
                                    
                                    <td> {{ ($user->status == 1) ? 'Enabled': 'Disabled' }}</td>
                                    <td>
                                    @if(count($users) > 1)
                                     @if($client->client_user_id == $user->id)
                                        <button type="button" class="btn btn-danger btn-xs disabled" ><i class="fa fa-times"></i> Delete</button>
                                     @else
                                        @component('client.manageclientusers.components.deletemodal')
                                            @slot('client_id') 
                                                {{ $client->id }}
                                            @endslot
                                            @slot('id') 
                                                {{ $user->id }}
                                            @endslot
                                            @slot('name') 
                                                {{ $user->full_name }}
                                            @endslot
                                        @endcomponent
                                       @endif
                                    @else
                                        <button type="button" class="btn btn-danger btn-xs disabled" ><i class="fa fa-times"></i> Delete</button>
                                    @endif 
                                    <a class="btn btn-success btn-xs " href="{{ route('client.clientusers.edit',$user->id)}}"><i class="fa fa-pencil"></i> Edit</a>
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
                            <h5>No Users found</h5>
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
$('.add-user').click(function(){
    $('.add-user').addClass("disabled");
    $('.add-user').css('pointer-events','none');
});
$(function () {
    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
});
</script>
@endsection