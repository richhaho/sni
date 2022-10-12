<?php

use App\Attachment;
use App\BatchDetail;
use App\WorkOrder;
use Illuminate\Database\Seeder;

class AddClientBatchDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $batchdetails = BatchDetail::all();

        foreach ($batchdetails as $bd) {
            $attachment = Attachment::find($bd->attachment_id);
            if ($attachment) {
                $wo = WorkOrder::findOrFail($attachment->attachable_id);

                $client_id = $wo->job->client_id;
                $bd->client_id = $client_id;
                $bd->save();
            }
        }
    }
}
