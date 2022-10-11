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
                        <div class="col-xs-12  col-md-10">
                            <h1 class="" > Preview</h1>
                        </div>
                        <div class="col-xs-12 col-md-2">
                            <div class="col-md-12 ">
                                <a class="btn btn-success pull-right" href="{{route('mailing.index')}}" id="add-batch"><i class="fa fa-chevron-left"></i> Back</a>
                            </div>
                        </div>
                        
                    </div>
                       
                    
                         @if (Session::has('message'))
                            <div class="col-xs-12 message-box">
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                            </div>
                        @endif
                       <div class="col-xs-12"> 
                          <embed src="{{route('mailing.view',$id)}}#toolbar=0&navpanes=0&scrollbar=0" type='application/pdf' style="width: 100%">
                        </div>
                    
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
@endsection

@section('scripts')
<script>
    function resizeEmbed(){
         var topOffset = 50;
        var width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse');
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse');
        }

        var height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        height = height - topOffset - 120;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("embed").css("max-height", (height) + "px");
            $("embed").css("min-height", (height) + "px");
            //$("#page-wrapper").css("min-height", 90 + "vh");
        }
    }
$(function () {
    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
    resizeEmbed();
    $(window).on("resize", function() {
        
        resizeEmbed();
    });
    
   
});

(function() {
    var beforePrint = function() {
        console.log('Functionality to run before printing.');
    };
    var afterPrint = function() {
        console.log('Functionality to run after printing');
    };

    if (window.matchMedia) {
        var mediaQueryList = window.matchMedia('print');
        mediaQueryList.addListener(function(mql) {
            if (mql.matches) {
                beforePrint();
            } else {
                afterPrint();
            }
        });
    }

    window.onbeforeprint = beforePrint;
    window.onafterprint = afterPrint;
}());
</script>
@endsection