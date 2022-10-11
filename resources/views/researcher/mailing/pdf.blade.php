<!DOCTYPE html>
<html>
<head>
	<style type="text/css" >
                @font-face {
                   font-family:ArialNarrow;
                    src:url('{{asset("/fonts/arial-narrow.ttf")}}');
                }
            
            
		@page {
			size: letter portrait; /* can use also 'landscape' for orientation */
		}
                
                @media print {
                    size: letter portrait; /* can use also 'landscape' for orientation */
                    
                    body {
                        font-size: 10pt;
                        font-family: ArialNarrow, sans-serif;
                    }
                    .content {
                        min-height: 31cm;
                        max-height: 31cm;
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
                    
                }
                
		* {
                        box-sizing: border-box;
                    }
                    
               
                
                .text-justify {
                    text-align: justify;
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
                h1 {
                    margin-top: 0px;
                    margin-bottom: 0px;
                }
                h2 {
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
                
                .disclaimer {
                        height: 8.3in;
                }
                .barcode {
                    padding-top: 0.80cm;
                }
                .barcode .number {
                    padding-top: 0.3cm;
                    font-family: monospace;
                    font-size: 2em;
                }
                .mailing-address {
                    padding-top: 2.95cm;
                    padding-left: 49%;
                    font-size: 1.5em;
                    font-weight:bold;
                    
                }
                
                .mailing-address-no-barcode {
                    padding-top: 6.38cm;
                    padding-left: 49%;
                    font-size: 1.5em;
                    font-weight:bold;   
                }
               
                .last-l1 { 
                    
                    position: absolute; 
                    bottom: 30px; 
                    left: 0px; 
                    right: 0px; 
                    
                    width: 100%;
                   
                }
                
                .last-l2 { 
                    
                    position: absolute; 
                    bottom: 0px; 
                    left: 0px; 
                    right: 0px; 
                   
                    width: 100%;
                   
                }
                
                .warning {
                    font-weight: bold;
                }
	</style>
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