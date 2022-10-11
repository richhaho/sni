@extends('admin.pdf.forms.formbase')

@section('fields')

    <div class="row ">
        <div class="col-xs-4">
        <div class="form-group">
            <label>Client Name: </label>
            {!! Form::text('client_name',$client_name,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-4">
        <div class="form-group">
            <label>Client Company Name: </label>
            {!! Form::text('client_company_name',$client_company_name,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-4">
        <div class="form-group">
            <label>Client Phone: </label>
            {!! Form::text('client_phone',$client_phone,['class'=>'form-control'])!!}
        </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group">
            <label>Client Address: </label>
            {!! Form::textarea('client_address',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$client_address),['class'=>'form-control','rows'=>5])!!}
        </div>
    </div>
  

    <div class="row">
        <div class="col-xs-6">
        <div class="form-group">
            <label>Job Name: </label>
            {!! Form::text('job_name',$job_name,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-6">
        <div class="form-group">
            <label>Job County: </label>
            {!! Form::text('job_county',$job_county,['class'=>'form-control'])!!}
        </div>
        </div>
    </div>
      
        <div class="row">
            <div class="col-xs-6">
             <div class="form-group">
                <label>Job Address: </label>
                {!! Form::textarea('job_address',preg_replace("/\<br(\s*)?\/?\>/i", "\n",$job_address),['class'=>'form-control','rows'=>5])!!}
            </div>
            </div>
            <div class="col-xs-6">
             <div class="form-group">
                <label>Job Legal Description: </label>
                {!! Form::textarea('job_legal_description',preg_replace("/\<br(\s*)?\/?\>/i", "\n",$job_legal_description),['class'=>'form-control','rows'=>5])!!}
            </div>
            </div>
            <div class="col-xs-6">
                 <div class="form-group">
                    <label>Permit Number: </label>
                    {!! Form::text('permit',$permit,['class'=>'form-control'])!!}
                </div>
            </div>
            <div class="col-xs-6">
                 <div class="form-group">
                    <label>Folio Number: </label>
                    {!! Form::text('folio',$folio,['class'=>'form-control'])!!}
                </div>
            </div>
            <div class="col-xs-6">
                 <div class="form-group">
                    <label>Instrument/Book and Page: </label>
                    {!! Form::text('book_page',$book_page,['class'=>'form-control noucase'])!!}
                </div>
            </div>
            <div class="col-xs-6">
                <div class="form-group">
                    <label>NOC Recorded Date: </label>
                    {!! Form::text('noc_recorded',$noc_recorded,['class'=>'form-control date-picker', 'data-date-format' => 'mm/dd/yyyy'])!!}
                </div>
            </div>

            <div class="col-xs-6 hidden">
                 <div class="form-group">
                    <label>Client County: </label>
                    {!! Form::text('client_county',$client_county,['class'=>'form-control'])!!}
                </div>
            </div>
        </div>
   
    <div class="row">
        <div class="col-xs-12">
       
            <div class="form-group">
                <label>Improvements: </label>
                {!! Form::textarea('improvements',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$improvements),['class'=>'form-control','rows'=>5])!!}
            </div>
        </div>
    </div>
 
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="form-group">
                <label>Owner/Lessee name and address: </label>
                {!! Form::textarea('OwnerLessee_NameAddress',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$OwnerLessee_NameAddress),['class'=>'form-control','rows'=>3])!!}
            </div>
        </div>
        <div class="col-lg-12 col-md-12">
            <div class="form-group">
                <label>Interest In Property: </label>
                {!! Form::text('Interest_Property',$Interest_Property,['class'=>'form-control'])!!}
            </div>
        </div>
        <div class="col-lg-12 col-md-12">
            <div class="form-group">
                <label>Name and address of fee simple titleholder: </label>
                {!! Form::textarea('Simple_Titleholder',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$Simple_Titleholder),['class'=>'form-control','rows'=>3])!!}
            </div>
        </div>

    </div>


<!-- //////////////////////////////////////////////// -->
    <div class="hidden row">
    <div class="row ">
        <div class="col-xs-6">
        <div class="form-group">
            <label>Property Owner Firm Name: </label>
            {!! Form::text('land_owner_firm_name',$land_owner_firm_name,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-6">
        <div class="form-group">
            <label>Property Owner Name: </label>
            {!! Form::text('land_owner_name',$land_owner_name,['class'=>'form-control'])!!}
        </div>
        </div>
        
    </div>
    <div class="row">
        <div class="form-group">
            <label>Property Owner Address: </label>
            {!! Form::textarea('land_owner_address',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$land_owner_address),['class'=>'form-control','rows'=>5])!!}
        </div>
    </div> 
    </div>  
 <!-- //////////////////////////////////////////////// -->   
    <div class="row ">
        <div class="col-xs-4">
        <div class="form-group">
            <label>General Contractor Firm Name: </label>
            {!! Form::text('gc_firm_name',$gc_firm_name,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-4">
        <div class="form-group">
            <label>General Contractor Name: </label>
            {!! Form::text('gc_name',$gc_name,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-4">
        <div class="form-group">
            <label>General Contractor Phone: </label>
            {!! Form::text('gc_phone',$gc_phone,['class'=>'form-control'])!!}
        </div>
        </div>
        
    </div>
    <div class="row">
        <div class="form-group">
            <label>General Contractor Address: </label>
            {!! Form::textarea('gc_address',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$gc_address),['class'=>'form-control','rows'=>5])!!}
        </div>
    </div> 
    <div class="row ">
        <div class="col-lg-6">
        <div class="form-group">
            <label>Expiration Date: </label>
            {!! Form::text('expiration_date',$expiration_date,['class'=>'form-control date-picker', 'data-date-format' => 'mm/dd/yyyy'])!!}
        </div>
        </div> 
        <div class="col-lg-6">
        <div class="form-group">
            <label>Termination Date: </label>
            {!! Form::text('termination_date',$termination_date,['class'=>'form-control date-picker', 'data-date-format' => 'mm/dd/yyyy'])!!}
        </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group">
            <label>Exempt Real Property: </label>
            {!! Form::text('exempt_real_property',$exempt_real_property,['class'=>'form-control'])!!}
        </div>
    </div>

    <div class="col-xs-12">
        <table style='width:100%'>

            <tbody class="noc_sureties{{$page_id}}" id="noc_sureties{{$page_id}}">
                <tr>
                    <td colspan="2" style="text-align: right"><a href="#noc_sureties{{$page_id}}" class="add-line-noc_surety" data-page-id="{{$page_id}}"><i class="fa fa-plus-circle"></i> Add Surety</a></td>
                </tr>
                @foreach($noc_sureties as $key => $st)
                <tr data-id="{{$key}}" >
                    <td>
                        <table style="width:100%"> 
                            
                            <tr>
                                <td><label>Name and Address</label></td>
                                <td >
                                    <textarea name="noc_sureties[{{$key}}][name_address]" class="form-control">{{ preg_replace('/\<br(\s*)?\/?\>/i', "\n",$st["name_address"])}}</textarea>
                                </td>
                                <td style="padding:5px;"><a href="#" class="delete-line-noc_sureties" data-id="{{$key}}"><span class="text-danger"><i class="fa fa-minus-circle"></i></span></a></td>
                            <tr>
                            <tr>
                                <td><label>Phone Number</label></td>
                                <td>{!! Form::text("noc_sureties[" . $key ."][phone]",$st["phone"],['class'=>'form-control'])!!}</td>
                                
                            </tr>
                            <tr>
                                <td><label>Amount of Bond</label></td>
                                <td>{!! Form::text("noc_sureties[" . $key ."][amount]",$st["amount"],['class'=>'form-control'])!!}</td>
                                
                            </tr>
                        </table>
                    </td>
                </tr>
                @endforeach
                
            </tbody>
        </table>
    </div>
    <div class="col-xs-12"><p></p></div>


    <div class="col-xs-12">
        <table style='width:100%'>

            <tbody class="noc_lenders{{$page_id}}" id="noc_lenders{{$page_id}}">
                <tr>
                    <td colspan="2" style="text-align: right"><a href="#noc_lenders{{$page_id}}" class="add-line-noc_lenders" data-page-id="{{$page_id}}"><i class="fa fa-plus-circle"></i> Add Lender</a></td>
                </tr>
                @foreach($noc_lenders as $key => $st)
                <tr data-id="{{$key}}" >
                    <td>
                        <table style="width:100%"> 
                            
                            <tr>
                                <td><label>Name and Address</label></td>
                                <td >
                                    <textarea name="noc_lenders[{{$key}}][name_address]" class="form-control">{{ preg_replace('/\<br(\s*)?\/?\>/i', "\n",$st["name_address"])}}</textarea>
                                </td>
                                <td style="padding:5px;"><a href="#" class="delete-line-noc_lenders" data-id="{{$key}}"><span class="text-danger"><i class="fa fa-minus-circle"></i></span></a></td>
                            <tr>
                            <tr>
                                <td><label>Phone Number</label></td>
                                <td>{!! Form::text("noc_lenders[" . $key ."][phone]",$st["phone"],['class'=>'form-control'])!!}</td>
                                
                            </tr>
                        </table>
                    </td>
                </tr>
                @endforeach
                
            </tbody>
        </table>
    </div>
    <div class="col-xs-12"><p></p></div>


    <div class="col-xs-12">
        <table style='width:100%'>

            <tbody class="copiers_designated{{$page_id}}" id="copiers_designated{{$page_id}}">
                <tr>
                    <td colspan="2" style="text-align: right"><a href="#copiers_designated{{$page_id}}" class="add-line-copiers_designated" data-page-id="{{$page_id}}"><i class="fa fa-plus-circle"></i> Add Copy Recipients (Owner Designated) </a></td>
                </tr>
                @foreach($copiers_designated as $key => $st)
                <tr data-id="{{$key}}" >
                    <td>
                        <table style="width:100%"> 
                            
                            <tr>
                                <td><label>Name and Address</label></td>
                                <td >
                                    <textarea name="copiers_designated[{{$key}}][name_address]" class="form-control">{{ preg_replace('/\<br(\s*)?\/?\>/i', "\n",$st["name_address"])}}</textarea>
                                </td>
                                <td style="padding:5px;"><a href="#" class="delete-line-copiers_designated" data-id="{{$key}}"><span class="text-danger"><i class="fa fa-minus-circle"></i></span></a></td>
                            <tr>
                            <tr>
                                <td><label>Phone Number</label></td>
                                <td>{!! Form::text("copiers_designated[" . $key ."][phone]",$st["phone"],['class'=>'form-control'])!!}</td>
                                
                            </tr>
                        </table>
                    </td>
                </tr>
                @endforeach
                
            </tbody>
        </table>
    </div>
    <div class="col-xs-12"><p></p></div>

    <div class="col-xs-12">
        <table style='width:100%'>

            <tbody class="othercopiers_designated{{$page_id}}" id="othercopiers_designated{{$page_id}}">
                <tr>
                    <td colspan="2" style="text-align: right"><a href="#othercopiers_designated{{$page_id}}" class="add-line-othercopiers_designated" data-page-id="{{$page_id}}"><i class="fa fa-plus-circle"></i> Add Copy Recipients</a></td>
                </tr>
                @foreach($othercopiers_designated as $key => $st)
                <tr data-id="{{$key}}" >
                    <td>
                        <table style="width:100%"> 
                            
                            <tr>
                                <td><label>Name and Address</label></td>
                                <td >
                                    <textarea name="othercopiers_designated[{{$key}}][name_address]" class="form-control">{{ preg_replace('/\<br(\s*)?\/?\>/i', "\n",$st["name_address"])}}</textarea>
                                </td>
                                <td style="padding:5px;"><a href="#" class="delete-line-othercopiers_designated" data-id="{{$key}}"><span class="text-danger"><i class="fa fa-minus-circle"></i></span></a></td>
                            <tr>
                            <tr>
                                <td><label>Phone Number</label></td>
                                <td>{!! Form::text("othercopiers_designated[" . $key ."][phone]",$st["phone"],['class'=>'form-control'])!!}</td>
                                
                            </tr>
                        </table>
                    </td>
                </tr>
                @endforeach
                
            </tbody>
        </table>
    </div>
    <div class="col-xs-12"><p></p></div>


















    
    <div class="hidden row">
    <div class="row ">
        <div class="col-xs-4">
        <div class="form-group">
            <label>Copy Firm Name: </label>
            {!! Form::text('copy_firm_name',$copy_firm_name,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-4">
        <div class="form-group">
            <label>Copy Name: </label>
            {!! Form::text('copy_name',$copy_name,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-4">
        <div class="form-group">
            <label>Copy Phone: </label>
            {!! Form::text('copy_phone',$copy_phone,['class'=>'form-control'])!!}
        </div>
        </div>
        
    </div>
    <div class="row">
        <div class="form-group">
            <label>Copy Address: </label>
            {!! Form::textarea('copy_address',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$copy_address),['class'=>'form-control','rows'=>5])!!}
        </div>
    </div> 
    
   <div class="row">
        <div class="col-xs-6">
            <div class="form-group">
                <label>Copy Firm Name: </label>
                {!! Form::text('copyl_firm_name',$copyl_firm_name,['class'=>'form-control'])!!}
            </div>
            <div class="form-group">
            <label>Copy Name: </label>
            {!! Form::text('copyl_name',$copyl_name,['class'=>'form-control'])!!}
            </div>
            <div class="form-group">
            <label>Copy Phone: </label>
            {!! Form::text('copyl_phone',$copyl_phone,['class'=>'form-control'])!!}
            </div>
        </div>
        
  
       <div class="col-xs-6">
        <div class="form-group">
            <label>Copy Address: </label>
            {!! Form::textarea('copyl_address',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$copyl_address),['class'=>'form-control','rows'=>5])!!}
        </div>
       </div>
    </div>
</div>
        
         
    
@endsection