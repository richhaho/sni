@extends('researcher.layouts.app')


@section('css')
<style>
    #page-wrapper {
        margin-left: 0px
    }
    #top-wrapper {
        margin-left: 0px
    }
.new-template {
  display:none;
}
.new-item-line {
    display:none;
}
.delete-line {

}
td.address  {
   line-height: 0.8!important;
}

td.address span{
    font-size: 0.8em;
}
</style>
@endsection
@section('content')
    
  
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">Mailing Hstory
                    <div class="pull-right">
                      
                            <a class="btn btn-danger " href="#" onclick="window.top.close();"><i class="fa fa-times-circle"></i> Close</a> &nbsp;&nbsp;

                    </div>
                </h1>       
            </div>
            </div>
        </div>
            <div id="page-wrapper">
            
            <div class="container-fluid">
                 <div class="col-xs-12" id="filters-form">
                    {!! Form::open(['route' => 'mailing.setfilter', 'class'=>'form-inline'])!!}
                        @if(count($clients) > 0)
                        <div class="form-group">
                            <label for="mailing_type_filter">Client: </label>
                            {!! Form::select('client_filter',$clients,session('mailing_filter.client'),['class'=>'form-control'])!!}
                        </div>
                        @endif
                        <div class="form-group">
                            <label for="mailing_type_filter"> Mailing Type: </label>
                            {!! Form::select('mailing_type',$mailing_types,session('mailing_filter.mailing_type'),['class'=>'form-control'])!!}
                        </div>

                    <button class="btn btn-success" type="submit" ><i class="fa fa-filter"></i> Filter</button>
                     <a href="{{ route('mailing.resetfilter') }}" class="btn btn-danger">Reset</a>
                    {!! Form::close() !!}

                </div>
                
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div>&nbsp;</div>
           {!! Form::open(['route' => ['mailing.store'],'autocomplete' => 'off','id' => 'new-mailing']) !!}     
                @if(count($available_documents) > 0 )
                        <div class="col-xs-12">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        <div class=" form-group">
                                    
                                    <div class="checkbox checkbox-slider--b-flat" data-toggle="tooltip" data-placement="top" title="Select All">
                                        <label>
                                        <input id="select_all" type="checkbox" ><span></span>
                                        </label>
                                    </div>
                                    </div> 
                                            </th>
                                    <th>To:</th>
                                    
                                    <th>Work Order ID</th>
                                    <th>Work Order Type</th>
                                    <th>Mailing Type</th>
                                    <th>Job Name</th>
                                    <th>Client Name</th>
                                    <th>File Name</th>
                                   
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($available_documents as $document)
                                {!! Form::hidden('work_order_recipient[' . $document->id . ']',$document->generated_id) !!}
                                @if($document->recipient) 
                                <tr>
                                    <td>
                                         <div class=" form-group">
                                    
                                    <div class="checkbox checkbox-slider--b-flat">
                                        <label>
                                        <input name="selected[{{$document->id}}]" type="checkbox" class="selection"><span></span>
                                        </label>
                                    </div>
                                    </div>        
                                </div>
                                    </td>
                                    <td class="address">
                                        {{$document->recipient->firm_name}}<br />
                                        <span >{!! nl2br($document->recipient->address) !!}</span>
                                    </td>
        
                                    <td> {{  $document->attachable->number  }}</td>
                                    <td> {{  $document->attachable->order_type->name  }}</td> 
                                    
                                    <td> @if($document->recipient){{ $mailing_types[$document->recipient->mailing_type] }}@endif</td>
                                     <td> {{  $document->attachable->job->name  }}</td> 
                                     <td> {{  $document->attachable->job->client->company_name  }}</td> 
                                    <td> {{  $document->original_name }} </td>
                                </tr>
                                @endif
                                @endforeach
                               
                            </tbody>
                        </table>
                        </div>
                        <div class="col-xs-12 text-center">
                            {{ $available_documents->links() }}
                        </div>
                        @else
                        <div class="col-xs-12">
                            <h5>No Available Work Orders to Mail</h5>
                        </div>
                        @endif
               {!! Form::close() !!}
            </div>
            <!-- /.container-fluid -->
            
        </div>
   
    
    
   
@endsection

@section('scripts')
<script>
$(function() {
    $('[data-toggle="tooltip"]').tooltip()
 

    $('#select_all').change(function() {
        var checkboxes = $('.selection');
        if($(this).is(':checked')) {
            checkboxes.prop('checked', true);
        } else {
            checkboxes.prop('checked', false);
        }
    });
});
</script>
    
@endsection