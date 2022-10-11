<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Report;
use DB;
use Storage;

class SendSubscribedReport extends Mailable
{
    use Queueable, SerializesModels;
    public $report;
    public $client_id;
    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($report,$client_id)
    {
        $this->report =$report;
        $this->client_id =$client_id;
        $this->subject('Sunshine Notices: '.$report->name);

        $from = \App\FromEmails::where('class', 'SendSubscribedReport')->first();
        if (isset($from->from_email)) {
            $this->from[] = [
                'address' => $from->from_email,
                'name' => $from->from_name
            ];
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $report = $this->report;
        $client_id = $this->client_id;
        
        $sql = str_replace("@client", "$client_id", $report->sql);
        $result = DB::select($sql);
        
        $columns = [];
        foreach ($result[0] as $key => $val) {
            $columns[] = $key;
        }
        $fpath = storage_path('app/reporting/report.csv');
        $file = fopen($fpath, 'w');
        fputcsv($file, $columns);

        foreach($result as $row) {
            fputcsv($file, (array) $row);
        }
        fclose($file);

        return $this->markdown('emails.report')->attach(storage_path('app/reporting/report.csv'), [
            'as' => 'report.csv',
            'mime' => 'text/csv',
        ]);;
    }

    
}
