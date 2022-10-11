@extends('admin.mailing.pdf')
@section('content')
<div id="page">
    <div class="content">
        <h1 class="text-center">BATCH DETAIL REPORT</h1>
        <div>&nbsp;</div> 
        <h3 class="">BATCH ID: {{$batch_id}}</h3>
        <div>&nbsp;</div> 
    
    <!-- Start Loop -->
        @foreach($summary_details as $mailing_type => $rows)
        <div class="row square-box">
             <div class=" col-12">Destination:</div>
        </div>
   
            <table class="col-12">
                <thead style="border-bottom: 1px solid black;">
                    <tr>
                        <th>Zip</th>
                        <th>Order #</th>
                        <th>Id#</th>
                        <th>Copy Type</th>
                        <th>Cert #</th>
                        <th>Sent To</th>
                        <th>Mail Type</th>
                    </tr>
                </thead>
                <tbody>
                    
                    @foreach($rows as $r)
                    <tr>
                        <td>{{ $r['zip'] }}</td>
                        <td>{{ $r['order_id'] }}</td>
                        <td>{{ $r['notice_id'] }}</td>
                        <td>{{ $parties_type[$r['copy_type']] }}</td>
                        <td>
                            @if (strlen($r['cert_num'])>7)
                            {{ substr($r['cert_num'], -7) }}
                            @else
                            {{$r['mailing_number'] ? $r['mailing_number'] : 'N/A'}}
                            @endif
                        </td>
                        <td>{{ $r['sent_to'] }}</td>
                        <td>{{ $mailing_types[$r['mail_type']] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
         <p><h3><span class="warning">Destination Count: </span> {{ $summary_totals[$mailing_type] }}</h3></p>
    
    
        <div>&nbsp;</div> 
        @endforeach
    <div>&nbsp;</div> 
    
    <div class="col-12 text-center"><h3><span class="warning">Total Notices:</span> {{ $total_notices }}</h3></div>
    <div>&nbsp;</div> 
    <div class="col-12 text-center"><h3><span class="warning">Total Batch:</span> {{ $total_batch }}</h3></div>
  
   
    <div class="last-l1">
    <div class="row" style="clear: both;">
        <div class="col-12">On: {{ Carbon\Carbon::now()->format('m-d-Y H:i:s a') }}</div>
    </div>
        </div>
         <div class="last-l2">
    <div class="row" style="clear: both;">
        <div class="col-6">Printed by: {{ Auth::user()->full_name}}</div>
        <div class="col-6 text-right">Page 1 of 1</div>
    </div>
    </div>
</div>
</div>
@endsection