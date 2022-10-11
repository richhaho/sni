<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\JobParty
 *
 * @property int $id
 * @property int $job_id
 * @property string|null $type
 * @property int $entity_id
 * @property int $contact_id
 * @property string|null $bond_pdf
 * @property string|null $bond_pdf_filename
 * @property string|null $bond_pdf_filename_mime
 * @property int $bond_pdf_filename_size
 * @property string|null $bond_date
 * @property float|null $bond_amount
 * @property string|null $bond_bookpage_number
 * @property string|null $bond_type
 * @property string|null $landowner_deed_number
 * @property int $landowner_lien_prohibition
 * @property string|null $leaseholder_type
 * @property string|null $leaseholder_lease_agreement
 * @property string|null $leaseholder_lease_number
 * @property string|null $leaseholder_bookpage_number
 * @property string|null $copy_type
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $hot_id
 * @property-read \App\ContactInfo $contact
 * @property-read \App\Entity $firm
 * @property-read \App\Job $job
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobParty ofType($type)
 * @method static \Illuminate\Database\Query\Builder|\App\JobParty onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobParty whereBondAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobParty whereBondBookpageNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobParty whereBondDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobParty whereBondPdf($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobParty whereBondPdfFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobParty whereBondPdfFilenameMime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobParty whereBondPdfFilenameSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobParty whereBondType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobParty whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobParty whereCopyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobParty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobParty whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobParty whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobParty whereHotId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobParty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobParty whereJobId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobParty whereLandownerDeedNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobParty whereLandownerLienProhibition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobParty whereLeaseholderBookpageNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobParty whereLeaseholderLeaseAgreement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobParty whereLeaseholderLeaseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobParty whereLeaseholderType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobParty whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobParty whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\JobParty withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\JobParty withoutTrashed()
 * @mixin \Eloquent
 */
class JobParty extends Model
{
    //
    use SoftDeletes;
    
    protected $fillable = ['job_id','type', 'entity_id','contact_id','bond_type','bond_amount','bond_date','bond_bookpage_number','landowner_deed_number','landowner_lien_prohibition','leaseholder_type','leaseholder_lease_agreement','leaseholder_lease_number','leaseholder_bookpage_number','copy_type', 'source'];
    
    protected $attributes = [
        'source' => 'CL',
    ];

    public function contact() {
        return $this->belongsTo('App\ContactInfo')->withTrashed();
    }
    
    public function firm() {
        return $this->belongsTo('App\Entity','entity_id');
    }
    
    public function job() {
        return $this->belongsTo('App\Job');
    }
    
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type)->whereNull('deleted_at');
    }
    
}
