@extends('researcher.layouts.app')

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
<style>
    .table > tbody > tr > .no-line {
      border-top: none;
  }

  .table > thead > tr > .no-line {
      border-bottom: none;
  }

  .table > tbody > tr > .thick-line {
      border-top: 2px solid;
  }
</style>

@endsection

@section('navigation')
    @include('researcher.navigation')
@endsection

@section('content')
    
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">
                    <h1 class="page-header">Payment Decline
                    
                       
                </h1> 
                    
                </h1>       
            </div>
            </div>
        </div>
            <div id="page-wrapper">
            
            <div class="container-fluid">
                
                @if (count($errors) > 0)
                    <div class="alert alert-danger">            
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class ="col-xs-12">
                    Your Payment has been declined, please check with your financial institution, and try again, you can change your credit card under the Settings Menu, and try a new payment under the Invoices menu.
                </div>
                <div class="col-md-12 text-right">
                    <a href="{{route('invoices.index')}}" class="btn btn-success"><i class="fa fa-bar-chart"></i> Back to Invoices</a>
                </div>
            </div>
            <!-- /.container-fluid -->
            
        </div>
   
@endsection

@section('scripts')

<script>

</script>
    
@endsection