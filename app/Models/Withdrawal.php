<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class  Withdrawal extends Model
{
    public $timestamps = true;
    protected $table = 'withdrawal';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'bank_name',
        'subbranch_name',
        'bank_card_number',
        'account_name',
        'withdraw_order',
        'amount',
        'status',
        'username',
        'operator',
        'finish_time',
        'admin_id',
        'created_at',
        'updated_at',
        'type',
        'remark'
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
