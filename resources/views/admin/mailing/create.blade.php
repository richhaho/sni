@extends('admin.layouts.app')


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
                <h1 class="page-header">Create New Mailing Batch
                    <div class="pull-right">
                        <button class="btn btn-success btn-save" type="submit" form="new-mailing"> <i class="fa fa-floppy-o"></i> Save</button>
                 
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
                        <div class="form-group">
                            <label for="job_name_filter"> Job Name: </label>
                            {!! Form::text('job_name',session('mailing_filter.job_name'),['class'=>'form-control'])!!}
                        </div>
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
                        <div class="form-group">
                            <label for="notice_type_filter"> Work Order Type: </label>
                            {!! Form::select('notice_type',$wo_types,session('mailing_filter.notice_type'),['class'=>'form-control'])!!}
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
                                    <th>Tracking Number</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($available_documents as $document)
                                {!! Form::hidden('work_order_recipient[' . $document->id . ']',$document->generated_id) !!}
                                @if($document->recipient) 
                                @if($document->attachable) 
                                <tr>
                                    <td>
                                         <div class=" form-group">
                                    
                                    <div class="checkbox checkbox-slider--b-flat">
                                        <label>
                                        <input name="selected[{{$document->id}}]" type="checkbox" class="selection"><span></span>
                                        </label>
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
                                    <td>
                                    @if($document->recipient)
                                    @if($document->recipient->mailing_type == 'registered-mail' || $document->recipient->mailing_type == 'express-mail')
                                        @component('admin.mailing.components.trackingnumbermodal')
                                        @slot('id') 
                                            {{ $document->id }}
                                        @endslot
                                        @slot('tracking_number') 
                                            {{ $document->recipient->mailing_number }}
                                        @endslot
                                        @endcomponent
                                    @endif
                                    @endif
                                    </td>
                                </tr>
                                @endif
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
$('.btn-save').click(function(){
    $('.btn-save').addClass("disabled");
    $('.btn-save').css('pointer-events','none');
});
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

    $('.btn-add-tracking-number').click(function() {
        const document_id = $(this).parent().parent().find('.tracking_number_id').val();
        const tracking_number = $(this).parent().parent().find('.tracking_number').val();
        $.ajax({
          url: "{{ url('/admin/mailing/add-tracking-number') }}",
          type: "post",
          data: {
            document_id: document_id,
            tracking_number: tracking_number
          },
          success: function(data) {
              location.reload();
          }
        });
    });
});
</script>
    
@endsection