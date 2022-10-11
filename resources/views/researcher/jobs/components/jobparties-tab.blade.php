
<div >&nbsp</div>
<div class="row">
    <div class="col-xs-12">

    
    <div class="panel panel-default">
    <div class="panel-heading">
        Job Parties
    </div>
    <div class="panel-body">
    @foreach ($parties_type as $type_key => $type_name)

        @if($job->parties()->ofType($type_key)->count() > 0)
           
                <div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">{{ $type_name }}</h4>
                        </div>
                        <div class="panel-body">
                           @foreach($job->parties()->ofType($type_key)->get() as $jobparty) 

                                @include('researcher.jobs.components.contacticon')

                            @endforeach
                        </div>
                    </div>
                </div>
           @endif
           
        @endforeach
    </div> <!-- finish panel body -->
        
</div><!-- finish panel  -->

</div><!-- col-xs  -->
</div><!-- col-row  -->