                     
@if(count($work_orders) > 0 )
<div class="col-xs-12" style="overflow-x: scroll;">
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Work Order Type</th>
            <th>WO Number</th>
            <th>Questions</th>
            <th>Answers</th>
        </tr>
    </thead>
    <tbody>
        @foreach($work_orders as $work)
        <?php
            $answers=\App\WorkOrderAnswers::where('work_order_id',$work->id)->get();
            foreach ($answers as $answer) {
                $question=\App\WorkOrderFields::where('id',$answer->work_order_field_id)->first();
                if ($question['field_label'] && $answer->answer){
                    $drop=null;    
                    if ($question['field_type']=='dropdown'){    

                        if ($question['dropdown_list']){    

                            $dropdown=json_decode($question['dropdown_list']);

                            if (isset($dropdown->{$answer->answer})){$drop=$dropdown->{$answer->answer};}else{$drop=" ";}

                        }  else {$drop=" ";}  

                    }

            if ($drop!=" "){             
        ?>
        <tr>
            <td>{{ $wo_types[$work->type] }}</td>
            <td>{{ $work->number}}</td>
            <td>{{ $question['field_label']}}</td>
            @if (!$drop) 
            <td> {{ $answer->answer }}</td>
            @else
            <td> {{ $drop }}</td>
            @endif

        </tr>
        <?php 
            }}}
        ?>

        @endforeach
    </tbody>
</table>
</div>

@else
<div class="col-xs-12">
    <h5>No Questions and Answers for Work Orders found</h5>
</div>
@endif
