<style type="text/css">
	.signature-panel canvas{
		width: 100% !important;
	}
	#currentsignature{
		width: 100% !important;
	}
</style>

<div class=''>

   
    {!! Form::hidden('type',$type) !!}
    
     <div class="row ">
        
        <div class="col-xs-2 pull-right">
            <button type="button" class="btn btn-block btn-success update" name="submit" value="save" disabled="true"><i class="fa fa-floppy-o"></i> Save </button>
     
        </div>
    </div>
    
    @yield('fields')
    

</div>
