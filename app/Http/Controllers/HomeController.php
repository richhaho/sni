<?php

namespace App\Http\Controllers;

use App\Country;
use App\Job;
use App\Notifications\ShareJobRequestFromQrScan;
use App\User;
use Auth;
use Illuminate\Support\Facades\Notification;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->hasRole(['admin'])) {
            $destination = 'admin';
        } else {
            if (Auth::user()->hasRole(['researcher'])) {
                //$destination="researcher";
                $destination = 'admin';
                if (Auth::user()->restricted) {
                    $destination = 'research.index';
                }
            } else {
                $destination = 'client.index';
            }
        }

        return redirect()->route($destination);
    }

    public function listCountries()
    {
        return array_map('strtoupper', Country::get()->pluck('name')->ToArray());
    }

    public function listStates($country_name = null)
    {
        $country = Country::where('name', $country_name)->first();
        if ($country) {
            return array_map('strtoupper', $country->states->pluck('name')->ToArray());
        } else {
            return [];
        }
    }

    public function listCounties()
    {
        $counties = ['Miami-Dade', 'Broward', 'Palm Beach', 'Hillsborough', 'Orange', 'Pinellas', 'Duval', 'Lee', 'Polk', 'Brevard', 'Volusia', 'Pasco', 'Seminole', 'Sarasota', 'Collier', 'Marion', 'Manatee', 'Escambia', 'Lake', 'Saint Lucie', 'Osceola', 'Leon', 'Alachua', 'Okaloosa', 'Clay', 'Saint Johns', 'Bay', 'Charlotte', 'Hernando', 'Santa Rosa', 'Martin', 'Citrus', 'Indian River', 'Highlands', 'Flagler', 'Sumter', 'Monroe', 'Putnam', 'Nassau', 'Columbia', 'Walton', 'Jackson', 'Gadsden', 'Levy', 'Suwannee', 'Hendry', 'Okeechobee', 'De Soto', 'Wakulla', 'Bradford', 'Hardee', 'Baker', 'Washington', 'Taylor', 'Madison', 'Holmes', 'Gilchrist', 'Dixie', 'Gulf', 'Union', 'Jefferson', 'Hamilton', 'Calhoun', 'Franklin', 'Glades', 'Liberty', 'Lafayette'];

        return array_map('strtoupper', $counties);
    }

    public function listAddressSources()
    {
        $address_sources = ['TR', 'NOC', 'ATIDS', 'SubBiz', 'Other'];

        return array_map('strtoupper', $address_sources);
    }

    public function shareRequestFromMonitoringUser($job_id, $user_id)
    {
        $job = Job::where('id', $job_id)->first();
        if (empty($job)) {
            return;
        }
        $user = User::where('id', $user_id)->first();
        if (empty($user)) {
            return;
        }
        $client = $job->client;
        foreach ($client->activeusers as $toUser) {
            Notification::send($toUser, new ShareJobRequestFromQrScan($job, $user));
        }
    }
}
