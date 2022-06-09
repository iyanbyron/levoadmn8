<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class BetOrders extends Model
{
    public $timestamps = true;
    protected $table = 'bet_orders';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order',
        'bet',
        'game_name',
        'issue',
        'create_time',
        'win_numbers',
        'play',
        'multiple',
        'odds',
        'single_money',
        'bet_number',
        'win_money',
        'bet_money',
        'win_rebate',
        'personal_profit_and_loss',
        'type',
        'username',
        'origin',
        'remark',
        'bet_type'

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
