@component('mail::message')
# Hello!
Job name: {{$job->name}}
<br>
This is a reminder that if you are currently carrying an outstanding balance on this job. Based on the last day entered, you have {{90 - intval($over)}} days left to file your Claim of Lien and/or Notice of Nonpayment (if bonded). Statutory requirements of filing a timely notice to owner must have been met (if required).  If you have been paid in full click the button below.
<br>
@component('mail::button',['url'=>url('/client/jobs/'. $job->id . '/closelink')])
Click Here to close the job
@endcomponent
<br>
Thank you for using Sunshine Notices!<br>
@endcomponent