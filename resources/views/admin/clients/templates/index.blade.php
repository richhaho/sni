@extends('admin.layouts.app')

@section('navigation')
    @include('admin.navigation')
@endsection
@section('css')
<style>
    h1.with-buttons {
        display: block;
        width: 100%;
        float: left;
    }
    .page-header h1 { margin-top: 0; }
</style>
@endsection

@section('content')
            <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="page-header col-xs-12">
                        <div class="col-xs-12  col-md-12">
                            <h1 class="" > Templates for {{$client->company_name}}</h1>
                        </div>
                        @if(count($types) > 0)
                        <div class="col-xs-12 col-md-6"></div>
                        <div class="col-xs-12 col-md-6">
                            <div class="col-md-6  form-inline ">
                                <div class=" form-group pull-right">
                                    <label>Type:</label>
                                    {{Form::select('type',$types,'',['class' => 'form-control ','id'=>'template_type_id'])}}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                <a class="btn btn-success " href="#" id="add-template"><i class="fa fa-plus"></i> Add Template</a>
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                <a class="btn btn-danger " href="{{route('clients.index')}}" id="add-template"><i class="fa fa-chevron-left"></i> Back to Clients</a>
                            </div>
                        </div>
                        @endif
                       
                    </div>
                         @if (Session::has('message'))
                            <div class="col-xs-12 message-box">
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                            </div>
                        @endif
                    
                        
                        @if(count($templates) > 0 )
                        <div class="col-xs-12">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Lines</th>
                                    <th class="col-xs-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($templates as $template)
                                <tr>
                                    <td> {{ $template->type->name }}</td>
                                    <td> {{ $template->lines->count() }}</td>
                                    <td>
                                        @component('admin.clients.templates.components.deletemodal')
                                        @slot('id') 
                                            {{ $template->id }}
                                        @endslot
                                        @slot('client_id') 
                                            {{ $template->client_id }}
                                        @endslot
                                        @slot('template_name') 
                                            {{ $template->type->name }}
                                        @endslot
                                        @endcomponent
                                        <a href="{{ route('client.templates.edit',[$client->id,$template->id])}}" class="btn btn-success btn-xs"><i class="fa fa-pencil"></i> Edit</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        <div class="col-xs-12 text-center">
                            {{ $templates->links() }}
                        </div>
                        @else
                        <div class="col-xs-12">
                            <h5>No Templates found</h5>
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
    
    $("#add-template").on('click',function(){
        var xid =$('#template_type_id').val();
        window.location = '{{ route("client.templates.create",$client->id)}}?type='+ xid;
    });
    
   
});
</script>
@endsection