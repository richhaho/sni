<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPayment extends Model
{
    protected $table = 'subscription_payments';

    protected $fillable = ['type', 'amount', 'client_id', 'reference', 'gateway', 'transaction_status', 'log_result', 'user_id', 'service_type', 'subscription_period', 'subscription_rate', 'expiration'];
}
