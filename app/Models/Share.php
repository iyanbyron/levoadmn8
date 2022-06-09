<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Share extends Model
{
    protected $table = 'share';
    //protected $guard_name = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uid',
        'invi_code',
        'is_invitation',
        'is_exchange',
        'invi_uid',
        'vip_days',
        'invi_num'


    ];


}
