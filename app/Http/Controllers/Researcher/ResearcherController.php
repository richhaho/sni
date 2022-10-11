<?php

namespace App\Http\Controllers\Researcher;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Client;
use App\WorkOrder;
use App\Invoice;
use App\Job;
use App\Attachment;
use Storage;
use Response;

class ResearcherController extends Controller
{
    public function index() {
        $clients_count = Client::count();
        $work_order_count = WorkOrder::whereNotIn('status',['completed','cancelled','cancelled charge','cancelled no charge', 'closed', 'cancelled duplicate','cancelled duplicate needs credit'])->count();
        $invoices_count = Invoice::where('payed_at',NULL)->count();
        $work_order_rush_count = WorkOrder::where('is_rush',1)->count();
        $open_jobs_count = Job::open()->count();
        $data = [
            'clients_count' => $clients_count,
            'work_order_count' => $work_order_count,
            'invoices_count' => $invoices_count,
            'work_order_rush_count' => $work_order_rush_count,
            'open_jobs_count' => $open_jobs_count
        ];
        return view('Researcher.index',$data);
    }
    
   public function printAttachment($id) {
        
        $url = route('attachment.view',$id);
        $data =[
            'url' => $url
        ];
        return view ('Researcher.mailing.exeprint',$data);
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
    
}
