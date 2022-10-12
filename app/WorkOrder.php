<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\WorkOrder
 *
 * @property int $id
 * @property int $job_id
 * @property string|null $status
 * @property string|null $type
 * @property \Carbon\Carbon|null $due_at
 * @property \Carbon\Carbon|null $mailing_at
 * @property int $is_rush
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Attachment[] $attachments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Client[] $client
 * @property-read mixed $number
 * @property-read mixed $paid
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Invoice[] $invoices
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Invoice[] $invoicesPaid
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Invoice[] $invoicesPending
 * @property-read \App\Job $job
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Note[] $notes
 * @property-read \App\WorkOrderType|null $order_type
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\PdfPage[] $pdf_pages
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\WorkOrderRecipient[] $recipients
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Invoice[] $wizardInvoices
 *
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\WorkOrder onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrder whereDueAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrder whereIsRush($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrder whereJobId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrder whereMailingAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrder whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\WorkOrder withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\WorkOrder withoutTrashed()
 * @mixin \Eloquent
 */
class WorkOrder extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'job_id', 'status', 'type', 'due_at', 'is_rush', 'mailing_at', 'responsible_user', 'manager', 'researcher',
    ];

    protected $dates = ['deleted_at', 'due_at', 'mailing_at'];

    use SoftDeletes;

    public function job()
    {
        return $this->belongsTo('App\Job')->withTrashed();
    }

    public function attachments()
    {
        return $this->morphMany('App\Attachment', 'attachable');
    }

    public function getNumberAttribute()
    {
        return sprintf('%08d', $this->id);
    }

    public function client()
    {
        return $this->hasManyThrough('App\Client', 'App\Job', 'client_id', 'id', 'job_id')->withTrashed();
    }

    public function notes()
    {
        return $this->morphMany('App\Note', 'noteable');
    }

    public function invoices()
    {
        return $this->hasMany('App\Invoice');
    }

    public function invoicesPending()
    {
        return $this->hasMany('App\Invoice')->whereNull('payed_at');
    }

    public function invoicesPaid()
    {
        return $this->hasMany('App\Invoice')->whereNotNull('payed_at');
    }

    public function wizardInvoices()
    {
        return $this->hasMany('App\Invoice')->wizard();
    }

    public function pdf_pages()
    {
        return $this->hasMany('App\PdfPage');
    }

    public function recipients()
    {
        return $this->hasMany('App\WorkOrderRecipient');
    }

    public function order_type()
    {
        return $this->belongsTo('App\WorkOrderType', 'type', 'slug')->withTrashed();
    }

    public function getPaidAttribute()
    {
        $unpaid_i = count($this->invoices()->where('status', 'unpaid')->get());
        $open_i = count($this->invoices()->where('status', 'open')->get());
        if ($unpaid_i + $open_i > 0) {
            return false;
        }

        return true;
    }

    public function researcherUser()
    {
        $researcher = User::where('id', $this->researcher)->first();

        return $researcher;
    }

    public function todos()
    {
        return Todo::where('workorder_id', $this->id)->where('status', '!=', 'pending')->get();
    }

    public function unpaidTodos()
    {
        return Todo::where('workorder_id', $this->id)->where('status', 'pending')->orderBy('created_at', 'desc')->get();
    }

    public function unpaidLastTodo($todo_name)
    {
        return Todo::where('workorder_id', $this->id)->where('status', 'pending')->where('name', $todo_name)->orderBy('created_at', 'desc')->first();
    }

    public function incompleteTodos()
    {
        return Todo::where('workorder_id', $this->id)->where('status', '!=', 'pending')->where('status', '!=', 'completed')->get();
    }
}
