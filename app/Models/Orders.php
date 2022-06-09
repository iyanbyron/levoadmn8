<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class  Orders extends Model
{
    public $timestamps = true;
    protected $table = 'orders';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'amount',
        'remarks',
        'created_at',
        'updated_at',
        'admin_id',
        'after_money',
        'username',
        'admin_name',
        'order_type',
        'bank_id',
        'recharge_num',
        'order_num',
        'oreder_type'
    ];

    /**
     * 格式化TZ日期格式
     * @param DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');//年月日格式
        //return $date->format($this->dateFormat ?: 'U');//默认时间戳格式
    }

}
