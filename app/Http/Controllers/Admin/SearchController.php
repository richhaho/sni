<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Client;
use App\ContactInfo;
use App\Entity;
use App\Job;
use App\Note;
use App\Attachment;
use App\JobParty;
class SearchController extends Controller
{
    public function loading() {
        return view('admin.search.loading');
    }
    
    public function clients(Request $request) {
        $clients = Client::search($request->search)->paginate(10);
        $data = [ 'clients' => $clients ];
        return view('admin.search.results.clients', $data);
    }
    
     public function contacts(Request $request) {
        $search = $request->search;
        $contacts = Entity::where('firm_name', 'like', '%' . $request->search . '%')
                ->orWhere('latest_type', 'like', '%' . $request->search . '%')
                ->paginate(10);
        $data = [ 'contacts' =>$contacts ];
        return view('admin.search.results.contacts', $data);
    }
    
    public function associates(Request $request) {
        $associates = ContactInfo::search($request->search)->paginate(10);
        $data = [ 'associates' =>$associates ];
        return view('admin.search.results.associates', $data);
    }
    
    public function jobs(Request $request) {
        $clients = Client::search($request->search)->get()->pluck('id');
        //dd($clients);
        $client_jobs = Job::whereIn('client_id',$clients)->get();
        //dd($client_jobs);
        $jobs = Job::search($request->search)->get();
        $merge = $jobs->merge($client_jobs);
        
        $data = [ 'jobs' =>$merge ];
        return view('admin.search.results.jobs', $data);
    }
    
    
    public function notes(Request $request) {
        $jobs =   Job::search($request->search)->get()->pluck('id');
        //dd($jobs);
        $jobs_notes = Note::where('noteable_type','App\Job')->whereIn('noteable_id',$jobs)->get();
        //dd($jobs_notes);
        $notes = Note::search($request->search)->get();

        $merge = $notes->merge($jobs_notes);
       
        $data = [ 'notes' =>$merge ];
        return view('admin.search.results.notes', $data);
    }
    
    public function attachments(Request $request) {
        $jobs =  $jobs = Job::search($request->search)->get()->pluck('id');
        //dd($jobs);
        $jobs_attachments = Attachment::where('attachable_type','App\Job')->whereIn('attachable_id',$jobs)->get();
        //dd($jobs_notes);

        $attachments = Attachment::search($request->search)->get();

        $merge = $attachments->merge($jobs_attachments);
       
        $data = [ 'attachments' =>$merge ];
        return view('admin.search.results.attachments', $data);
    }
    
    
     public function parties(Request $request) {
        $associates = ContactInfo::search($request->search)->get()->pluck('id');
        $parties = JobParty::whereIn('contact_id',$associates)->get();
                
        $data = [ 'parties' =>$parties ];
        return view('admin.search.results.parties', $data);
        
     }
    
}
