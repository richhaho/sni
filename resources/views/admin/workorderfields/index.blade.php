@extends('admin.layouts.app')

@section('navigation')
    @include('admin.navigation')
@endsection

@section('css')
<link href="{{asset('vendor/bootstrap-datarange/css/daterangepicker.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<style>
    #filters-form {
        margin-bottom: 15px;
        *margin-top: 15px;
    }
     #filters-form div.row {
         margin-top: 15px;
     }
         
    input[name="daterange"] {
            min-width: 180px;
    }
        td.job_name  {
       line-height: 0.8!important;
    }

    td.job_name span{
        font-size: 0.8em;
    }
    .job_name_align{
        word-break: break-all;
    }
    .phonecall{
         word-break: break-all;
    }
</style>
@endsection


@section('content')
            <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12">
                        <h1 class="page-header">Work Order Fields
                            <a class="btn btn-success pull-right" href="{{ route('workorderfields.create')}}"><i class="fa fa-plus"></i> New Field</a>
                        </h1>
                       
                    </div>
                        <div class="col-xs-12" id="filters-form">
                            {!! Form::open(['route' => 'workorderfields.setfilter', 'class'=>'form-inline'])!!}
                             
                            <div class="row">
                                <div class="form-group">
                                    <label for="job_type_filter">Type: </label>
                                    {!! Form::select('work_type',$wo_types,session('work_order_field.work_type'),['class'=>'form-control'])!!}
                                </div>
                                 
                            <button class="btn btn-success" type="submit" ><i class="fa fa-filter"></i> Enter</button>
                              
                        </div>
                            {!! Form::close() !!}
                           
                        </div>
                    
                        @if (Session::has('message'))
                            <div class="col-xs-12 message-box">
                            <div class="alert {{ Session::get('message-class','alert-info') }}">{{ Session::get('message') }}</div>
                            </div>
                        @endif
                    
                        
                        @if(count($workfields) > 0 )
                        <div class="col-xs-12">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Work Order Type</th>
                                    <th>Field Order</th>
                                    <th>Field Label</th>
                                    <th>Field Type</th>
                                    <th>Field Required</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($workfields as $work)
                                <tr>
                                    <td> {{$wo_types[$work->workorder_type ]}}</td>
                                    <td> {{$work->field_order}}</td>
                                    <td> {{$work->field_label}}</td>
                                    <td> {{$field_type[$work->field_type]}}</td>
                                    <td> {{$required[$work->required]}}</td>

                                    
                                    <td>
                                        <div class="btn-group pull-right dropup">
                                            <button type="button" class="btn btn-default dropdown-toggle " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-cogs"></i> Actions <span class="caret"></span>
                                            </button>
                                    @component('admin.workorderfields.components.deletemodal')
                                        @slot('id') 
                                            {{ $work->id }}
                                        @endslot
                                    @endcomponent
                                     
                                        <li>
                                            <a class=" " href="{{ route('workorderfields.edit',$work->id)}}"><i class="fa fa-pencil"></i> Edit</a>
                                        </li>
                                        
                                    </ul>
                                      </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        <div class="col-xs-12 text-center">
                            {{ $workfields->links() }}
                        </div>
                        @else
                        <div class="col-xs-12">
                            <h5>No Work Order Fields found</h5>
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
    $(".message-box").fadeTo(3000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
});
</script>
@endsection