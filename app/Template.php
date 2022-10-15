<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Template
 *
 * @property int $id
 * @property string $type_slug
 * @property int $enabled
 * @property int $client_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\TemplateLine[] $lines
 * @property-read \App\WorkOrderType $type
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Template defaults()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Template whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Template whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Template whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Template whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Template whereTypeSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Template whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Template extends Model
{
    public static function boot()
    {
        parent::boot();

        // Attach event handler, on deleting of the user
        Template::deleting(function ($template) {
            // Delete all tricks that belong to this user
            foreach ($template->lines as $line) {
                $line->delete();
            }
        });
    }

    public function scopeDefaults($query)
    {
        return $query->where('client_id', 0);
    }

    public function type()
    {
        return $this->belongsTo(\App\WorkOrderType::class, 'type_slug', 'slug');
    }

    public function lines()
    {
        return $this->hasMany(\App\TemplateLine::class);
    }

    public function clientName()
    {
        if ($this->client_id == 0) {
            return 'Default';
        }
        $client = Client::where('id', $this->client_id)->first();

        return $client ? $client->company_name : 'Default';
    }
}
