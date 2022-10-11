@extends('client.layouts.app')

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
    @include('client.navigation')
@endsection

@section('content')
    
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">
                    <h1 class="page-header">Setup Card 
                        
                    <div class="pull-right">

                    </div>
                       
                </h1> 
                    
                </h1>       
            </div>
            </div>
        </div>
            <div id="page-wrapper">
            
            
                
                <div class ="col-xs-4">
                     <h4><i class="fa fa-lock"></i> Payment Setup required.</h4>

                        <p>Before you can pay invoices you must first setup a credit card on file.</p>
                        <img  class="img-responsive col-xs-12 " src="{{ asset('/images/payeezylogo.png') }}" alt=""/>
                    </div> 
                 
                <div class="col-xs-8">
                    {!! Form::open(['route' => 'client.invoicesbatches.index','method'=> 'GET'])!!}
                   
                    <div class="col-xs-4 pull-right">
                            <button type="submit" class="btn  btn-pay btn-block btn-danger form-control"> <i class="fa fa-close"></i> &nbsp; Cancel</button>
                       
                    </div>

                    </form>

                    {!! Form::open(['route' => 'creditcard.index','method'=> 'GET'])!!}
                   
                    <div class="col-xs-4  pull-right">
                            <button type="submit" class="btn btn-success btn-pay btn-block form-control"> <i class="fa fa-money"></i> &nbsp; Setup Card</button>
                       
                    </div>

                    </form>
                </div>
           
               
            </div>
            <!-- /.container-fluid -->
            
        </div>
   
@endsection

@section('scripts')

<script>

$('.btn-pay').click(function(){
    $('.btn-pay').addClass("disabled");
    $('.btn-pay').css('pointer-events','none');
});
 
</script>
    
@endsection