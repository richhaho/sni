<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\PriceList
 *
 * @property int $id
 * @property string $description
 * @property float $price
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PriceList whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PriceList whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PriceList whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PriceList wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PriceList whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PriceList extends Model
{
    protected $fillable = ['description','price'];
}
