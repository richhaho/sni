@extends('admin.layouts.app')


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
                        font-size: 7.7pt;
                        font-family: ArialNarrow, sans-serif;
                    }
                    
                    
                    }
		
                
                @media screen {
                    #page {
                        @if($view)
                        min-height: 12in;
                        max-height: 12in;
                        max-width:  8.5in;
                        border: solid 1px black;
                        @endif
                        /* to centre page on screen*/
                        margin-left: auto;
                        margin-right: auto;
                        font-size: 9pt;
                        font-family: ArialNarrow, sans-serif;
                    }
                    @if($view)
                    .content {
                        min-height: 10in;
                        padding-left: 1.20cm;
                        padding-right:  1.20cm;
                        padding-bottom: 0;
                        padding-top: 0.76cm;
                    }
                    
                    .footer {
                        padding-left: 1.20cm;
                        padding-right:  1.20cm;
                        padding-bottom: 0.76cm;
                        
                    }
                    @endif
                }
		* {
                        box-sizing: border-box;
                    }
                
                .text-center  {
                    text-align: center;
                }
              
                 .text-right {
                 text-align: right;
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
                    font-size: 1.5em;
                    margin-top: 0px;
                    margin-bottom: 0px;
                    font-weight: bold;
                }
                
                #page h3 { 
                    display: block;
                    font-size: 1.17em;
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
           
                .page-separator {
                    border-bottom: 1px dashed black;
                    margin-bottom: 20px;
                }
                .barcode {
                    margin-top: 1.5cm;
                }
                .warning {
                    font-size: 8.7pt;
                    font-weight: bold;
                }
                  .esignature {
                    font-family: SignatureFont;
                    font-size: 2em;
                    left: 30px;
                     position: relative;
                   
                }
                
                .content-back {
                    min-height: 5.3in;
                    padding-left: 1.20cm;
                    padding-right: 1.20cm;
                    padding-bottom: 0;
                    padding-top: 0.76cm;
                }

                .footer-back {
                    padding-left: 1.20cm;
                    padding-right: 1.20cm;
                    padding-top: 0.76cm;

                }
	</style>
@stop


@section('content')
    
            <div class="col-xs-12 pull-left">
            
            {!! Form::open(['route'=>['pdfpage.generate',$work_order_id],'autocomplete' => 'off','class' => 'form-inline','id'=>'generate','style' => 'display: inline-block;'])!!}
                {{ Form::hidden('work_order_id', $work_order_id,['id' => 'work_order_id']) }}
                <button type="submit" class="btn  btn-success button-generate" name="generate" value="generate"><i class="fa fa-file-pdf-o"></i> Generate</button>
            {!! Form::close() !!}
            {!! Form::open(['route'=>['pdfpage.AttachPDF',$work_order_id],'autocomplete' => 'off','class' => 'form-inline','style' => 'display: inline-block;'])!!}
                {{ Form::hidden('loading', 'start') }}
                <button type="submit" class="btn  btn-success button-AttachPDF button-generate" name="AttachPDF" value="preview"><i class="fa fa-eye-slash"></i> Attach PDF and Generate</button>
            {!! Form::close() !!}
            {!! Form::open(['route'=>['pdfpage.preview',$work_order_id],'autocomplete' => 'off','target' => '_blank','class' => 'form-inline','style' => 'display: inline-block;'])!!}
                <button type="submit" class="btn  btn-success button-generate" name="generate" value="preview"><i class="fa fa-eye-slash"></i> Preview PDF</button>
            {!! Form::close() !!}
            {!! Form::open(['route'=>['workorders.cancel',$work_order_id],'autocomplete' => 'off','class' => 'form-inline','style' => 'display: inline-block;'])!!}
                {{ Form::hidden('backurl', $backurl) }}
                <button type="submit" class="btn  btn-danger button-generate" name="generate" value="preview"><i class="fa fa-eye-slash"></i> Cancel</button>
            {!! Form::close() !!}

            
               
           
        </div>
    
@foreach($pdf_pages as $page)
    
    
    @if($page['type'] == 'amend-claim-of-lien')
    <div class="col-6 pdf-section" id="{{$page['type'].$page['page_id']}}">
        @include('admin.pdf.acol',$page)
    </div>
    <div class="col-6 form-section">
        @include('admin.pdf.forms.acol',$page)
    </div>
    @endif
    
    @if($page['type'] == 'claim-of-lien')
    <div class="col-6 pdf-section" id="{{$page['type'].$page['page_id']}}">
        @include('admin.pdf.col',$page)
    </div>
    <div class="col-6 form-section">
        @include('admin.pdf.forms.col',$page)
    </div>
    @endif
    
     @if($page['type'] == 'conditional-waiver-and-release-of-lien-upon-final-payment')
    <div class="col-6 pdf-section" id="{{$page['type'].$page['page_id']}}">
        @include('admin.pdf.cwarolufp',$page)
    </div>
    <div class="col-6 form-section">
        @include('admin.pdf.forms.cwarolufp',$page)
    </div>
    @endif
    
     @if($page['type'] == 'conditional-waiver-and-release-of-lien-upon-progress-payment')
    <div class="col-6 pdf-section" id="{{$page['type'].$page['page_id']}}">
        @include('admin.pdf.cwarolupp',$page)
    </div>
    <div class="col-6 form-section">
        @include('admin.pdf.forms.cwarolupp',$page)
    </div>
    @endif
    
    
     @if($page['type'] == 'contractors-final-payment-affidavit')
    <div class="col-6 pdf-section" id="{{$page['type'].$page['page_id']}}">
        @include('admin.pdf.cfpa',$page)
    </div>
    <div class="col-6 form-section">
        @include('admin.pdf.forms.cfpa',$page)
    </div>
    @endif
    
    
    @if($page['type'] == 'notice-of-bond')
    <div class="col-6 pdf-section" id="{{$page['type'].$page['page_id']}}">
        @include('admin.pdf.nob',$page)
    </div>
    <div class="col-6 form-section">
        @include('admin.pdf.forms.nob',$page)
    </div>
    @endif
    
    @if($page['type'] == 'notice-of-commencement')
    <div class="col-6 pdf-section" id="{{$page['type'].$page['page_id']}}">
        @include('admin.pdf.noc',$page)
    </div>
    <div class="col-6 form-section">
        @include('admin.pdf.forms.noc',$page)
    </div>
    @endif
    @if($page['type'] == 'notice-of-termination')
    <div class="col-6 pdf-section" id="{{$page['type'].$page['page_id']}}">
        @include('admin.pdf.not',$page)
    </div>
    <div class="col-6 form-section">
        @include('admin.pdf.forms.not',$page)
    </div>
    @endif
    
    @if($page['type'] == 'notice-to-owner')
        <div class="col-6 pdf-section" id="{{$page['type'].$page['page_id']}}">
            @include('admin.pdf.nto',$page)
        </div>
        <div class="col-6 form-section">
            @include('admin.pdf.forms.nto',$page)
        </div>
    @endif
    
    @if($page['type'] == 'notice-to-owner-back')

    <div class="col-6 pdf-section" id="{{$page['type'].$page['page_id']}}">
        @include('admin.pdf.ntoback',$page)
    </div>
    <div class="col-6 form-section">
        @include('admin.pdf.forms.ntoback',$page)
    </div>
    @endif

    @if($page['type'] == 'amended-notice-to-owner')
        <div class="col-6 pdf-section" id="{{$page['type'].$page['page_id']}}">
            @include('admin.pdf.anto',$page)
        </div>
        <div class="col-6 form-section">
            @include('admin.pdf.forms.anto',$page)
        </div>
    @endif
    
    @if($page['type'] == 'amended-notice-to-owner-back')

    <div class="col-6 pdf-section" id="{{$page['type'].$page['page_id']}}">
        @include('admin.pdf.antoback',$page)
    </div>
    <div class="col-6 form-section">
        @include('admin.pdf.forms.antoback',$page)
    </div>
    @endif
    
    
     @if($page['type'] == 'notice-of-contest-of-claim-against-payment-bond')

    <div class="col-6 pdf-section" id="{{$page['type'].$page['page_id']}}">
        @include('admin.pdf.nococapb',$page)
    </div>
    <div class="col-6 form-section">
        @include('admin.pdf.forms.nococapb',$page)
    </div>
    @endif
    
     @if($page['type'] == 'notice-of-contest-of-lien')

    <div class="col-6 pdf-section" id="{{$page['type'].$page['page_id']}}">
        @include('admin.pdf.nocol',$page)
    </div>
    <div class="col-6 form-section">
        @include('admin.pdf.forms.nocol',$page)
    </div>
    @endif
    
    @if($page['type'] == 'notice-of-non-payment')
    <div class="col-6 pdf-section" id="{{$page['type'].$page['page_id']}}">
        @include('admin.pdf.nonp',$page)
    </div>
    <div class="col-6 form-section">
        @include('admin.pdf.forms.nonp',$page)
    </div>
    @endif
    @if($page['type'] == 'notice-of-nonpayment-for-bonded-private-jobs-statutes-713')
    <div class="col-6 pdf-section" id="{{$page['type'].$page['page_id']}}">
        @include('admin.pdf.nonp713',$page)
    </div>
    <div class="col-6 form-section">
        @include('admin.pdf.forms.nonp713',$page)
    </div>
    @endif
    @if($page['type'] == 'notice-of-nonpayment-for-government-jobs-statutes-255')
    <div class="col-6 pdf-section" id="{{$page['type'].$page['page_id']}}">
        @include('admin.pdf.nonp255',$page)
    </div>
    <div class="col-6 form-section">
        @include('admin.pdf.forms.nonp255',$page)
    </div>
    @endif
    
    @if($page['type'] == 'notice-of-nonpayment-with-intent-to-lien-andor-foreclose')

    <div class="col-6 pdf-section" id="{{$page['type'].$page['page_id']}}">
        @include('admin.pdf.nonwitlaf',$page)
    </div>
    <div class="col-6 form-section">
        @include('admin.pdf.forms.nonwitlaf',$page)
    </div>
    @endif
    
    @if($page['type'] == 'out-of-state-nto-preliminary-notice-of-lien-rights')
    <div class="col-6 pdf-section" id="{{$page['type'].$page['page_id']}}">
        @include('admin.pdf.pnolr',$page)
    </div>
    <div class="col-6 form-section">
        @include('admin.pdf.forms.pnolr',$page)
    </div>
    @endif

    @if($page['type'] == 'rescission-letter')
    <div class="col-6 pdf-section" id="{{$page['type'].$page['page_id']}}">
        @include('admin.pdf.rl',$page)
    </div>
    <div class="col-6 form-section">
        @include('admin.pdf.forms.rl',$page)
    </div>
    @endif
    
      @if($page['type'] == 'partial-satisfaction-of-lien')
    <div class="col-6 pdf-section" id="{{$page['type'].$page['page_id']}}">
        @include('admin.pdf.psol',$page)
    </div>
    <div class="col-6 form-section">
        @include('admin.pdf.forms.psol',$page)
    </div>
    @endif
    
    @if($page['type'] == 'satisfaction-of-lien')
    <div class="col-6 pdf-section" id="{{$page['type'].$page['page_id']}}">
        @include('admin.pdf.sol',$page)
    </div>
    <div class="col-6 form-section">
        @include('admin.pdf.forms.sol',$page)
    </div>
    @endif
    
     @if($page['type'] == 'sworn-statement-of-account')
    <div class="col-6 pdf-section" id="{{$page['type'].$page['page_id']}}">
        @include('admin.pdf.ssoa',$page)
    </div>
    <div class="col-6 form-section">
        @include('admin.pdf.forms.ssoa',$page)
    </div>
    @endif

     @if($page['type'] == 'waiver-and-release-of-lien-upon-final-payment')
    <div class="col-6 pdf-section" id="{{$page['type'].$page['page_id']}}">
        @include('admin.pdf.warolufp',$page)
    </div>
    <div class="col-6 form-section">
        @include('admin.pdf.forms.warolufp',$page)
    </div>
    @endif
    
    @if($page['type'] == 'waiver-and-release-of-lien-upon-progress-payment')
    <div class="col-6 pdf-section" id="{{$page['type'].$page['page_id']}}">
        @include('admin.pdf.warolupp',$page)
    </div>
    <div class="col-6 form-section">
        @include('admin.pdf.forms.warolupp',$page)
    </div>
    @endif
    
     @if($page['type'] == 'waiver-of-right-to-claim-against-bond-final-payment')
    <div class="col-6 pdf-section" id="{{$page['type'].$page['page_id']}}">
        @include('admin.pdf.wortcabfp',$page)
    </div>
    <div class="col-6 form-section">
        @include('admin.pdf.forms.wortcabfp',$page)
    </div>
    @endif
    
     @if($page['type'] == 'waiver-of-right-to-claim-against-bond-progress-payment')
    <div class="col-6 pdf-section" id="{{$page['type'].$page['page_id']}}">
        @include('admin.pdf.wortcabpp',$page)
    </div>
    <div class="col-6 form-section">
        @include('admin.pdf.forms.wortcabpp',$page)
    </div>
    @endif
    
    @if($loop->last)
    @else
    <div class=" col-12 page-separator" ></div>
    @endif
    
@endforeach
@endsection

@section('scripts')
<script src="{{ asset('/vendor/datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script>
$(function () {
    $('.date-picker').datepicker({
        autoclose : true,
        orientation: 'auto bottom'
    });
    
    $( 'form.save-page' ).submit( function(event){
        event.preventDefault();
         var page_id = $(this).data('id');
         var page_type = $(this).data('type');
         var data = $(this).serialize();
         var url = $(this).attr('action');
          $.ajax({
           type: "POST",
           url: url,
           data: data, // serializes the form's elements.
           success: function(data)
           {
               if (data =='kicked') {
                    var  wid =$('#work_order_id').val();
                    window.location.replace("{{ url('admin/workorders/kickout')}}");
               } else {
                    $('.button-generate').prop('disabled', false);
                    $('.submit' + page_id ).prop('disabled', true);
                    $('#' + page_type + page_id).html(data);
                }
           }
         });
    });
    
    $('a.add-leaseholder').on('click',function() {
        var xid = $(this).data('id');
        var xindex = $('tr.leaseholders' + xid + ' td').eq(-2).data('id') +1 ;
         if(isNaN(xindex)) { xindex=0; }
        var html = '<td data-id="' + xindex + '"><div class="row">';
            html += '<div class="row">';
            html += '<div class="col-xs-12">';
            html += '<div class="form-group">';
            html += '   <label><a href="#" class="delete-leaseholder" data-id=' + xindex + '><span class="text-danger"><i class="fa fa-times-circle"></i></span></a>Lease Holder Name: </label>';
            html += '   <input class="form-control" name="leaseholders[' + xindex +'][full_name]" type="text" value="">';
            html +='</div>';
            html +='</div>';
            html +='</div>';
            html +=' <div class="row">';
            html +='     <div class="col-xs-6">';
            html +='            <div class="form-group">';
            html +='                <label>Lease Holder Phone: </label>';
            html +='                <input class="form-control" name="leaseholders[' + xindex +'][phone]" type="text" value="">';
            html +='            </div>';
            html +='      </div>';
            html +='      <div class="col-xs-6">';
            html +='            <div class="form-group">';
            html +='           <label>Lease Holder Email: </label>';
            html +='                <input class="form-control" name="leaseholders[' + xindex +'][email]" type="text" value="">';
            html +='            </div>';
            html +='        </div>';
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
        if(isNaN(xindex)) { xindex=0; }
        var html = '<tr data-id="' + xindex + '">';
            //html += '<td><input class="form-control" name="parties[' + xindex + '][type]" type="text" value=""></td>';
            html += '<td><input class="form-control" name="parties[' + xindex + '][company_name]" type="text" value=""></td>';
            html += '<td style="padding:5px;"><a class="delete-line" data-id="' + xindex + '" style="cursor:pointer;"><span class="text-danger"><i class="fa fa-minus-circle"></i></span></a></td>';
            html +='</tr>';
            
        $('tbody.parties' + page_id + ' tr').eq(-1).before(html)
        $('.btn-success').prop('disabled', false);
    }); 
    $('a.add-line-document').on('click',function() {
        var page_id = $(this).data('page-id');
        var xindex = $('tbody.documents_list' + page_id + ' tr').eq(-2).data('id') +1 ;
        if(isNaN(xindex)) { xindex=0; }
        var html = '<tr data-id="' + xindex + '">';
            html += '<td><input class="form-control" name="documents_list[' + xindex + '][document_name]" type="text" value=""></td>';
            html += '<td style="padding:5px;"><a style="cursor:pointer;" class="delete-line" data-id="' + xindex + '"><span class="text-danger"><i class="fa fa-minus-circle"></i></span></a></td>';
            html +='</tr>';
            
        $('tbody.documents_list' + page_id + ' tr').eq(-1).before(html)
        
    });  
    
    $('a.add-line-lienor').on('click',function() {
        var page_id = $(this).data('page-id');
        var xindex = $('tbody.lienors' + page_id + ' tr').eq(-2).data('id') +1 ;
         if(isNaN(xindex)) { xindex=0; }
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
        $('.btn-success').prop('disabled', false);
    });
    
    $("tbody[class^='lienors']").on('click','a.delete-line-lienor',function() {
        var xid  = $(this).data('id');
        $("tbody[class^='lienors'] tr[data-id='" + xid + "']").remove();
    });
    
     $( 'body' ).on('change','input, textarea, select',function() {
        triggerChange($(this));
        
     });
     
     
     
     
    $('a.add-line-surety').on('click',function() {
        var page_id = $(this).data('page-id');
          var cuenta = $('tbody.sureties' + page_id + ' > tr').length;
        var xindex = cuenta-1 ;
         if(isNaN(xindex)) { xindex=0; }
        var html = '<tr data-id="' + xindex + '">';
            html += '        <td>';
            html += '            <table style="width:100%"';
            html += '                <tr>';
            html += '                    <td><label>Name</label></td>';
            html += '                    <td><input type"text" name="sureties[' + xindex + '][name]" class="form-control"></td>';
            html += '                    <td style="padding:5px;"><a href="#" class="delete-line-surety" data-id="' + xindex + '"><span class="text-danger"><i class="fa fa-minus-circle"></i></span></a></td>';
            html += '                </tr>';
            html += '                <tr>';
            html += '                    <td><label>Address</label></td>';
            html += '                    <td >';
            html += '                        <textarea name="sureties[' + xindex + '][address]" class="form-control"></textarea>';
            html += '                    </td><td></td>';
            html += '                <tr>';
            html += '            </table>';
            html += '        </td>';
            html += '    </tr>';
            
        $('tbody.sureties' + page_id + ' > tr').eq(-1).after(html)
        
        
    }); 
    
      $("tbody[class^='sureties']").on('click','a.delete-line-surety',function() {
        var xid  = $(this).data('id');
        $("tbody[class^='sureties'] tr[data-id='" + xid + "']").remove();
    });

// NOC Surety
    $('a.add-line-noc_surety').on('click',function() {
        var page_id = $(this).data('page-id');
          var cuenta = $('tbody.noc_sureties' + page_id + ' > tr').length;
        var xindex = cuenta-1 ;
         if(isNaN(xindex)) { xindex=0; }
        var html = '<tr data-id="' + xindex + '">';
            html += '        <td>';
            html += '            <table style="width:100%"';
            html += '                <tr>';
            html += '                    <td><label>Name and Address</label></td>';
            html += '                    <td >';
            html += '                        <textarea name="noc_sureties[' + xindex + '][name_address]" class="form-control"></textarea>';
            html += '                    </td>';
            html += '                    <td style="padding:5px;"><a href="#" class="delete-line-noc_sureties" data-id="' + xindex + '"><span class="text-danger"><i class="fa fa-minus-circle"></i></span></a></td>';
            html += '                <tr>';
            html += '                <tr>';
            html += '                    <td><label>Phone Number</label></td>';
            html += '                    <td><input type"text" name="noc_sureties[' + xindex + '][phone]" class="form-control"></td>';
           
            html += '                </tr>';
            html += '                <tr>';
            html += '                    <td><label>Amount of Bond</label></td>';
            html += '                    <td><input type"text" name="noc_sureties[' + xindex + '][amount]" class="form-control"></td>';
            html += '                </tr>';
            html += '            </table>';
            html += '        </td>';
            html += '    </tr>';
            
        $('tbody.noc_sureties' + page_id + ' > tr').eq(-1).after(html)
        
        
    }); 
    
      $("tbody[class^='noc_sureties']").on('click','a.delete-line-noc_sureties',function() {
        var xid  = $(this).data('id');
        $("tbody[class^='noc_sureties'] tr[data-id='" + xid + "']").remove();
    });
// NOC Lender
    $('a.add-line-noc_lenders').on('click',function() {
        var page_id = $(this).data('page-id');
          var cuenta = $('tbody.noc_lenders' + page_id + ' > tr').length;
        var xindex = cuenta-1 ;
         if(isNaN(xindex)) { xindex=0; }
        var html = '<tr data-id="' + xindex + '">';
            html += '        <td>';
            html += '            <table style="width:100%"';
            html += '                <tr>';
            html += '                    <td><label>Name and Address</label></td>';
            html += '                    <td >';
            html += '                        <textarea name="noc_lenders[' + xindex + '][name_address]" class="form-control"></textarea>';
            html += '                    </td>';
            html += '                    <td style="padding:5px;"><a href="#" class="delete-line-noc_lenders" data-id="' + xindex + '"><span class="text-danger"><i class="fa fa-minus-circle"></i></span></a></td>';
            html += '                <tr>';
            html += '                <tr>';
            html += '                    <td><label>Phone Number</label></td>';
            html += '                    <td><input type"text" name="noc_lenders[' + xindex + '][phone]" class="form-control"></td>';
           
            html += '                </tr>';
            html += '            </table>';
            html += '        </td>';
            html += '    </tr>';
            
        $('tbody.noc_lenders' + page_id + ' > tr').eq(-1).after(html)
        
        
    }); 
    
      $("tbody[class^='noc_lenders']").on('click','a.delete-line-noc_lenders',function() {
        var xid  = $(this).data('id');
        $("tbody[class^='noc_lenders'] tr[data-id='" + xid + "']").remove();
    });

// NOC copiers_designated
    $('a.add-line-copiers_designated').on('click',function() {
        var page_id = $(this).data('page-id');
          var cuenta = $('tbody.copiers_designated' + page_id + ' > tr').length;
        var xindex = cuenta-1 ;
         if(isNaN(xindex)) { xindex=0; }
        var html = '<tr data-id="' + xindex + '">';
            html += '        <td>';
            html += '            <table style="width:100%"';
            html += '                <tr>';
            html += '                    <td><label>Name and Address</label></td>';
            html += '                    <td >';
            html += '                        <textarea name="copiers_designated[' + xindex + '][name_address]" class="form-control"></textarea>';
            html += '                    </td>';
            html += '                    <td style="padding:5px;"><a href="#" class="delete-line-copiers_designated" data-id="' + xindex + '"><span class="text-danger"><i class="fa fa-minus-circle"></i></span></a></td>';
            html += '                <tr>';
            html += '                <tr>';
            html += '                    <td><label>Phone Number</label></td>';
            html += '                    <td><input type"text" name="copiers_designated[' + xindex + '][phone]" class="form-control"></td>';
           
            html += '                </tr>';
            html += '            </table>';
            html += '        </td>';
            html += '    </tr>';
            
        $('tbody.copiers_designated' + page_id + ' > tr').eq(-1).after(html)
        
        
    }); 
    
      $("tbody[class^='copiers_designated']").on('click','a.delete-line-copiers_designated',function() {
        var xid  = $(this).data('id');
        $("tbody[class^='copiers_designated'] tr[data-id='" + xid + "']").remove();
    });      

// NOC othercopiers_designated
    $('a.add-line-othercopiers_designated').on('click',function() {
        var page_id = $(this).data('page-id');
          var cuenta = $('tbody.othercopiers_designated' + page_id + ' > tr').length;
        var xindex = cuenta-1 ;
         if(isNaN(xindex)) { xindex=0; }
        var html = '<tr data-id="' + xindex + '">';
            html += '        <td>';
            html += '            <table style="width:100%"';
            html += '                <tr>';
            html += '                    <td><label>Name and Address</label></td>';
            html += '                    <td >';
            html += '                        <textarea name="othercopiers_designated[' + xindex + '][name_address]" class="form-control"></textarea>';
            html += '                    </td>';
            html += '                    <td style="padding:5px;"><a href="#" class="delete-line-othercopiers_designated" data-id="' + xindex + '"><span class="text-danger"><i class="fa fa-minus-circle"></i></span></a></td>';
            html += '                <tr>';
            html += '                <tr>';
            html += '                    <td><label>Phone Number</label></td>';
            html += '                    <td><input type"text" name="othercopiers_designated[' + xindex + '][phone]" class="form-control"></td>';
           
            html += '                </tr>';
            html += '            </table>';
            html += '        </td>';
            html += '    </tr>';
            
        $('tbody.othercopiers_designated' + page_id + ' > tr').eq(-1).after(html)
        
        
    }); 
    
      $("tbody[class^='othercopiers_designated']").on('click','a.delete-line-othercopiers_designated',function() {
        var xid  = $(this).data('id');
        $("tbody[class^='othercopiers_designated'] tr[data-id='" + xid + "']").remove();
    });









    
    $('#generate').submit(function() {
        $('form.save-page').first().trigger('submit');
        return true; // return false to cancel form action
    });
    $( 'input, textarea, select' ).on('keydown',function() {
 
         $('.btn-success').prop('disabled', false);
         
     });

    
});


function triggerChange (xform) {
          console.log('hubo un cambio');
         var pid = xform.closest('form').data('id');
          console.log(pid);
         $('.submit' + pid ).prop('disabled', false);
         $('.button-generate').prop('disabled', true);
     }
</script>

@endsection