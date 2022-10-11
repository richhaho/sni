@extends('layouts.app')

@section('content')
<div class="container">
        <div class="row">
                <div class="col-xs-2 col-xs-offset-5">
                <img class="img-responsive" src="{{ asset('images/logo.png')}}" alt="">
                </div>
        
        </div>
            <div>&nbsp;</div>
            <br>
        <div class="col-xs-12 col-md-12 col-lg-12">
          <center><h2>Your account is pending approval. </h2></center> <br>
          <center><h2>We will contact you to complete account setup.</h2></center>
        </div>    

    
</div>
@endsection
