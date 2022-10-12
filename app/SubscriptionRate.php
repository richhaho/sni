<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriptionRate extends Model
{
    protected $table = 'subscription_rates';

    protected $fillable = ['self_30day_rate', 'self_365day_rate', 'full_30day_rate', 'full_365day_rate'];
}
