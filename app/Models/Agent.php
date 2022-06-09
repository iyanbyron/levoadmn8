<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    protected $table = 'agent_paytotal';
    //protected $guard_name = 'admin';
    // protected $dateFormat ='U';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        'admin_id',
        'all_total_income',
        'agent_total_income',
        'total_deduct_amount',
        'total_deduct_order_num',
        'total_order_num',
        'total_install_num',
        'total_user_num',
        'agent_real_income',

    ];


    public function getUpdatedAtAttribute($value)
    {
        return $value ? date("Y-m-d", strtotime($value)) : '';
    }

    public function getCreatedAtAttribute($value)
    {
        return $value ? date("Y-m-d", strtotime($value)) : '';
    }

    /*public function setStartTimeAttribute($value)
    {
        $this->attributes['start_time'] = is_int($value) ? $value : strtotime($value);
    }

    public function getStartTimeAttribute()
    {
        return date('Y-m-d H:i:s', $this->attributes['start_time']);
    }*/
}
