<?php

namespace App\Listeners;

use App\Events\WorkOrderRecipientSaved;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ModifyTotalRecipient
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  WorkOrderRecipientSaved  $event
     * @return void
     */
    public function handle(WorkOrderRecipientSaved $event)
    {
        
        $event->recipient->total = $event->recipient->postage + $event->recipient->fee + $event->recipient->other;
       
    }
}
