<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\TemplateLine
 *
 * @property int $id
 * @property int $template_id
 * @property string $description
 * @property int $quantity
 * @property float $price
 * @property string|null $status
 * @property string $type
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property mixed $recipient_id
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TemplateLine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TemplateLine whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TemplateLine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TemplateLine wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TemplateLine whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TemplateLine whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TemplateLine whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TemplateLine whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TemplateLine whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TemplateLine extends Model
{
    public $recipient_id = 0;

    public function getRecipientIdAttribute()
    {
        return $recipient_id;
    }

    public function setRecipientIdAttribute($value)
    {
        $recipient_id = $value;
    }
}
