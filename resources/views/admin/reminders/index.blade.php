@extends('admin.layouts.app')

@section('navigation')
    @include('admin.navigation')
@endsection

@section('css')
<link href="{{asset('vendor/bootstrap-datarange/css/daterangepicker.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<style>
    #filters-form {
        margin-bottom: 15px;
        *margin-top: 15px;
    }
     #filters-form div.row {
         margin-top: 15px;
     }
         
    input[name="daterange"] {
            min-width: 180px;
    }
        td.job_name  {
       line-height: 0.8!important;
    }

    td.job_name span{
        font-size: 0.8em;
    }
    .job_name_align{
        word-break: break-all;
    }
    .phonecall{
         word-break: break-all;
    }
</style>
@endsection


@section('content')
            <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12">
                        <h1 class="page-header">Reminders
                            <a class="btn btn-success pull-right" href="{{ route('reminders.create')}}"><i class="fa fa-plus"></i> New Reminder</a>
                        </h1>
                       
                    </div>
                        
                    
                        @if (Session::has('message'))
                            <div class="col-xs-12 message-box">
                            <div class="alert {{ Session::get('message-class','alert-info') }}">{{ Session::get('message') }}</div>
                            </div>
                        @endif
                    
                        
                        @if(count($reminder) > 0 )
                        <div class="col-xs-12">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Reminder Name</th>
                                    <th>Email Subject</th>
                                    <th>First Send Date/Time</th>
                                    <th>Last Send Date/Time</th>
                                    <th>Next Send Date/Time</th>
                                    <th>Frequency</th>
                                    <th>Disable/Enable</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reminder as $remind)
                                <tr>
                                    <td> {{$remind->reminder_name}}</td>
                                    <td> {{$remind->email_subject}}</td>
                                    <td> {{$remind->first_send_date}}</td>
                                    <td> {{$remind->end_send_date}}</td>
                                    <td> {{$remind->next_send_date}}</td>
                                    <td> {{$remind->send_frequency}} {{$period[$remind->period]}}</td>
                                    <td> <div class="checkbox checkbox-slider--b-flat">
                                            <label>
                                                <input name="allow_emailReminder"  type="checkbox" class="allow_emailReminder selection" {{ ($remind->status==1) ? 'checked' :''}} disabled><span></span>
                                            </label>
                                        </div></td>
                                    
                                    <td>
                                        <div class="btn-group pull-right dropup">
                                            <button type="button" class="btn btn-default dropdown-toggle " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-cogs"></i> Actions <span class="caret"></span>
                                            </button>
                                    @component('admin.reminders.components.deletemodal')
                                        @slot('id') 
                                            {{ $remind->id }}
                                        @endslot
                                    @endcomponent
                                     
                                        <li>
                                            <a class=" " href="{{ route('reminders.edit',$remind->id)}}"><i class="fa fa-pencil"></i> Edit</a>
                                        </li>
                                        
                                    </ul>
                                      </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        <div class="col-xs-12 text-center">
                            {{ $reminder->links() }}
                        </div>
                        @else
                        <div class="col-xs-12">
                            <h5>No Reminders found</h5>
                        </div>
                        @endif
                    
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
@endsection

@section('scripts')
 
 
<script>
    
    
$(function () {
    $(".message-box").fadeTo(3000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
});
</script>
@endsection