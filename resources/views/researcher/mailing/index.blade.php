@extends('researcher.layouts.app')

@section('navigation')
    @include('researcher.navigation')
@endsection
@section('css')
<style>
    h1.with-buttons {
        display: block;
        width: 100%;
        float: left;
    }
    .page-header h1 { margin-top: 0; }
    
     #filters-form {
        margin-bottom: 15px;
        margin-top: 15px;
    }
    
    input[name="daterange"] {
            min-width: 180px;
    }
</style>
@endsection

@section('content')
            <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="page-header col-xs-12">
                        <div class="col-xs-12">
                            <h1 class="" > Mailing Batches</h1>
                            <div class="text-right">
                        
                                <a class="btn btn-success " target="_blank" href="{{route('mailing.create')}}" id="add-batch"><i class="fa fa-plus"></i> New Batch</a>
                                <a class="btn btn-primary" onclick="location.reload(true);"><i class="fa fa-refresh"></i> Reload</a>
                    
                        </div>
                        </div>
                        
                        
                    </div>
                       
                    
                         @if (Session::has('message'))
                            <div class="col-xs-12 message-box">
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                            </div>
                        @endif
                    
                        
                        @if(count($batches) > 0 )
                        <div class="col-xs-12">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Date Created</th>
                                    <th>Number of Documents</th>
                                    <th class="col-xs-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($batches as $batch)
                                <tr>
                                    <td> {{ $batch->id  }}</td>
                                    <td> {{ $batch->created_at  }}</td>
                                    <td> {{ count($batch->details)}}</td>
                                    
                                    <td>
                                        @component('researcher.mailing.components.deletemodal')
                                        @slot('id') 
                                            {{ $batch->id }}
                                        @endslot
                                        @endcomponent
                                        <a href="{{ route('mailing.show',$batch->id)}}" class="btn btn-success btn-xs"><i class="fa fa-eye"></i> View Batch</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        <div class="col-xs-12 text-center">
                            {{ $batches->links() }}
                        </div>
                        @else
                        <div class="col-xs-12">
                            <h5>No Mailing Batch found</h5>
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