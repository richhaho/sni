<a href="#" data-toggle="modal" data-target="#modal-report-subscribe-{{$id}}" class="btn btn-warning pull-right btn-xs" style="margin-right:5px"> Subscribe</a>
<div class="modal fade" id="modal-report-subscribe-{{$id}}" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      {!! Form::open(['route' => ['client.reports.subscribe', $id]]) !!}
      {!! Form::hidden('_method', 'POST') !!}
      <?php
        $subscribe = \App\ReportSubscribed::where('client_id', Auth::user()->client_id)->where('report_id', $id)->first();
        $users = Auth::user()->client->users->pluck('email', 'email')->toArray();
        $weekdays = [
          'Monday' =>'Monday',
          'Tuesday' =>'Tuesday',
          'Wednesday' =>'Wednesday',
          'Thursday' =>'Thursday',
          'Friday' =>'Friday',
          'Saturday' =>'Saturday',
          'Sunday' =>'Sunday'
        ];
      ?>
      {!! Form::hidden('subscribed_id', $subscribe ? $subscribe->id : '') !!}
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        @if (empty($subscribe))
        <h4 class="modal-title">Subscribe report.</h4>
        @else
        <h4 class="modal-title">This report has already been subscribed. Do you want to update?</h4>
        @endif
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-12 form-group">
            <label><h5>Report Name: </h5> {{$name}}</label>
          </div>
          <div class="col-xs-12 form-group">
            <label><h5>To Users: </h5></label>
            @if(empty($subscribe))
            {!! Form::select('email_select',$users, [] ,['class' => 'multi-select-email form-control', 'multiple'=>'multiple', 'required'=>'required'])!!}
            <input type="hidden" name="users" class="email-content hidden">
            <p class="emails-label"></p>
            <p class="text-danger users-email-required">* Users' email required. Please select one or more emails.</p>
            @else
            {!! Form::select('email_select',$users, explode(',',$subscribe->users) ,['class' => 'multi-select-email form-control', 'multiple'=>'multiple', 'required'=>'required'])!!}
            <input type="hidden" name="emails" class="email-content hidden" value="{{$subscribe->users}}">
            <p class="emails-label">{{$subscribe->users}}</p>
            <p class="text-danger users-email-required hidden">* Users' email required. Please select one or more emails.</p>
            @endif
          </div>
          <div class="col-xs-12 form-group">
            <label><h5>Weekdays: </h5></label>
            @if(empty($subscribe))
            {!! Form::select('weekdays_select',$weekdays, [] ,['class' => 'multi-select-weekdays form-control', 'multiple'=>'multiple', 'required'=>'required'])!!}
            <input type="hidden" name="weekdays" class="weekdays-content hidden">
            <p class="weekdays-label"></p>
            <p class="text-danger weekdays-required">* Weekdays required. Please select one or more emails.</p>
            @else
            {!! Form::select('weekdays_select',$weekdays, explode(',',$subscribe->weekdays) ,['class' => 'multi-select-weekdays form-control', 'multiple'=>'multiple', 'required'=>'required'])!!}
            <input type="hidden" name="weekdays" class="weekdays-content hidden" value="{{$subscribe->weekdays}}">
            <p class="weekdays-label">{{$subscribe->weekdays}}</p>
            <p class="text-danger users-weekdays-required hidden">* Weekdays required. Please select one or more emails.</p>
            @endif
          </div>
          <div class="col-xs-12 form-group">
            <label><h5>Time: </h5></label>
            <?php
              $hourList = ['12'=>'12', '01'=>'01', '02'=>'02', '03'=>'03', '04'=>'04', '05'=>'05', '06'=>'06', '07'=>'07', '08'=>'08', '09'=>'09', '10'=>'10', '11'=>'11'];
              $minList = ['00'=>'00', '05'=>'05', '10'=>'10', '15'=>'15', '20'=>'20', '25'=>'25', '30'=>'30', '35'=>'35', '40'=>'40', '45'=>'45', '50'=>'50', '55'=>'55'];
            ?>
            {!! Form::select('hour',$hourList, $subscribe ? substr($subscribe->time,0,2) : date('h') ,['class' => 'subscribe-time'])!!}
            {!! Form::select('min',$minList, $subscribe ? substr($subscribe->time,3,2) : '00' ,['class' => 'subscribe-time'])!!}
            {!! Form::select('am_pm',['AM'=>'AM', 'PM'=>'PM'], $subscribe ? substr($subscribe->time,6,2) : date('A') ,['class' => ''])!!}
          </div>
        </div>
      </div>
      <div class="modal-footer">
          <button type="submit" class="btn btn-success">Subscribe</button>&nbsp;&nbsp;
          @if($subscribe)
          <a href="{{route('client.reports.unsubscribe', $id)}}" class="btn btn-warning">Unsubscribe</a>&nbsp;&nbsp;
          @endif
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i calss="fa fa-times"></i> Cancel</button>&nbsp;&nbsp;
      </div>
      {!! Form::close() !!}
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>