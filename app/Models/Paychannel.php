<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Paychannel extends Model
{
    protected $table = 'pay_channel';
    protected $guard_name = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'chann_title',
        'chann_id',
        'pay_title',
        'pay_type',
        'pay_img',
        'is_open',
        'mch_id',
        'appid',
        'key',
        'notify_url',
        'redirect_url',
        'submit_url',
        'pay_is_rend',
        'pay_prname'
    ];

}
