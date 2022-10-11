<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Events\WorkOrderRecipientSaved;

/**
 * App\WorkOrderRecipient
 *
 * @property int $id
 * @property int $party_id
 * @property int $work_order_id
 * @property string|null $party_type
 * @property string|null $firm_name
 * @property string|null $attention_name
 * @property string|null $address
 * @property string|null $mailing_type
 * @property string|null $barcode
 * @property int $return_receipt
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property float $postage
 * @property float $fee
 * @property float $other
 * @property float $total
 * @property-read \App\JobParty $party
 * @property-read \App\WorkOrder $work_order
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderRecipient whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderRecipient whereAttentionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderRecipient whereBarcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderRecipient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderRecipient whereFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderRecipient whereFirmName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderRecipient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderRecipient whereMailingType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderRecipient whereOther($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderRecipient wherePartyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderRecipient wherePartyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderRecipient wherePostage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderRecipient whereReturnReceipt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderRecipient whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderRecipient whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderRecipient whereWorkOrderId($value)
 * @mixin \Eloquent
 */
class WorkOrderRecipient extends Model
{
    protected $events = [
        'saving' => WorkOrderRecipientSaved::class,
    ];
    public function party()
    {
        return $this->belongsTo('App\JobParty')->withTrashed();
    }
    
    public function work_order()
    {
        return $this->belongsTo('App\WorkOrder','work_order_id');
    }
}
