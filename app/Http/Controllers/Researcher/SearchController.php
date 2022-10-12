<?php

namespace App\Http\Controllers\Researcher;

use App\Attachment;
use App\Client;
use App\ContactInfo;
use App\Entity;
use App\Http\Controllers\Controller;
use App\Job;
use App\JobParty;
use App\Note;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function loading()
    {
        return view('researcher.search.loading');
    }

    public function clients(Request $request)
    {
        $clients = Client::search($request->search)->paginate(10);
        $data = ['clients' => $clients];

        return view('researcher.search.results.clients', $data);
    }

    public function contacts(Request $request)
    {
        $search = $request->search;
        $contacts = Entity::where('firm_name', 'like', '%'.$request->search.'%')
                ->orWhere('latest_type', 'like', '%'.$request->search.'%')
                ->paginate(10);
        $data = ['contacts' => $contacts];

        return view('researcher.search.results.contacts', $data);
    }

    public function associates(Request $request)
    {
        $associates = ContactInfo::search($request->search)->paginate(10);
        $data = ['associates' => $associates];

        return view('researcher.search.results.associates', $data);
    }

    public function jobs(Request $request)
    {
        $clients = Client::search($request->search)->get()->pluck('id');
        //dd($clients);
        $client_jobs = Job::whereIn('client_id', $clients)->get();
        //dd($client_jobs);
        $jobs = Job::search($request->search)->get();
        $merge = $jobs->merge($client_jobs);

        $data = ['jobs' => $merge];

        return view('researcher.search.results.jobs', $data);
    }

    public function notes(Request $request)
    {
        $jobs = Job::search($request->search)->get()->pluck('id');
        //dd($jobs);
        $jobs_notes = Note::where('noteable_type', 'App\Job')->whereIn('noteable_id', $jobs)->get();
        //dd($jobs_notes);
        $notes = Note::search($request->search)->get();

        $merge = $notes->merge($jobs_notes);

        $data = ['notes' => $merge];

        return view('researcher.search.results.notes', $data);
    }

    public function attachments(Request $request)
    {
        $jobs = $jobs = Job::search($request->search)->get()->pluck('id');
        //dd($jobs);
        $jobs_attachments = Attachment::where('attachable_type', 'App\Job')->whereIn('attachable_id', $jobs)->get();
        //dd($jobs_notes);

        $attachments = Attachment::search($request->search)->get();

        $merge = $attachments->merge($jobs_attachments);

        $data = ['attachments' => $merge];

        return view('researcher.search.results.attachments', $data);
    }

    public function parties(Request $request)
    {
        $associates = ContactInfo::search($request->search)->get()->pluck('id');
        $parties = JobParty::whereIn('contact_id', $associates)->get();

        $data = ['parties' => $parties];

        return view('researcher.search.results.parties', $data);
    }
}
