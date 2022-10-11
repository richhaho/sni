@extends('client.layouts.app')


@section('css')
        <link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
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
                    #page {
                        font-size: 7pt;
                        font-family: ArialNarrow, sans-serif;
                    }
                    
                    
                    }
		
                
                @media screen {
                    #page {
                        @if($view)
                        min-height: 11in;
                        max-height: 11in;
                        max-width:  8.5in;
                        border: solid 1px black;
                        @endif
                        /* to centre page on screen*/
                        margin-left: auto;
                        margin-right: auto;
                        font-size: 7pt;
                        font-family: ArialNarrow, sans-serif;
                    }
                    @if($view)
                    .content {
                        padding-left: 1.20cm;
                        padding-right:  1.20cm;
                        padding-bottom: 0.76cm;
                        padding-top: 0.76cm;
                    }
                    @endif
                }
		* {
                        box-sizing: border-box;
                    }
                
                .text-center  {
                    text-align: center;
                }
                #page  h1 {
                    display: block;
                    font-size: 1.5em;
                    margin-top: 0px;
                    margin-bottom: 0px;
                    font-weight: bold;
                }
                #page  h2 {
                    display: block;
                    font-size: 1.2em;
                    margin-top: 0px;
                    margin-bottom: 0px;
                    font-weight: bold;
                }
                
                #page h3 { 
                    display: block;
                    font-size: 1.1em;
                     margin-top: 0px;
                    margin-bottom: 0px;
                    margin-left: 0;
                    margin-right: 0;
                    font-weight: bold;
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
                
                .signature {
                    border-top: 1px solid black;
                    margin-top: 25px;
                    padding-top:0px;
                }
                .disclaimer {
                        min-height: 8.3in;
                }
                .page-separator {
                    border-bottom: 1px dashed black;
                    margin-bottom: 20px;
                }
                .barcode {
                    margin-top: 1.5cm;
                }
                .warning {
                    font-weight: bold;
                }
                .esignature {
                    font-family: SignatureFont;
                    font-size: 2em;
                    left: 15px;
                     position: relative;
                   
                }
	</style>
@stop


@section('content')
    {!! Form::open(['route'=>['client.release.generate'],'autocomplete' => 'off'])!!}
     {!! Form::hidden('signature',$signature) !!}
     {!! Form::hidden('job_id',$job_id) !!}
     @foreach($selected  as $key => $sel)
     {!! Form::hidden('selected[' . $key .']',$sel) !!}
     @endforeach
            <div class="col-xs-12 pull-left">
            <button type="submit" class="btn  btn-success button-generate" name="generate" value="generate"><i class="fa fa-file-pdf-o"></i> Generate</button>
            <a href="{{ url()->previous() }}" class="btn  btn-danger " ><i class="fa fa-times-circle"></i> Cancel</a>
        </div>
    
    @foreach($pdf_pages as $page)

        @if($page['type'] == 'waiver-and-release-of-lien-upon-final-payment')
        <div class="col-6 pdf-section" id="{{$page['type']}}">
            @include('client.release.warolufp',$page)
        </div>
        <div class="col-6 form-section">
            @include('client.release.forms.warolufp',$page)
        </div>
        @endif
        
        @if($page['type'] == 'waiver-and-release-of-lien-upon-progress-payment')
        <div class="col-6 pdf-section" id="{{$page['type']}}">
            @include('client.release.warolupp',$page)
        </div>
        <div class="col-6 form-section">
            @include('client.release.forms.warolupp',$page)
        </div>
        @endif

        @if($page['type'] == 'conditional-waiver-and-release-of-lien-upon-progress-payment')
        <div class="col-6 pdf-section" id="{{$page['type']}}">
            @include('client.release.cwarolupp',$page)
        </div>
        <div class="col-6 form-section">
            @include('client.release.forms.cwarolupp',$page)
        </div>
        @endif
        
        
         @if($page['type'] == 'conditional-waiver-and-release-of-lien-upon-final-payment')
        <div class="col-6 pdf-section" id="{{$page['type']}}">
            @include('client.release.cwarolufp',$page)
        </div>
        <div class="col-6 form-section">
            @include('client.release.forms.cwarolufp',$page)
        </div>
        @endif

        @if($loop->last)
        @else
        <div class=" col-12 page-separator" ></div>
        @endif

    @endforeach
    {!! Form::close() !!}
@endsection

@section('scripts')
<script src="{{ asset('/vendor/datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script>
$('.button-generate').click(function(){
    $('.button-generate').addClass("disabled");
    $('.button-generate').css('pointer-events','none');
}); 
$(function () {
    $('.date-picker').datepicker({
        autoclose : true,
        orientation: 'auto bottom'
    });
    
    $('.update' ).on('click',function(event){
      
        event.preventDefault();
        
         var page_type = $("input[name='type']").val();
         var data = $('form').serialize();
         console.log(data);
         var url = '{{ route('client.release.update') }}';
          $.ajax({
           type: "POST",
           url: url,
           data: data, // serializes the form's elements.
           success: function(data)
           {
               $('.button-generate').prop('disabled', false);
               $('.update' ).prop('disabled', true);
               $('#' + page_type).html(data);
           }
         });
    });
    
    $('a.add-leaseholder').on('click',function() {
        var xid = $(this).data('id');
        var xindex = $('tr.leaseholders' + xid + ' td').eq(-2).data('id') +1 ;
        var html = '<td data-id="' + xindex + '"><div class="row">';
            html += '<div class="form-group">';
            html += '   <label><a href="#" class="delete-leaseholder" data-id=' + xindex + '><span class="text-danger"><i class="fa fa-times-circle"></i></span></a>Lease Holder Name: </label>';
            html += '   <input class="form-control" name="leaseholders[' + xindex +'][full_name]" type="text" value="">';
            html +='</div>';
            html +='</div>';
            html +='<div class="row">';
            html +='<div class="form-group">';
            html +='    <label>Lease Holder Address: </label>';
            html +='    <textarea class="form-control" rows="5" name="leaseholders[' + xindex +'][address]" cols="50"></textarea>';
            html +='</div>';
            html +='</div></td>';
        $('td.add-cell'+xid).before(html)
        
    }); 
    
     $('a.add-line-party').on('click',function() {
        var page_id = $(this).data('page-id');
        var xindex = $('tbody.parties' + page_id + ' tr').eq(-2).data('id') +1 ;
 
        var html = '<tr data-id="' + xindex + '">';
            html += '<td><input class="form-control" name="parties[' + xindex + '][type]" type="text" value=""></td>';
            html += '<td><input class="form-control" name="parties[' + xindex + '][company_name]" type="text" value=""></td>';
            html += '<td style="padding:5px;"><a href="#" class="delete-line" data-id="' + xindex + '"><span class="text-danger"><i class="fa fa-minus-circle"></i></span></a></td>';
            html +='</tr>';
            
        $('tbody.parties' + page_id + ' tr').eq(-1).before(html)
        
    }); 
    
    $('a.add-line-lienor').on('click',function() {
        var page_id = $(this).data('page-id');
        var xindex = $('tbody.lienors' + page_id + ' tr').eq(-2).data('id') +1 ;
        console.log(xindex);
        var html = '<tr data-id="' + xindex + '">';
            html += '<td><input class="form-control" name="lienors[' + xindex + '][name]" type="text" value=""></td>';
            html += '<td><input class="form-control" name="lienors[' + xindex + '][amount]" type="text" value=""></td>';
            html += '<td style="padding:5px;"><a href="#" class="delete-line-lienor" data-id="' + xindex + '"><span class="text-danger"><i class="fa fa-minus-circle"></i></span></a></td>';
            html +='</tr>';
            
        $('tbody.lienors' + page_id + ' tr').eq(-1).before(html)
        
    }); 
    
    
    $("tr[class^='leaseholders']").on('click','a.delete-leaseholder',function() {
        var xid  = $(this).data('id');
        $("tr[class^='leaseholders'] td[data-id='" + xid + "']").remove();
    });
    
    $("tbody[class^='parties']").on('click','a.delete-line',function() {
        var xid  = $(this).data('id');
        $("tbody[class^='parties'] tr[data-id='" + xid + "']").remove();
    });
    
    $("tbody[class^='lienors']").on('click','a.delete-line-lienor',function() {
        var xid  = $(this).data('id');
        $("tbody[class^='lienors'] tr[data-id='" + xid + "']").remove();
    });
    
     $( 'input, textarea, select' ).on('change',function() {
         
         console.log('hubo un cambio');
         
   
         $('.update').prop('disabled', false);
         $('.button-generate').prop('disabled', true);
     });
});

</script>

@endsection