<!DOCTYPE html>
<html>
<head>
     <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
	<style type="text/css" >
                @font-face {
                   font-family:ArialNarrow;
                    src:url('{{asset("/fonts/arial-narrow.ttf")}}');
                }
            
                @font-face {
                   font-family:SignatureFont;
                   src:url('{{asset("/fonts/simple-signature.regular.ttf")}}');
                }
            
		@page {
			size: letter portrait; /* can use also 'landscape' for orientation */
			
                        

		}
                
                @media print {
                    body {
                        font-size: 11pt;
                        font-family: ArialNarrow, sans-serif;
                    }
                     .text-justify {
                        text-align: justify;
                        
                    }

                   .content-back {
                    min-height: 7.4in;
                   
                    padding-bottom: 0;
                        padding-top: 0.76cm;
                }
                
                .footer-back {
                    font-size: 10.5pt;
                    line-height: 14pt;
                }


                }
		
                
                @media screen {
                    #page {

                        /* to centre page on screen*/
                        margin-left: auto;
                        margin-right: auto;
                        font-size: 10pt;
                        font-family: ArialNarrow, sans-serif;
                    }
                    .text-justify {
                    text-align: justify;
                   
                    }
                    
                }
		* {
                        box-sizing: border-box;
                    }
                
               
                .text-center  {
                    text-align: center;
                }
                .text-bold {
                    font-weight:bold;
                }
                .text-right {
                    text-align: right;
                }
                .text-left {
                    text-align: left;
                }
               #page  h1 {
                    display: block;
                    font-size: 2em;
                    margin-top: 0px;
                    margin-bottom: 0px;
                    font-weight: bold;
                }
                #page  h2 {
                    display: block;
                    font-size: 1.3em;
                    margin-top: 0px;
                    margin-bottom: 0px;
                    font-weight: bold;
                }
                
                #page h3 { 
                    display: block;
                    font-size: 1.2em;
                     margin-top: 0px;
                    margin-bottom: 0px;
                    margin-left: 0;
                    margin-right: 0;
                    font-weight: bold;
                }
                
                 h4 {
                    margin-top: 0px;
                    margin-bottom: 0px;
                }
                 h5 {
                    margin-top: 0px;
                    margin-bottom: 0px;
                }
                
                .col-1 {width: 8.33%;}
                .col-2 {width: 16.66%;}
                .col-3 {width: 25%;}
                .col-4 {width: 33.33%;}
                .col-5 {width: 41.66%;}
                .col-6 {width: 50%;}
                .col-7 {width: 58.33%;}
                .col-8 {width: 66.66%;}
                .col-9 {width: 75%;}
                .col-10 {width: 83.33%;}
                .col-11 {width: 91.66%;}
                .col-12 {width: 100%;}
                
                .row{
                margin-right: 0px;
                margin-left: 0px;
                }
                
                [class*="col-"] {
                    float: left;
                    padding: 5px;
                }
                
                .row::after {
                    content: "";
                    clear: both;
                    display: table;
                }
                
                
                .rounded-box {
                    border: 1px solid black;
                    border-radius: 5px;
                }
                
                .square-box {
                    border: 1px solid black;
                }
                
                .square-box-no-top {
                    border-bottom:  1px solid black;
                    border-right:  1px solid black;
                    border-left:  1px solid black;
                }
                
                .signature {
                    border-top: 1px solid black;
                    margin-top: 25px;
                    padding-top:0px;
                }
                
                .mailing-address {
                    font-size: 1.5em;
                    font-weight:bold;
                    
                }
                

                .warning {
                    font-weight: bold;
                    font-size:11pt!important;
                }
                
                .esignature {
                    font-family: SignatureFont;
                    font-size: 2em;
                    left: 30px;
                     position: relative;
                   
                }
              
	</style>
        @yield('css')
</head>

<body>
      @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @yield('content')
</body>
</html>