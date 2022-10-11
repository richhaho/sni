<div  class="col-xs-12">
    <h1 class="page-header">ToDos List
        <div class="pull-right">
        </div>
    </h1>       
</div>
<div ><br></div>
<div class="row">
<div class="col-xs-12">
    <div class="col-xs-12">
        @if(count($todos) >0 )
        <table class="table" >
            <thead>
                <tr>
                    <th> Name</th>
                    <th> Description</th>
                    <th> Summary</th>
                    <th> Status</th>
                    <th> Completed Time</th>
                    <th> To Do Uploads</th>
                    <th> To Do Instructions</th>
                    <th> Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($todos as $todo)
                <tr>
                    <td>{{$todo->name}}</td>
                    <td>{{$todo->description}}</td>
                    <td>{{$todo->summary}}</td>
                    <td>{{$todo->status}}</td>
                    <td>{{$todo->completed_at}}</td>
                    <td>{{$todo->todo_uploads ? 'Yes':'No'}}</td>
                    <td>{{$todo->todo_instructions ? 'Yes':'No'}}</td>
                    <td>
                        <a href="{{route('client.notices.todo.edit', ['work_id' => $work->id, 'id' => $todo->id])}}" class="btn btn-success btn-xs"> View Detail</a>
                    </td>
                </tr>
               @endforeach
            </tbody>
        </table>
        @else
        No ToDos Yet
        @endif
    </div> <!-- finish panel body -->
</div>
</div><!-- col-xs  -->
