@foreach ($parties_type as $type_key => $type_name)
@if($loop->first )
    <div class="row">
@endif
@if($xjob->parties()->ofType($type_key)->count() > 0)

        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">{{ $type_name }}</h4>
                </div>
                <div class="panel-body">
                   @foreach($xjob->parties()->ofType($type_key)->get() as $jobparty) 

                        @include('admin.jobs.components.copycontacticon')

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