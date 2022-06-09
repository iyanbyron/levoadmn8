<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    //protected $guard_name = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_code',
        'amount',
        'payment_method',
        'pay_status',
        'pay_time',
        'days',
        'product_type',
        'gold_num',
        'admin_id',
        'auth_type',
        'product_id',
        'uid'

    ];


}
