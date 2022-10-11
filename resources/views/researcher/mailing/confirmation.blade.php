@extends('researcher.layouts.app')


@section('css')
<style>
    
        #page-wrapper {
        margin-left: 0px
    }
    #top-wrapper {
        margin-left: 0px
    }
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
                        <div class="col-xs-12 ">
                            <h1 class="" > FTP Printing<br><small>The files have been sent</small></h1>
                        </div>
                       
                        
                    </div>
                     <div>&nbsp;</div>
                       <div class="col-xs-12 ">
                            <div class="col-md-12 text-right">
                                <a class="btn btn-danger pull-right" href="#"  onclick="window.top.close();" id="add-batch"><i class="fa fa-times"></i> Close</a>
                            </div>
                        </div> 
                    <div>&nbsp;</div>
             
                      
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
});


</script>
@endsection
