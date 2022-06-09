<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class Games extends Model
{
    //  public $timestamps = false;
    /* const CREATED_AT = 'create_time';
     const UPDATED_AT = 'update_time';*/
    protected $table = 'games';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'game_name',
        'game_status',
        'start_time',
        'end_time',
        'color_limited_red',
        'single_bet_limit_red',
        'sort',
        'odds',
        'time_interval',
        'min_bet_money',
        'game_remark',
        'game_element',
        'front_play_menu',
        'split_time',
        'created_at',
        'updated_at'
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
    /*
    * 自动维护更新时间时间戳
    */
    /*public function fromDateTime($value)
    {
        return strtotime(parent::fromDateTime($value));
    }*/

}
