<?php



namespace App\Jobs;



use Illuminate\Bus\Queueable;

use Illuminate\Queue\SerializesModels;

use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Foundation\Bus\Dispatchable;

use App\Client;

use App\ContactInfo;

use App\Entity;

use App\Job;

use App\JobParty;

use App\WorkOrder;
use App\JobAddressSearchAPI;
use App\Note;
use Carbon\Carbon;

use Illuminate\Support\Facades\Mail;

//composer require guzzlehttp/guzzle:~6.0
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client as Http_Client; 



class JobinfoEstated implements ShouldQueue

{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;



    /**

     * Create a new job instance.

     *

     * @return void

     */

    public function __construct()

    {

        //

    }



    /**

     * Execute the job.

     *

     * @return void

     */

    

    public function handle()

    {
        
        $jobs=Job::where('search_status','ready')->get();

        $token=env('ESTATED_API_TOKEN');
        // $token='fa1zRLG4WkyZMy05vyxal2Pz0DCwFD';
        // $token='YjgnGy2ZVTtzwR4Sk9YhhW3CGYqs3B';


        foreach ($jobs as $job) {

            $address=$job->address_1.' '.$job->address_2;

            $city=$job->city;

            $state=$job->state;

            $zipcode=$job->zip;
            $job->search_status='done';
            $job->save();


            $res=null;
            $url_con="https://api.estated.com/property/v3?token=$token&address=$address&city=$city&state=$state&zipcode=$zipcode";
            $http = new Http_Client();

            $response = $http->get($url_con);
            $response_data = $response->getBody(); 
            $res=json_decode($response_data);

            if (!isset($res->properties)){
                $job->search_status='No Address Found';
                $job->save();

                $note = New Note();
                $now = Carbon::now();
                $note->note_text = 'No Address Found';
                $note->entered_at = $now->toDateTimeString();
                $note->entered_by = 1;
                $note->viewable = 0;
                $note->noteable_type = 'App\Job';
                $note->client_id=$job->client->id;
                $note = $job->notes()->save($note);
                continue;

            }
            if (count($res->properties)>1){
                $search_api=JobAddressSearchAPI::Create();
                $search_api->job_id=$job->id;
                $address_sent='{"address":'.'"'.$address.'","city":'.'"'.$city.'","state":'.'"'.$state.'","zipcode":'.'"'.$zipcode.'"}';

                $search_api->address_sent=$address_sent;
                $search_api->json_response=$response_data;
                $search_api->save();
                continue;
            }

            $api_property=$res->properties[0];

            if (!isset($api_property->owners)){
                $job->search_status='No Address Found';
                $job->save();

                $note = New Note();
                $now = Carbon::now();
                $note->note_text = 'No Address Found';
                $note->entered_at = $now->toDateTimeString();
                $note->entered_by = 1;
                $note->viewable = 0;
                $note->noteable_type = 'App\Job';
                $note->client_id=$job->client->id;
                $note = $job->notes()->save($note);

                continue;

            }

            

            $owners=$api_property->owners;
            $legal=$api_property->legal;

             
            if (strpos($job->folio_number,$legal->apn_original)===false){
                 if ($job->folio_number) $job->folio_number=$job->folio_number.'/';
                 $job->folio_number=$job->folio_number. $legal->apn_original; 
            }
           

            if (strpos($job->legal_description,$legal->legal_description)===false){
                $job->legal_description=$job->legal_description."\n". $legal->legal_description;
            }

            $job->save();
            
            $estate_addresses=$api_property->addresses;
            if (isset($estate_addresses[0]->zip_code)){
              $populated_zip=$estate_addresses[0]->zip_code;
            }else{
              $populated_zip="";
            }
            if ($job->zip=="" || !$job->zip){
              $job->zip=$populated_zip;
              $job->save();
            } 

            $note = New Note();
            $now = Carbon::now();
            $note_text="Matching Address:";
            foreach ($estate_addresses as $estate_address) {
                $note_text=$note_text.$estate_address->formatted_street_address." ".$estate_address->city." ".$estate_address->state." ".$estate_address->zip_code."\n";
            }
            $note->note_text = $note_text;
            $note->entered_at = $now->toDateTimeString();
            $note->entered_by = 1;
            $note->viewable = 0;
            $note->noteable_type = 'App\Job';
            $note->client_id=$job->client->id;
            $note = $job->notes()->save($note);





            $sales=$api_property->sales;

            $deed_book='';

            $deed_page='';
            $recent_date='1900-00-00';
            foreach ($sales as $sale) {
                if ($sale->date && $sale->date>$recent_date){
                    $recent_date=$sale->date;
                    if ($sale->deed_book) $deed_book=$sale->deed_book;
                    if ($sale->deed_page) $deed_page=$sale->deed_page;
                }
            }

            ////////////////////////////////////////////// 
            for ($i=0;$i<count($owners);$i++){
               
              if ($owners[$i]->address || !$owners[$i]->last_name) continue;
              for ($j=0;$j<count($owners);$j++){
                if($i==$j || !$owners[$j]->address) continue;
                if($owners[$i]->last_name==$owners[$j]->last_name){
                  $owners[$i]->address=$owners[$j]->address;
                  $owners[$i]->address2=$owners[$j]->address2;
                  $owners[$i]->city=$owners[$j]->city;
                  $owners[$i]->state=$owners[$j]->state;
                  $owners[$i]->zip_code=$owners[$j]->zip_code;
                  break;
                }
              }
            }

            for ($i=0;$i<count($owners);$i++){
              if (!$owners[$i]->address) continue;
              for ($j=$i+1;$j<count($owners);$j++){
                if($owners[$j]->name=='' || !$owners[$j]->address) continue;
                if ($owners[$i]->address==$owners[$j]->address){
                  $owners[$i]->name=$owners[$i]->name.' AND '.$owners[$j]->name;
                  $owners[$j]->name='';
                  $owners[$i]->first_name='';
                  $owners[$i]->last_name='';
                }
              }
            }
            /////////////////////////////////////////////////// 

            $contacts=$job->client->contacts;

            $lo=0;
            foreach ($owners as $owner) {

                if ($owner->ended_at || $owner->name=='') continue;
                $lo++;if($lo>10) break;
                $matched=false;

                foreach ($contacts as $contact) {

                    $entity_contact=Entity::where('id',$contact->entity_id)->first();


                    $first_name=strtoupper($owner->first_name);
                    $last_name=strtoupper($owner->last_name);
                    if (!$first_name) $first_name='';
                    if (!$last_name) $last_name='';

                    if ($entity_contact->firm_name==strtoupper($owner->name) && $contact->first_name==$first_name && $contact->last_name==$last_name && (substr($contact->address_1,0,5)==strtoupper(substr($owner->address,0,5)) || $contact->address_1==$owner->address) && $contact->city==strtoupper($owner->city) && $contact->zip==$owner->zip_code){

                           $entity_contact->latest_type='owner';

                           $entity_contact->save();

                           $landowners=$job->parties->where('contact_id',$contact->id)->where('type','landowner');

                           if (count($landowners)>0){

                                foreach ($landowners as $landowner)

                                {
                                    $landowner_deed_number='';
                                    if ($deed_book) $landowner_deed_number="Book: $deed_book";
                                    if ($landowner_deed_number) $landowner_deed_number=$landowner_deed_number." and ";    
                                    if ($deed_page) $landowner_deed_number=$landowner_deed_number." Page: $deed_page";
                                    $landowner->landowner_deed_number=$landowner_deed_number;

                                }

                                

                           }else{

                                $data['entity_id'] = $entity_contact->id;

                                $data['contact_id'] = $contact->id;

                                $data['type'] = 'landowner';

                                $data['job_id'] = $job->id;

                                $landowner_deed_number='';
                                    if ($deed_book) $landowner_deed_number="Book: $deed_book";
                                    if ($landowner_deed_number) $landowner_deed_number=$landowner_deed_number." and ";    
                                    if ($deed_page) $landowner_deed_number=$landowner_deed_number." Page: $deed_page";

                                $data['landowner_deed_number']=$landowner_deed_number;

                                $newJobParty = JobParty::create($data);





                           }

                        $matched=true;   

                        break;

                    }

                }

                if (!$matched){

                    $data['firm_name']=strtoupper($owner->name);

                    $data['latest_type']='owner';

                    $data['client_id']=$job->client_id;

                    $data['is_hot']=0;

                    $data['hot_id']=0;



                    $entity = Entity::create($data);



                    $xdata['first_name'] = strtoupper($owner->first_name);

                    $xdata['last_name'] = strtoupper($owner->last_name);

                    if (!$owner->first_name) $xdata['first_name'] ='';
                    if (!$owner->last_name) $xdata['last_name'] ='';
                    if (isset($owner->gender)) $xdata['gender'] = $owner->gender; else $xdata['gender']='none';

                    $xdata['address_1'] = strtoupper($owner->address);

                    $xdata['address_2'] = strtoupper($owner->address2);

                    $xdata['city'] = strtoupper($owner->city);

                    $xdata['state'] = strtoupper($owner->state);

                    $xdata['zip'] = $owner->zip_code;

                    //$xdata['phone'] = $owner->phone;
                    $xdata['country'] = 'USA';


                    $new_contact = ContactInfo::create($xdata);

                    $new_contact->entity_id = $entity->id;

                    $new_contact->primary = 1;

                    $new_contact->save();



                                $xdata['entity_id'] = $entity->id;

                                $xdata['contact_id'] = $new_contact->id;

                                $xdata['type'] = 'landowner';

                                $xdata['job_id'] = $job->id;

                                $landowner_deed_number='';
                                    if ($deed_book) $landowner_deed_number="Book: $deed_book";
                                    if ($landowner_deed_number) $landowner_deed_number=$landowner_deed_number." and ";    
                                    if ($deed_page) $landowner_deed_number=$landowner_deed_number." Page: $deed_page";

                                $xdata['landowner_deed_number']=$landowner_deed_number.' Date:'.$recent_date;

                                $newJobParty = JobParty::create($xdata);

                }

            }

            $job->search_status='done';
            $job->save();

        }    

        

    }



}

