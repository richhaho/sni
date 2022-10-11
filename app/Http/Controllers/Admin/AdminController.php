<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Client;
use App\WorkOrder;
use App\Invoice;
use App\Job;
use App\Attachment;
use App\SubscriptionRate;
use Storage;
use Response;
use Session;

class AdminController extends Controller
{
    public function index() {
        $clients_count = Client::count();
        // $work_order_count = WorkOrder::whereNotIn('status',['temporary', 'completed','cancelled','cancelled charge','cancelled no charge','closed','cancelled duplicate','cancelled duplicate needs credit'])->count();
        // $work_order_rush_count = WorkOrder::where('is_rush',1)->whereNotIn('status',['temporary', 'completed','cancelled','cancelled charge','cancelled no charge','closed','cancelled duplicate','cancelled duplicate needs credit'])->count();
        $invoices_count = Invoice::where('payed_at',NULL)->count();
        $open_jobs_count = Job::open()->count();

        $work_order_count_full = WorkOrder::where(function($q) {
                $q->where('service', null)->orwhere('service', 'full');
            })->whereNotIn('status',['temporary', 'completed','cancelled','cancelled charge','cancelled no charge','closed','cancelled duplicate','cancelled duplicate needs credit'])->count();
        $work_order_rush_count_full = WorkOrder::where(function($q) {
                $q->where('service', null)->orwhere('service', 'full');
            })->where('is_rush',1)->whereNotIn('status',['temporary', 'completed','cancelled','cancelled charge','cancelled no charge','closed','cancelled duplicate','cancelled duplicate needs credit'])->count();
        $work_order_count_self = WorkOrder::where('service', 'self')->whereNotIn('status',['temporary', 'completed','cancelled','cancelled charge','cancelled no charge','closed','cancelled duplicate','cancelled duplicate needs credit'])->count();
        $work_order_rush_count_self = WorkOrder::where('service', 'self')->where('is_rush',1)->whereNotIn('status',['temporary', 'completed','cancelled','cancelled charge','cancelled no charge','closed','cancelled duplicate','cancelled duplicate needs credit'])->count();
        $work_order_count = "$work_order_count_full/$work_order_count_self";
        $work_order_rush_count = "$work_order_rush_count_full/$work_order_rush_count_self";
        $data = [
            'clients_count' => $clients_count,
            'work_order_count' => $work_order_count,
            'invoices_count' => $invoices_count,
            'work_order_rush_count' => $work_order_rush_count,
            'open_jobs_count' => $open_jobs_count
        ];
        return view('admin.index',$data);
    }
    
   public function printAttachment($id) {
        
        $url = route('attachment.view',$id);
        $data =[
            'url' => $url
        ];
        return view ('admin.mailing.exeprint',$data);
   }
   
   
   public function viewAttachment($id)
   {
        $attachment = Attachment::findOrFail($id);
        $content = Storage::get($attachment->file_path);
           
           return Response::make($content, 200, [
               'Content-Type' => 'application/pdf',
               'Content-Disposition' => 'inline; filename="mailing-final-'.$id.'.pdf"'
           ]);
    }


    public function editSubscriptionRate()
    {
        $rate = SubscriptionRate::first();
        $data =[
            'self_30day_rate' => $rate->self_30day_rate,
            'self_365day_rate' => $rate->self_365day_rate,
            'full_30day_rate' => $rate->full_30day_rate,
            'full_365day_rate' => $rate->full_365day_rate
        ];
        return view ('admin.subscriptionrate.index',$data);
    }

    public function updateSubscriptionRate(Request $request)
    {
        $data = $request->all();
        $rate = SubscriptionRate::first();
        $rate->update($data);
        Session::flash('message', 'Subscription rate updated.');
        return redirect()->back();
    }


    /**
     * Download CSV
     *
     * @return Response
     */
    public function download(Request $request)
    {
        $result = SubscriptionRate::all();
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=subscriptionrate.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        $columns = ['self_30day_rate', 'self_365day_rate', 'full_30day_rate', 'full_365day_rate'];
        $callback = function() use ($result, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($result as $row) {
                fputcsv($file, [$row->self_30day_rate, $row->self_365day_rate, $row->full_30day_rate, $row->full_365day_rate]);
            }
            fclose($file);
        };
        return Response::stream($callback, 200, $headers);
    }

    /**
     * Upload CSV
     *
     * @return Response
     */
    public function upload(Request $request)
    {
        $delimiter = ',';
        $f= $request->file('csv');
        if (!$f || $f=='') {
            Session::flash('message','Please input csv file.');
            return redirect()->route('subscriptionrate.edit');
        }
        $header = null;
        $data = array();
        if (($handle = fopen($f, 'r')) !== false) {
            try {
                while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
                {
                    if (!$header)
                        $header = $row;
                    else
                        $data[] = array_combine($header, $row);
                }
                fclose($handle);
            } catch (\Exception $e) {
                Session::flash('message','You uploaded invalid CSV file. Please upload valid one.');
                return redirect()->route('subscriptionrate.edit'); 
            }
        }
        $invalidError = $this->checkIfValidCSV($header, $data);
        if ($invalidError) {
            Session::flash('message', $invalidError);
            return redirect()->route('subscriptionrate.edit');    
        }
        foreach($data as $row) {
            $mt = SubscriptionRate::first();
            if (empty($mt)) {
                $mt = SubscriptionRate::create($row);
            } else {
                $mt->update($row);
            }
        }

        Session::flash('message','Price list Updated from uploaded csv file.');
        return redirect()->route('subscriptionrate.edit');
    }

    public function checkIfValidCSV($header, $data) {
        $columns = ['self_30day_rate', 'self_365day_rate', 'full_30day_rate', 'full_365day_rate'];
        if ($columns != $header) return 'Error: CSV header does not match. Header should be self_30day_rate, self_365day_rate, full_30day_rate and full_365day_rate.';
        foreach($data as $row) {
            if (!is_numeric($row['self_30day_rate']) || !is_numeric($row['self_365day_rate']) || !is_numeric($row['full_30day_rate']) || !is_numeric($row['full_365day_rate'])) {
                return 'Error: self_30day_rate, self_365day_rate, full_30day_rate, full_365day_rate must be numeric value on your csv file.';
            }
        }
        return null;
    }
}
