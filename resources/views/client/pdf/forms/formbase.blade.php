

<div class=''>

    {!! Form::open(['route'=>['client.pdfpage.update',$page_id],'data-type'=>$type,'data-id'=> $page_id, 'id' => 'form'.$page_id,'class'=>'save-page','autocomplete' => 'off'])!!}
    {!! Form::hidden('type',$type) !!}
    {!! Form::hidden('page_id',$page_id) !!}
    {!! Form::hidden('signature',$signature) !!}
    <div class="row ">
        
        <div class="col-xs-2 pull-right">
            <button type="submit" class="btn btn-block btn-success submit{{$page_id}}" name="submit" value="save" disabled="true"><i class="fa fa-floppy-o"></i> Save</button>
     
        </div>
    </div>
    @yield('fields')
    {!! Form::close() !!}

</div>
