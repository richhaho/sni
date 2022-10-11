<div  class="col-xs-12">
    <h1 class="page-header">{{$job->name}}
        <div class="pull-right">
            <a class="btn btn-success " href="{{ route('parties.index',$job->id) .'?workorder=' .$work->id }}"> <i class="fa fa-pencil"></i> Edit Parties</a>
            
        </div>
    </h1>       
</div>
<div >&nbsp</div>
<div class="row">
    <div class="col-xs-12">

    <div class="col-xs-12">
    <div class="panel panel-default">
    <div class="panel-heading">
        Job Parties
    </div>
    <div class="panel-body">
    @foreach ($parties_type as $type_key => $type_name)
        @if($loop->first )
            <div class="row">
        @endif
        @if($job->parties()->ofType($type_key)->count() > 0)
           
                <div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">{{ $type_name }}</h4>
                        </div>
                        <div class="panel-body">
                           @foreach($job->parties()->ofType($type_key)->get() as $jobparty) 

                                @include('admin.jobs.components.contacticon')

                            @endforeach
                        </div>
                    </div>
                </div>
           @endif
            @if($loop->last)
               </div><!-- div last-->
            @else
               @if($loop->iteration % 2 == 0)
                   </div><!-- div cambio fila-->
                   <div class="row">
               @else
                
               @endif
           @endif
        @endforeach
    </div> <!-- finish panel body -->
        
</div><!-- finish panel  -->
</div><!-- col-xs  -->
</div><!-- col-xs  -->
</div><!-- col-row  -->