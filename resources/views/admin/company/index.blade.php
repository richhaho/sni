@extends('admin.layouts.app')

@section('navigation')
    @include('admin.navigation')
@endsection

@section('content')
    {!! Form::open(['route' => ['company.update', $company->id], 'method'=> 'PUT','autocomplete' => 'off']) !!}
        
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">Company Settings
                    <div class="pull-right">
                        <button class="btn btn-success " type="submit"> <i class="fa fa-floppy-o"></i> Save</button>
                        
                    </div>
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
                @if (Session::has('message'))
                    <div class="col-xs-12 message-box">
                    <div class="alert alert-info">{{ Session::get('message') }}</div>
                    </div>
                @endif
                <div class="row">
                  
                    
                    <div class="col-xs-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Company Info
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <label>Company Name:</label>
                                    <input name="name"  value="{{ old("name",$company->name)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <label>Address:</label>
                                    <textarea name="address"  class="form-control" data-toggle="tooltip" data-placement="top" title="" rows='3'>{{ old("address",$company->address)}}</textarea>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xs-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Payeezy Info
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12 form-group">
                                        <label>API Key:</label>
                                        <input name="apikey" value="{{ old("apikey",$company->apikey)}}" placeholder="API Key" class="form-control  noucase" data-toggle="tooltip" data-placement="top" title="">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-md-6 form-group">
                                        <label>API Secret:</label>
                                        <input name="apisecret" value="{{ old("apisecret",$company->apisecret)}}" placeholder="API Secret" class="form-control noucase" data-toggle="tooltip" data-placement="top" title="">
                                    </div>
                                     <div class="col-xs-12 col-md-6 form-group">
                                    <label>Merchant Token:</label>
                                    <input  id="merchant_token" value="{{ old("merchant_token",$company->merchant_token)}}" name="merchant_token" class="form-control noucase" data-toggle="tooltip" data-placement="top" title="" autocomplete="off">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 form-group">
                                        <label>JS Security Token:</label>
                                        <input id="js_security_key" value="{{ old("js_security_key",$company->js_security_key)}}" name="js_security_key" class="form-control noucase" data-toggle="tooltip" data-placement="top" title=""  autocomplete="off">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-xs-12 form-group">
                                        <label>TA token:</label>
                                        <input id="ta_token" value="{{ old("ta_token",$company->ta_token)}}" name="ta_token" class="form-control noucase" data-toggle="tooltip" data-placement="top" title=""  autocomplete="off">
                                    </div>
                                </div>
                             
                                <div class="row">
                                    <div class="col-xs-12 form-group">
                                    <label>Payeezy Mode:</label>
                                   <div class="checkbox checkbox-slider--b-flat" style="margin-left: 40px;">
                                        <label>
                                            @if($company->payeezy_mode=="live")
                                            <input name="payeezy_mode" type="checkbox" checked><span></span>
                                            @else
                                            <input name="payeezy_mode" type="checkbox"><span></span>
                                            @endif
                                        </label>
                                    </div>
                                    <div >Sandbox&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Live</div>
                                    </div>   
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            
               
            </div>
            <!-- /.container-fluid -->
            
        </div>
    {!! Form::close() !!}
@endsection

@section('scripts')
<script>
 
    
    
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
$(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });

})
</script>
    
@endsection