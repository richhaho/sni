@extends('researcher.layouts.app')

@section('navigation')
    @include('researcher.navigation')
@endsection

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/tooltipster/css/tooltipster.bundle.min.css') }}" rel="stylesheet" type="text/css">
<style>
       .tooltip_templates { display: none; } 
</style>
@endsection


@section('content')
            <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12">
                        <h1 class="page-header">Price List</h1>
                       
                    </div>
                    <div>&nbsp;</div>
                    @if (count($errors) > 0)
                    <div class="col-xs-12 message-box">
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @endif
                    <div class="row">
                       
                            {!! Form::open (['route'=>['pricelist.store']]) !!}
                            <div class="col-md-4">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Add Item</h4>
                                    </div>
                                    <div class="panel-body">
            
                                        <div class="row">
                                            <div class="col-xs-12 form-group">
                                                <label>Name:</label>
                                                {!!  Form::text('new_description',"",['class' => 'form-control']) !!}
                                            </div>
                                         </div>
                                        <div class="row">
                                            <div class="col-xs-12 form-group">
                                                <label>Price:</label>
                                                {!!  Form::number('new_price',"",['class' => 'form-control','min'=>'0','step'=>'0.01']) !!}
                                            </div>

                                         </div>
                                  
                                        
                                        <div class='row'>
                                            <div class="col-xs-12 form-group ">
                                           {!! Form::submit('Save',['class'=>'btn btn-success pull-right']); !!}
                                           </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {!! Form::close(); !!}
                       
                        @if (Session::has('message'))
                           <div class="col-md-8 message-box">
                           <div class="alert alert-info">{{ Session::get('message') }}</div>
                           </div>
                       @endif
                        <div class="col-md-8">
                               
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">Price List</h4>
                                </div>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Price</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($list as $item) 
                                        <tr>
                                            <td>{{ $item->description }}</td>
                                            <td>{{ $item->price }}</td>
                                            <td>
                                                    @component('researcher.pricelist.components.deletemodal')
                                                        @slot('id') 
                                                            {{ $item->id }}
                                                        @endslot
                                                        @slot('item_name') 
                                                            {{ $item->description }}
                                                        @endslot
                                                    @endcomponent
                                                    @include('researcher.pricelist.components.editmodal')
                                                
                                            </td>   
                                        </tr>
                                        @endforeach
                                    </tbody>
                                        
                                </table>
                            </div>  
                        </div>
                        
                    </div>
                    
         
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
@endsection

@section('scripts')
<script src="{{ asset('/vendor/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/bootstrap-filestyle/js/bootstrap-filestyle.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/tooltipster/js/tooltipster.bundle.min.js') }}" type="text/javascript"></script>
<script>

    
$(function () {
    
    
    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
    
     $('.tooltipster').tooltipster();
     
   
  
});
</script>
@endsection