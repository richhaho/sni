<?php

use Illuminate\Database\Seeder;
use App\Job;
use Faker\Factory as Faker;

class ChangeJobStatusDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
   public function run()
    {
         $job_statuses = [
            'notice-to-owner' => 'Notice to Owner',
            'release-of-lien' => 'Release of Lien',
            'demand-letter' => 'Demand Letter',
            'claim-of-lien' => 'Claim of Lien',
            'ammended-claim-of-lien' => 'Amended Claim of Lien',
            'notice-of-non-payment' => 'Notice of Non Payment',
            'partial-satisfaction-of-lien' => 'Partial Satisfaction of Lien',
            'satisfaction-of-lien' => 'Satisfaction of Lien'
       ];
        $jobs = Job::all();
        $faker = Faker::create();
        foreach($jobs as $job) {
          $job->status=$faker->randomElement($job_statuses);
          $job->save();
        }
    }
}
